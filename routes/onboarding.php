<?php

use App\Http\Controllers\Onboarding\OnboardingController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'tenant', 'tenant.active'])->prefix('onboarding')->group(function () {
    Route::get('/', [OnboardingController::class, 'index'])->name('onboarding.index');
    Route::get('/data', [OnboardingController::class, 'data'])->name('onboarding.data');
    Route::patch('/tasks/{task}/complete', [OnboardingController::class, 'completeTask'])->name('onboarding.tasks.complete');
    Route::post('/assign', [OnboardingController::class, 'assign'])->name('onboarding.assign');
});
