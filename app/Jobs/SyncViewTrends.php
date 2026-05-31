<?php

namespace App\Jobs;

use App\Services\TrendService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;

class SyncViewTrends implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public string $startDate;
    public string $endDate;

    /**
     * Create a new job instance.
     */
    public function __construct(string $startDate, string $endDate)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    /**
     * Execute the job.
     */
    public function handle(TrendService $trendService): void
    {
        $start = Carbon::parse($this->startDate);
        $end = Carbon::parse($this->endDate);

        // Use the TrendService to sync the requested range
        $trendService->syncRange($start, $end);
    }
}
