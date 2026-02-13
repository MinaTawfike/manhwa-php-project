<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ComicController;
use App\Http\Controllers\ChapterController;
use App\Http\Controllers\ProfileController;

// Public routes
Route::get('/', [ComicController::class, 'index'])->name('comics.index');
Route::get('/error', function () {
    return view('errors.custom'); // create this view
})->name('error.page');

// Content management (only super admins) - must come before /comics/{comic} to prevent collision
Route::middleware(['auth', 'super.admin'])->group(function () {
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
Route::middleware('auth')->group(function () {
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

// Admin user management (protected by EnsureSuperAdmin middleware alias)
Route::middleware(['auth', 'super.admin'])->group(function () {
    Route::get('/admin/users', [\App\Http\Controllers\Admin\UserManagementController::class, 'index'])
        ->name('admin.users.index');

    Route::post('/admin/users/{user}/role', [\App\Http\Controllers\Admin\UserManagementController::class, 'update'])
        ->name('admin.users.update');
});

require __DIR__.'/auth.php';
