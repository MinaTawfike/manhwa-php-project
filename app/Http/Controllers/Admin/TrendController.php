<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\TrendService;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class TrendController extends Controller
{
    public function __construct(private TrendService $trendService) {}

    public function index(): View
    {
        return view('admin.dashboard.trends');
    }

    public function data(): JsonResponse
    {
        return response()->json($this->trendService->getSevenDayTrends());
    }
}
