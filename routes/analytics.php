<?php

use App\Http\Controllers\Analytics\AnalyticsController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'tenant', 'tenant.active'])->prefix('analytics')->group(function () {
    // Insight Engine (Livewire 3)
    Route::get('/', \App\Livewire\Analytics\InsightEngine::class)->name('analytics.index');
});
