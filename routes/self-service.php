<?php

use App\Http\Controllers\SelfService\MyProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Self-Service Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'tenant', 'tenant.active'])->prefix('self-service')->group(function () {
    // Profile page (Livewire 3)
    Route::get('/profile', \App\Livewire\SelfService\MyProfile::class)->name('self-service.profile');

    // All profile actions (Contact, Edu, Exp, Skills, Password) are now handled by the Livewire component.
});
