<?php

namespace App\Services;

use App\Models\Chapter;
use App\Models\Comic;
use App\Models\ViewTracking;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;

class ViewTrackingService
{
    /**
     * Cooldown period in seconds to prevent refresh spam.
     * 30 seconds cooldown as specified.
     */
    private const COOLDOWN_SECONDS = 30;

    /**
     * Cache key prefix for view tracking.
     */
    private const CACHE_PREFIX = 'view_tracking_';

    /**
     * Track a comic view.
     *
     * This method handles both logged-in and guest users,
     * prevents refresh spam using cooldown logic,
     * and updates view counts efficiently.
     *
     * @return bool Whether the view was tracked
     */
    public function trackComicView(Comic $comic): bool
    {
        return $this->trackView('comic', $comic->id, $comic);
    }

    /**
     * Track a chapter view.
     *
     * This method handles both logged-in and guest users,
     * prevents refresh spam using cooldown logic,
     * and updates view counts efficiently.
     *
     * @return bool Whether the view was tracked
     */
    public function trackChapterView(Chapter $chapter): bool
    {
        return $this->trackView('chapter', $chapter->id, $chapter);
    }

    /**
     * Core view tracking logic.
     *
     * This method implements the main tracking algorithm:
     * 1. Check cooldown to prevent refresh spam
     * 2. Track view for both logged-in and guest users
     * 3. Update view counts asynchronously for performance
     * 4. Use cache for cooldown management
     *
     * @param  string  $type  'comic' or 'chapter'
     * @param  mixed  $model
     * @return bool Whether the view was tracked
     */
    private function trackView(string $type, int $id, $model): bool
    {
        // Generate unique identifier for this visitor + content combination
        $visitorId = $this->getVisitorId();
        $cacheKey = self::CACHE_PREFIX."{$type}_{$visitorId}_{$id}";

        // Check cooldown period to prevent refresh spam
        if ($this->isInCooldown($cacheKey)) {
            return false;
        }

        // Set cooldown cache
        Cache::put($cacheKey, true, self::COOLDOWN_SECONDS);

        // Track the view in database (async for performance)
        $this->createViewTrackingRecord($type, $id, $model);

        // Update view count (atomic operation)
        $this->incrementViewCount($model);

        return true;
    }

    /**
     * Generate a unique visitor identifier.
     *
     * Combines multiple factors to create a reliable identifier:
     * - User ID if logged in (most reliable)
     * - IP address (for guests)
     * - User agent (to differentiate different browsers on same IP)
     *
     * @return string Unique visitor identifier
     */
    private function getVisitorId(): string
    {
        $user = auth()->user();

        if ($user) {
            // For logged-in users, use user ID (most reliable)
            return 'user_'.$user->id;
        }

        // For guests, use IP + user agent combination
        $ip = Request::ip();
        $userAgent = Request::userAgent();
        $userAgentHash = substr(md5($userAgent), 0, 8); // Short hash for efficiency

        return 'guest_'.md5($ip.$userAgentHash);
    }

    /**
     * Check if the visitor is in cooldown period.
     *
     * Uses cache for efficient cooldown checking.
     * Cache automatically expires after cooldown period.
     *
     * @return bool Whether visitor is in cooldown
     */
    private function isInCooldown(string $cacheKey): bool
    {
        return Cache::has($cacheKey);
    }

    /**
     * Create a view tracking record in database.
     *
     * Stores detailed information for analytics:
     * - Which content was viewed
     * - Who viewed it (if logged in)
     * - IP address for guest tracking
     * - User agent for analytics
     * - When it was viewed
     */
    private function createViewTrackingRecord(string $type, int $id, $model = null): void
    {
        $data = [
            'user_id' => auth()->id(),
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
            'viewed_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ];

        if ($type === 'comic') {
            $data['comic_id'] = $id;
        } else {
            // chapter view: store chapter_id and, if available, the parent comic_id
            $data['chapter_id'] = $id;
            if ($model && isset($model->comic_id)) {
                $data['comic_id'] = $model->comic_id;
            }
        }

        // Use queue for better performance if available
        try {
            ViewTracking::create($data);
        } catch (\Exception $e) {
            // Log error but don't break the user experience
            \Log::error('View tracking failed: '.$e->getMessage());
        }
    }

    /**
     * Increment view count on the model.
     *
     * Uses atomic database operation to prevent race conditions.
     * Updates are done efficiently without loading the full model.
     *
     * @param  mixed  $model
     */
    private function incrementViewCount($model): void
    {
        try {
            // Use raw query for atomic increment
            if ($model instanceof Comic) {
                \DB::table('comics')
                    ->where('id', $model->id)
                    ->increment('views_count');
            } elseif ($model instanceof Chapter) {
                \DB::table('chapters')
                    ->where('id', $model->id)
                    ->increment('views_count');
            }
        } catch (\Exception $e) {
            // Log error but don't break the user experience
            \Log::error('View count increment failed: '.$e->getMessage());
        }
    }

    /**
     * Get total views for a comic.
     *
     * @return int Total view count
     */
    public function getComicViewCount(Comic $comic): int
    {
        return (int) $comic->views_count;
    }

    /**
     * Get total views for a chapter.
     *
     * @return int Total view count
     */
    public function getChapterViewCount(Chapter $chapter): int
    {
        return (int) $chapter->views_count;
    }

    /**
     * Get total views for all chapters in a comic.
     *
     * @return int Total chapter views
     */
    public function getComicChapterViewsTotal(Comic $comic): int
    {
        return Cache::remember(
            "comic_chapter_views_total_{$comic->id}",
            300, // 5 minutes cache
            function () use ($comic) {
                return $comic->chapters()->sum('views_count');
            }
        );
    }

    /**
     * Get most viewed chapter in a comic.
     *
     * @return Chapter|null Most viewed chapter
     */
    public function getMostViewedChapter(Comic $comic): ?Chapter
    {
        return Cache::remember(
            "comic_most_viewed_chapter_{$comic->id}",
            300, // 5 minutes cache
            function () use ($comic) {
                return $comic->chapters()
                    ->orderByDesc('views_count')
                    ->first();
            }
        );
    }

    /**
     * Get average chapter views for a comic.
     *
     * @return float Average chapter views
     */
    public function getAverageChapterViews(Comic $comic): float
    {
        $totalChapters = $comic->chapters()->count();

        if ($totalChapters === 0) {
            return 0;
        }

        $totalViews = $this->getComicChapterViewsTotal($comic);

        return round($totalViews / $totalChapters, 2);
    }

    /**
     * Get overall site statistics.
     *
     * @return array Site-wide view statistics
     */
    public function getSiteViewStats(): array
    {
        return Cache::remember('site_view_stats', 300, function () {
            $uniqueVisitorSql = $this->uniqueVisitorSql();

            return [
                'total_comic_views' => Comic::sum('views_count'),
                'total_chapter_views' => Chapter::sum('views_count'),
                'total_views' => Comic::sum('views_count') + Chapter::sum('views_count'),
                'unique_visitors_today' => ViewTracking::whereDate('viewed_at', today())
                    ->selectRaw("COUNT(DISTINCT ip_address) as unique_count")
                    ->value('unique_count') ?? 0,
                'unique_comic_views' => ViewTracking::forComics()
                    ->selectRaw("COUNT(DISTINCT {$uniqueVisitorSql}) as unique_count")
                    ->value('unique_count') ?? 0,
                'unique_chapter_views' => ViewTracking::forChapters()
                    ->selectRaw("COUNT(DISTINCT {$uniqueVisitorSql}) as unique_count")
                    ->value('unique_count') ?? 0,
            ];
        });
    }

    public function getUniqueComicViewsTotal(): int
    {
        return Cache::remember('unique_comic_views_total', 300, function () {
            $driver = DB::connection()->getDriverName();
            $uniqueVisitorSql = $this->uniqueVisitorSql();
            $comicVisitorSql = $driver === 'sqlite'
                ? "comic_id || ':' || {$uniqueVisitorSql}"
                : "CONCAT(comic_id, ':', {$uniqueVisitorSql})";

            return (int) (ViewTracking::forComics()
                ->selectRaw("COUNT(DISTINCT {$comicVisitorSql}) as unique_count")
                ->value('unique_count') ?? 0);
        });
    }

    /**
     * Get unique view count for a comic.
     * Counts unique visitors who viewed the comic itself (not chapters).
     * 
     * @param Comic $comic
     * @return int Number of unique users who viewed this comic
     */
    public function getComicUniqueViewCount(Comic $comic): int
    {
        return Cache::remember(
            "comic_unique_views_{$comic->id}",
            300, // 5 minutes cache
            function () use ($comic) {
                return $this->getMultipleComicUniqueViewCounts([$comic->id])[$comic->id] ?? 0;
            }
        );
    }

    /**
     * Get unique view counts for multiple comics in a single batch query.
     * Prevents N+1 queries when loading unique views for many comics.
     *
     * @param array $comicIds Array of comic IDs to fetch unique views for
     * @return array Keyed array: ['comic_id' => unique_count, ...]
     */
    public function getMultipleComicUniqueViewCounts(array $comicIds): array
    {
        if (empty($comicIds)) {
            return [];
        }
        $ids = $this->normalizeIds($comicIds);

        return Cache::remember(
            'comic_unique_views_batch_' . md5(json_encode($ids)),
            300, // 5 minutes cache
            function () use ($ids) {
                $results = ViewTracking::forComics()
                    ->whereIn('comic_id', $ids)
                    ->selectRaw('comic_id, COUNT(DISTINCT COALESCE(CAST(user_id AS CHAR), MD5(CONCAT(COALESCE(ip_address, \'\'), COALESCE(user_agent, \'\'))))) as unique_count')
                    ->groupBy('comic_id')
                    ->pluck('unique_count', 'comic_id')
                    ->toArray();

                // Ensure all comic IDs are present in result (fill missing with 0)
                $normalized = [];
                foreach ($ids as $id) {
                    $normalized[$id] = isset($results[$id]) ? (int)$results[$id] : 0;
                }

                return $normalized;
            }
        );
    }

    /**
     * Get unique view counts for multiple chapters in a single batch query.
     * Prevents N+1 queries when loading unique views for many chapters.
     *
     * @param array $chapterIds Array of chapter IDs to fetch unique views for
     * @return array Keyed array: ['chapter_id' => unique_count, ...]
     */
    public function getMultipleChapterUniqueViewCounts(array $chapterIds): array
    {
        if (empty($chapterIds)) {
            return [];
        }
        $ids = $this->normalizeIds($chapterIds);

        return Cache::remember(
            'chapter_unique_views_batch_' . md5(json_encode($ids)),
            300, // 5 minutes cache
            function () use ($ids) {
                $results = ViewTracking::forChapters()
                    ->whereIn('chapter_id', $ids)
                    ->selectRaw('chapter_id, COUNT(DISTINCT COALESCE(CAST(user_id AS CHAR), MD5(CONCAT(COALESCE(ip_address, \'\'), COALESCE(user_agent, \'\'))))) as unique_count')
                    ->groupBy('chapter_id')
                    ->pluck('unique_count', 'chapter_id')
                    ->toArray();

                // Ensure all chapter IDs are present in result (fill missing with 0)
                $normalized = [];
                foreach ($ids as $id) {
                    $normalized[$id] = isset($results[$id]) ? (int)$results[$id] : 0;
                }

                return $normalized;
            }
        );
    }

    /**
     * Get unique view count for a chapter.
     * Counts unique visitors who viewed that specific chapter.
     *
     * @return int Number of unique users who viewed this chapter
     */
    public function getChapterUniqueViewCount(Chapter $chapter): int
    {
        return Cache::remember(
            "chapter_unique_views_{$chapter->id}",
            300, // 5 minutes cache
            function () use ($chapter) {
                return $this->getMultipleChapterUniqueViewCounts([$chapter->id])[$chapter->id] ?? 0;
            }
        );
    }

    /**
     * Get unique chapter views for a comic.
     *
     * @return int Total unique views across all chapters
     */
    public function getComicUniqueChapterViewsTotal(Comic $comic): int
    {
        return Cache::remember(
            "comic_unique_chapter_views_total_{$comic->id}",
            300, // 5 minutes cache
            function () use ($comic) {
                $uniqueVisitorSql = $this->uniqueVisitorSql();

                return (int) (ViewTracking::forChapters()
                    ->where('comic_id', $comic->id)
                    ->selectRaw("COUNT(DISTINCT {$uniqueVisitorSql}) as unique_count")
                    ->value('unique_count') ?? 0);
            }
        );
    }

    public function uniqueVisitorSql(): string
    {
        $driver = DB::connection()->getDriverName();

        if ($driver === 'sqlite') {
            return "CASE WHEN user_id IS NOT NULL THEN 'user:' || user_id ELSE 'guest:' || COALESCE(ip_address, '') || ':' || COALESCE(user_agent, '') END";
        }

        return "CASE WHEN user_id IS NOT NULL THEN CONCAT('user:', user_id) ELSE CONCAT('guest:', COALESCE(ip_address, ''), ':', COALESCE(user_agent, '')) END";
    }

    private function normalizeIds(array $ids): array
    {
        $ids = array_unique(array_map('intval', $ids));
        sort($ids);

        return array_values(array_filter($ids));
    }
}
