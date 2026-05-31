<?php

namespace App\Services;

use App\Jobs\SyncViewTrends;
use App\Models\ViewTracking;
use App\Models\ViewTrend;
use Carbon\CarbonPeriod;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class TrendService
{
    public function __construct(private ViewTrackingService $viewTrackingService) {}

    public function getTrendsForRange(int $days): array
    {
        $start = today()->subDays($days - 1);
        $end = today();

        return $this->getTrendsForDates($start, $end);
    }

    public function getTrendsForDates(Carbon $start, Carbon $end): array
    {
        $cacheKey = $this->cacheKey($start, $end);

        return Cache::remember($cacheKey, 300, function () use ($start, $end) {
            $this->dispatchSyncJobIfNeeded($start, $end);

            return $this->buildTrendPayload($start, $end);
        });
    }

    private function cacheKey(Carbon $start, Carbon $end): string
    {
        return sprintf('admin.trends.%s.%s', $start->toDateString(), $end->toDateString());
    }

    private function dispatchSyncJobIfNeeded(Carbon $start, Carbon $end): void
    {
        $existingDates = ViewTrend::whereBetween('date', [$start->toDateString(), $end->toDateString()])
            ->pluck('date')
            ->map(fn ($date) => Carbon::parse($date)->toDateString())
            ->all();

        $expectedCount = CarbonPeriod::create($start, $end)->count();

        if (count($existingDates) < $expectedCount) {
            SyncViewTrends::dispatch($start->toDateString(), $end->toDateString());
        }
    }

    private function buildTrendPayload(Carbon $start, Carbon $end): array
    {
        $trends = ViewTrend::whereBetween('date', [$start->toDateString(), $end->toDateString()])
            ->orderBy('date')
            ->get()
            ->keyBy(fn (ViewTrend $trend) => $trend->date->toDateString());

        $labels = [];
        $totalViews = [];
        $uniqueVisitors = [];
        $bookmarksAdded = [];

        foreach (CarbonPeriod::create($start, $end) as $date) {
            $dateKey = $date->toDateString();
            $trend = $trends->get($dateKey);

            $labels[] = $date->format('M j');
            $totalViews[] = $trend?->total_views ?? 0;
            $uniqueVisitors[] = $trend?->unique_visitors ?? 0;
            $bookmarksAdded[] = $trend?->bookmarks_added ?? 0;
        }

        return [
            'labels' => $labels,
            'datasets' => [
                'total_views' => $totalViews,
                'unique_visitors' => $uniqueVisitors,
                'bookmarks_added' => $bookmarksAdded,
            ],
        ];
    }

    public function syncRange(Carbon $start, Carbon $end): void
    {
        $existingDates = ViewTrend::whereBetween('date', [$start->toDateString(), $end->toDateString()])
            ->pluck('date')
            ->map(fn ($date) => Carbon::parse($date)->toDateString())
            ->all();

        foreach (CarbonPeriod::create($start, $end) as $date) {
            $date = Carbon::parse($date);

            if ($date->isToday() || ! in_array($date->toDateString(), $existingDates, true)) {
                $this->syncDate($date);
            }
        }
    }

    private function syncDate(Carbon $date): void
    {
        $dateKey = $date->toDateString();
        $uniqueVisitorSql = $this->viewTrackingService->uniqueVisitorSql();

        $totalViews = ViewTracking::whereDate('viewed_at', $dateKey)->count();
        $uniqueVisitors = ViewTracking::whereDate('viewed_at', $dateKey)
            ->selectRaw("COUNT(DISTINCT {$uniqueVisitorSql}) as unique_count")
            ->value('unique_count') ?? 0;
        $bookmarksAdded = DB::table('comic_user_bookmarks')->whereDate('created_at', $dateKey)->count()
            + DB::table('chapter_user_bookmarks')->whereDate('created_at', $dateKey)->count();

        ViewTrend::updateOrCreate(
            ['date' => $dateKey],
            [
                'total_views' => $totalViews,
                'unique_visitors' => $uniqueVisitors,
                'bookmarks_added' => $bookmarksAdded,
            ]
        );
    }
}
