<?php

use App\Http\Controllers\Performance\PerformanceController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'tenant', 'tenant.active'])->prefix('performance')->group(function () {
    Route::get('/', [PerformanceController::class, 'index'])->name('performance.index');
    Route::get('/data', [PerformanceController::class, 'data'])->name('performance.data');
    
    // Goals
    Route::post('/goals', [PerformanceController::class, 'storeGoal'])->name('performance.goals.store');
    Route::patch('/goals/{goal}', [PerformanceController::class, 'updateGoal'])->name('performance.goals.update');
    
    // Reviews
    Route::post('/reviews', [PerformanceController::class, 'storeReview'])->name('performance.reviews.store');
    
    // 1-on-1s
    Route::post('/notes', [PerformanceController::class, 'storeNote'])->name('performance.notes.store');
});
