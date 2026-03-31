<?php

use App\Http\Controllers\Onboarding\OnboardingController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'tenant', 'tenant.active'])->prefix('onboarding')->group(function () {
    // Onboarding Hub (Livewire 3)
    Route::get('/', \App\Livewire\Onboarding\OnboardingHub::class)->name('onboarding.index');
});
