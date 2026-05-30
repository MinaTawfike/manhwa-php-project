<?php

use App\Http\Controllers\Admin\TrendController;
use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth', 'super.admin', 'throttle:60,1'])
    ->prefix('admin')
    ->name('api.admin.')
    ->group(function () {
        Route::get('/trends/data', [TrendController::class, 'data'])
            ->name('trends.data');
    });
