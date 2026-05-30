<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\PaginationCache;
use App\Http\Controllers\Controller;
use App\Models\Chapter;
use App\Models\Comic;
use App\Models\User;
use App\Services\ViewTrackingService;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class DashboardController extends Controller
{
    protected ViewTrackingService $viewTrackingService;

    public function __construct(ViewTrackingService $viewTrackingService)
    {
        $this->viewTrackingService = $viewTrackingService;
    }

    public function index(Request $request): View
    {
        $this->authorizeAccess();

        // Cache overview statistics for better performance
        $stats = Cache::remember('admin.dashboard.stats', 300, function () {
            $siteStats = $this->viewTrackingService->getSiteViewStats();

            return [
                'total_comics' => Comic::count(),
                'total_chapters' => Chapter::count(),
                'total_users' => User::count(),
                'total_comic_views' => $siteStats['total_comic_views'],
                'total_chapter_views' => $siteStats['total_chapter_views'],
                'total_views' => $siteStats['total_views'],
                'unique_comic_views' => $siteStats['unique_comic_views'],
                'unique_chapter_views' => $siteStats['unique_chapter_views'],
                'unique_visitors_today' => $siteStats['unique_visitors_today'],
                'storage_usage' => $this->getStorageUsage(),
            ];
        });

        // Recent activity data
        $recentComics = Comic::with('user')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $recentChapters = Chapter::with(['comic', 'user'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $recentUsers = User::orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Analytics data
        $analyticsFilter = $request->get('filter', 'most_viewed');
        $comicAnalytics = $this->getComicAnalytics($analyticsFilter);

        return view('admin.dashboard.index', compact(
            'stats',
            'recentComics',
            'recentChapters',
            'recentUsers',
            'comicAnalytics',
            'analyticsFilter'
        ));
    }

    public function comicAnalytics(Request $request): View
    {
        $this->authorizeAccess();

        $filter = $request->get('filter', 'most_viewed');
        $comicAnalytics = $this->getComicAnalytics($filter, true);
        $analyticsSummary = $this->getAnalyticsSummary($filter);

        return view('admin.dashboard.analytics', compact('comicAnalytics', 'analyticsSummary', 'filter'));
    }

    private function getComicAnalytics(string $filter, bool $detailed = false)
    {
        $query = $this->buildComicAnalyticsQuery($filter);

        if ($detailed) {
            $page = request()->integer('page', 1);
            $comics = PaginationCache::remember('admin.analytics.comics', $filter, $page, 300, function () use ($query) {
                return $query->paginate(10);
            });
        } else {
            $comics = $query->limit(10)->get();
        }

        $this->attachUniqueViewCounts($detailed ? $comics->getCollection() : $comics);

        return $comics;
    }

    private function buildComicAnalyticsQuery(string $filter)
    {
        $query = Comic::with([
            'chapters' => function ($query) {
                $query->select('id', 'comic_id', 'name', 'number', 'created_at', 'views_count');
            },
            'bookmarkedBy' => function ($query) {
                $query->select('users.id');
            },
            'user' => function ($query) {
                $query->select('id', 'name');
            },
        ])->withCount(['chapters', 'bookmarkedBy']);

        // Apply filtering and ordering based on real view data
        switch ($filter) {
            case 'most_viewed':
                $query->orderByDesc('views_count');
                break;
            case 'recently_updated':
                $query->orderBy('latest_update', 'desc');
                break;
            case 'most_bookmarked':
                $query->withCount('bookmarkedBy')->orderBy('bookmarked_by_count', 'desc');
                break;
            case 'newest':
                $query->orderBy('created_at', 'desc');
                break;
            default:
                $query->orderByDesc('views_count');
        }

        return $query;
    }

    private function attachUniqueViewCounts(Collection $comics): void
    {
        $comicIds = $comics->pluck('id')->all();
        $chapterIds = $comics->flatMap(fn ($comic) => $comic->chapters->pluck('id'))->all();

        $comicUniqueViews = $this->viewTrackingService->getMultipleComicUniqueViewCounts($comicIds);
        $chapterUniqueViews = $this->viewTrackingService->getMultipleChapterUniqueViewCounts($chapterIds);

        foreach ($comics as $comic) {
            $comic->unique_views_count = $comicUniqueViews[$comic->id] ?? 0;

            foreach ($comic->chapters as $chapter) {
                $chapter->unique_views_count = $chapterUniqueViews[$chapter->id] ?? 0;
            }
        }
    }

    private function getAnalyticsSummary(string $filter): array
    {
        return PaginationCache::rememberTotals('admin.analytics.summary', 'all', 300, function () {
            return [
                'total_comics' => Comic::count(),
                'total_chapters' => Chapter::count(),
                'total_bookmarks' => DB::table('comic_user_bookmarks')->count(),
                'unique_comic_views' => $this->viewTrackingService->getUniqueComicViewsTotal(),
                'total_views' => Comic::sum('views_count') + Chapter::sum('views_count'),
            ];
        });
    }

    private function getStorageUsage(): array
    {
        return Cache::remember('admin.storage_usage', 3600, function () {
            try {
                // Get storage usage from R2 disk with timeout protection
                $totalSize = 0;
                $fileCount = 0;

                $comicsPath = 'comics';

                // Use a simple count instead of iterating all files to avoid timeout
                if (Storage::disk('r2')->exists($comicsPath)) {
                    try {
                        $files = Storage::disk('r2')->files($comicsPath);
                        $fileCount = count($files);

                        // Only calculate size for first 100 files to prevent timeout
                        $limit = min(100, count($files));
                        for ($i = 0; $i < $limit; $i++) {
                            try {
                                $totalSize += Storage::disk('r2')->size($files[$i]);
                            } catch (\Exception $e) {
                                // Skip files that fail to size
                                continue;
                            }
                        }

                        // Estimate total size if we have more files
                        if ($fileCount > 100) {
                            $avgSize = $totalSize / $limit;
                            $totalSize = $avgSize * $fileCount;
                        }
                    } catch (\Exception $e) {
                        // If listing files fails, return N/A
                        return [
                            'total_size' => 'N/A',
                            'file_count' => 'N/A',
                        ];
                    }
                }

                return [
                    'total_size' => $this->formatBytes($totalSize),
                    'file_count' => $fileCount,
                ];
            } catch (\Exception $e) {
                return [
                    'total_size' => 'N/A',
                    'file_count' => 'N/A',
                ];
            }
        });
    }

    private function formatBytes($bytes, $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, $precision).' '.$units[$i];
    }

    public function contentModeration(): View
    {
        $this->authorizeAccess();

        // Get recent content changes
        $recentComics = Comic::with('user')
            ->orderBy('updated_at', 'desc')
            ->limit(10)
            ->get();

        $recentChapters = Chapter::with(['comic', 'user'])
            ->orderBy('updated_at', 'desc')
            ->limit(10)
            ->get();

        // Get flagged content (placeholder - would need actual flagging system)
        $flaggedComments = collect(); // Would implement if comment system exists
        $reportedComics = collect(); // Would implement if reporting system exists

        return view('admin.dashboard.content-moderation', compact(
            'recentComics',
            'recentChapters',
            'flaggedComments',
            'reportedComics'
        ));
    }

    public function systemHealth(): View
    {
        $this->authorizeAccess();

        $systemInfo = [
            'app_env' => config('app.env'),
            'app_debug' => config('app.debug') ? 'ON' : 'OFF',
            'cache_driver' => config('cache.default'),
            'session_driver' => config('session.driver'),
            'queue_driver' => config('queue.default'),
            'laravel_version' => app()->version(),
            'php_version' => PHP_VERSION,
            'https_status' => request()->secure() ? 'HTTPS' : 'HTTP',
            'storage_status' => $this->checkStorageHealth(),
            'database_status' => $this->checkDatabaseHealth(),
        ];

        return view('admin.dashboard.system-health', compact('systemInfo'));
    }

    private function checkStorageHealth(): array
    {
        try {
            Storage::disk('r2')->put('health-check.txt', 'test');
            Storage::disk('r2')->delete('health-check.txt');

            return ['status' => 'healthy', 'message' => 'Storage is accessible'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    private function checkDatabaseHealth(): array
    {
        try {
            DB::select('SELECT 1');

            return ['status' => 'healthy', 'message' => 'Database connection is working'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    protected function authorizeAccess()
    {
        if (! auth()->check() || ! auth()->user()->isSuperAdmin()) {
            abort(403, 'Unauthorized access');
        }
    }
}
