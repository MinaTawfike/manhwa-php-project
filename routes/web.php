<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ComicController;
use App\Http\Controllers\ChapterController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;

// Debug route
Route::get('/debug', function () {
    return [
        'method' => request()->method(),
        'path' => request()->path(),
        'url' => request()->url(),
        'routes' => collect(Route::getRoutes())->map(function ($route) {
            return [
                'method' => implode('|', $route->methods()),
                'uri' => $route->uri(),
                'name' => $route->getName(),
            ];
        })->take(10)->toArray()
    ];
});

// SEO: XML Sitemap for Google and search engines
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');

// Public routes
Route::get('/', [ComicController::class, 'index'])->name('comics.index');

// Public category pages (SEO-friendly slug-based URLs)
Route::get('/categories/{category:slug}', [CategoryController::class, 'show'])->name('categories.show');

// Terms and privacy
Route::get('/terms', function () {
    return view('terms');
})->name('terms');
Route::get('/privacy', function () {
    return view('privacy');
})->name('privacy');
Route::get('/error', function () {
    return view('errors.custom'); // create this view
})->name('error.page');

// Content management (only super admins) - must come before /comics/{comic} to prevent collision
Route::middleware(['auth', 'super.admin', 'throttle:60,1'])->group(function () {
    // Comic management
    Route::get('/comics/create', [ComicController::class, 'create'])->name('comics.create');
    Route::post('/comics', [ComicController::class, 'store'])->name('comics.store');
    Route::get('/comics/{comic:slug}/edit', [ComicController::class, 'edit'])->name('comics.edit');
    Route::put('/comics/{comic:slug}', [ComicController::class, 'update'])->name('comics.update');
    Route::delete('/comics/{comic:slug}', [ComicController::class, 'destroy'])->name('comics.destroy');

    // Chapter management
    Route::get('/comics/{comic:slug}/chapters/create', [ChapterController::class, 'create'])->name('chapters.create');
    Route::post('/comics/{comic:slug}/chapters', [ChapterController::class, 'store'])->name('chapters.store');
    Route::get('/comics/{comic:slug}/chapters/{chapter}/edit', [ChapterController::class, 'edit'])->name('chapters.edit');
    Route::put('/comics/{comic:slug}/chapters/{chapter}', [ChapterController::class, 'update'])->name('chapters.update');
    Route::delete('/comics/{comic:slug}/chapters/{chapter}', [ChapterController::class, 'destroy'])->name('chapters.destroy');
});

Route::get('/comics/{comic:slug}', [ComicController::class, 'show'])->name('comics.show');
// Allow public reading of chapters. Move this into auth group if you want login-required reading.
Route::get('/comics/{comic:slug}/chapters/{chapter}', [ChapterController::class, 'show'])->name('chapters.show');

// Dashboard (Breeze)
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Authenticated routes
Route::middleware(['auth', 'throttle:60,1'])->group(function () {
    // Profile (Breeze)
    Route::get('/profile', [ProfileController::class, 'view'])->name('profile.view');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::patch('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.updatePassword');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    // debug routes

    // Chapter interactions (allowed for all authenticated users)
    Route::post('/chapters/{chapter}/bookmark', [ChapterController::class, 'bookmark'])->name('chapters.bookmark');
    Route::post('/chapters/{chapter}/rate', [ChapterController::class, 'rate'])->name('chapters.rate');
    Route::post('/chapters/{chapter}/comment', [ChapterController::class, 'comment'])->name('chapters.comment');

    // Comic interactions (allowed for all authenticated users)
    Route::post('/comics/{comic:slug}/bookmark', [ComicController::class, 'bookmark'])->name('comics.bookmark');
    Route::get('/bookmarks', [ComicController::class, 'bookmarks'])->name('bookmarks.index');
});

// Admin dashboard and management (protected by EnsureSuperAdmin middleware alias)
Route::middleware(['auth', 'super.admin', 'throttle:60,1'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])
        ->name('dashboard');
    
    Route::get('/analytics', [\App\Http\Controllers\Admin\DashboardController::class, 'comicAnalytics'])
        ->name('analytics');
    
    Route::get('/system-health', [\App\Http\Controllers\Admin\DashboardController::class, 'systemHealth'])
        ->name('system-health');
    
    Route::get('/content-moderation', [\App\Http\Controllers\Admin\DashboardController::class, 'contentModeration'])
        ->name('content-moderation');

    // User management
    Route::get('/users', [\App\Http\Controllers\Admin\UserManagementController::class, 'index'])
        ->name('users.index');

    Route::post('/users/{user}/role', [\App\Http\Controllers\Admin\UserManagementController::class, 'update'])
        ->name('users.update');

    // Category management
    Route::prefix('categories')->name('categories.')->group(function () {
        Route::get('/', [AdminCategoryController::class, 'index'])->name('index');
        Route::get('/create', [AdminCategoryController::class, 'create'])->name('create');
        Route::post('/', [AdminCategoryController::class, 'store'])->name('store');
        Route::get('/{category}/edit', [AdminCategoryController::class, 'edit'])->name('edit');
        Route::put('/{category}', [AdminCategoryController::class, 'update'])->name('update');
        Route::delete('/{category}', [AdminCategoryController::class, 'destroy'])->name('destroy');
    });
});

require __DIR__.'/auth.php';
