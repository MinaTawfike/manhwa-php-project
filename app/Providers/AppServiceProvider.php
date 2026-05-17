<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Http\Middleware\EnsureSuperAdmin;
use App\Services\ViewTrackingService;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register ViewTrackingService as singleton
        $this->app->singleton(ViewTrackingService::class, function ($app) {
            return new ViewTrackingService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register middleware alias for super admin checks
        $this->app['router']->aliasMiddleware('super.admin', EnsureSuperAdmin::class);
        
        // Share unread bookmarks count with all views
        View::composer('*', function ($view) {
            if (Auth::check()) {
                $view->with(
                    'unreadBookmarkedComicsCount',
                    Auth::user()->unreadBookmarkedComicsCount()
                );
            } else {
                $view->with('unreadBookmarkedComicsCount', 0);
            }
        });
    }
}
