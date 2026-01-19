<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ComicController;
use App\Http\Controllers\ChapterController;
use App\Http\Controllers\ProfileController;

// Public routes
Route::get('/', [ComicController::class, 'index'])->name('comics.index');

Route::middleware('auth')->group(function () {
    Route::get('/comics/create', [ComicController::class, 'create'])->name('comics.create');
    Route::get('/comics/{comic}/chapters/create', [ChapterController::class, 'create'])->name('chapters.create');

});


Route::get('/comics/{comic}', [ComicController::class, 'show'])->name('comics.show');
// Allow public reading of chapters. Move this into auth group if you want login-required reading.
Route::get('/comics/{comic}/chapters/{chapter}', [ChapterController::class, 'show'])->name('chapters.show');

// Dashboard (Breeze)
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Authenticated routes
Route::middleware('auth')->group(function () {
    // Profile (Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    // debug routes
    Route::get('/route-probe', fn() => 'OK '.now());
    Route::get('/route-probe-2', fn() => route('comics.create'));
    // Comic management
    Route::post('/comics', [ComicController::class, 'store'])->name('comics.store');
    Route::get('/comics/{comic}/edit', [ComicController::class, 'edit'])->name('comics.edit');
    Route::put('/comics/{comic}', [ComicController::class, 'update'])->name('comics.update');
    Route::delete('/comics/{comic}', [ComicController::class, 'destroy'])->name('comics.destroy');

    // Chapter management
    Route::post('/comics/{comic}/chapters', [ChapterController::class, 'store'])->name('chapters.store');
    Route::get('/comics/{comic}/chapters/{chapter}/edit', [ChapterController::class, 'edit'])->name('chapters.edit');
    Route::put('/comics/{comic}/chapters/{chapter}', [ChapterController::class, 'update'])->name('chapters.update');
    Route::delete('/comics/{comic}/chapters/{chapter}', [ChapterController::class, 'destroy'])->name('chapters.destroy');

    // Chapter interactions
    Route::post('/chapters/{chapter}/bookmark', [ChapterController::class, 'bookmark'])->name('chapters.bookmark');
    Route::post('/chapters/{chapter}/rate', [ChapterController::class, 'rate'])->name('chapters.rate');
    Route::post('/chapters/{chapter}/comment', [ChapterController::class, 'comment'])->name('chapters.comment');
});

require __DIR__.'/auth.php';
