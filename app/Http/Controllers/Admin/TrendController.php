<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\TrendService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class TrendController extends Controller
{
    public function __construct(private TrendService $trendService) {}

    public function index(): View
    {
        return view('admin.dashboard.trends');
    }

    public function data(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'range' => ['nullable', 'in:7,28,90,365'],
            'start' => ['nullable', 'date_format:Y-m-d', 'required_with:end'],
            'end' => ['nullable', 'date_format:Y-m-d', 'required_with:start'],
        ]);

        if ($request->filled('start') || $request->filled('end')) {
            $start = Carbon::parse($validated['start']);
            $end = Carbon::parse($validated['end']);

            if ($start->gt($end)) {
                abort(422, 'Start date must be before or equal to end date.');
            }

            if ($start->diffInDays($end) > 365) {
                abort(422, 'Maximum supported range is 365 days.');
            }

            return response()->json($this->trendService->getTrendsForDates($start, $end));
        }

        $range = (int) ($validated['range'] ?? 7);

        return response()->json($this->trendService->getTrendsForRange($range));
    }
}
