<?php

use App\Http\Controllers\Performance\PerformanceController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'tenant', 'tenant.active'])->prefix('performance')->group(function () {
    // Performance Hub (Livewire 3)
    Route::get('/', \App\Livewire\Performance\PerformanceHub::class)->name('performance.index');
});
