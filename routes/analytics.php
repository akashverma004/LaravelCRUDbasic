<?php

use App\Http\Controllers\Analytics\AnalyticsController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'tenant', 'tenant.active'])->prefix('analytics')->group(function () {
    Route::get('/', [AnalyticsController::class, 'index'])->name('analytics.index');
    Route::get('/data', [AnalyticsController::class, 'data'])->name('analytics.data');
});
