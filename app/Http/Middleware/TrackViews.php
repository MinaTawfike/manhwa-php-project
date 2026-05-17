<?php

namespace App\Http\Middleware;

use App\Services\ViewTrackingService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class TrackViews
{
    /**
     * View tracking service instance.
     */
    protected ViewTrackingService $viewTrackingService;

    /**
     * Create a new middleware instance.
     */
    public function __construct(ViewTrackingService $viewTrackingService)
    {
        $this->viewTrackingService = $viewTrackingService;
    }

    /**
     * Handle an incoming request.
     * 
     * This middleware automatically tracks views for comics and chapters
     * when they are accessed via their show routes.
     * 
     * The tracking logic:
     * 1. Identifies if the route is a comic or chapter view
     * 2. Extracts the model from route parameters
     * 3. Calls the view tracking service
     * 4. Does not block the request (non-blocking middleware)
     * 
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Only track GET requests to avoid tracking form submissions, etc.
        if ($request->isMethod('GET') && $response->getStatusCode() === 200) {
            $this->trackViewIfApplicable($request);
        }

        return $response;
    }

    /**
     * Track view if the route is applicable.
     * 
     * Checks if the current route is for viewing a comic or chapter
     * and automatically tracks the view using the ViewTrackingService.
     * 
     * @param Request $request
     */
    private function trackViewIfApplicable(Request $request): void
    {
        $route = $request->route();
        
        if (!$route) {
            return;
        }

        $routeName = $route->getName();

        // Track comic views
        if ($routeName === 'comics.show') {
            $comic = $route->parameter('comic');
            if ($comic) {
                $this->viewTrackingService->trackComicView($comic);
            }
        }

        // Track chapter views
        elseif ($routeName === 'chapters.show') {
            $chapter = $route->parameter('chapter');
            if ($chapter) {
                $this->viewTrackingService->trackChapterView($chapter);
            }
        }
    }
}
