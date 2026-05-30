<?php

namespace App\Services;

use App\Models\ViewTracking;
use App\Models\ViewTrend;
use Carbon\CarbonPeriod;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class TrendService
{
    public function __construct(private ViewTrackingService $viewTrackingService) {}

    public function getSevenDayTrends(): array
    {
        return Cache::remember('admin.trends.seven_day', 300, function () {
            $start = today()->subDays(6);
            $end = today();

            $this->syncRange($start, $end);

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
        });
    }

    private function syncRange(Carbon $start, Carbon $end): void
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
