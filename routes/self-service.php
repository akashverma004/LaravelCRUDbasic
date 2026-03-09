<?php

use App\Http\Controllers\SelfService\MyProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Self-Service Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'tenant', 'tenant.active'])->prefix('self-service')->group(function () {
    // Profile page
    Route::get('/profile', [MyProfileController::class, 'show'])->name('self-service.profile');

    // Async JSON endpoints
    Route::get('/profile/data', [MyProfileController::class, 'data'])->name('self-service.profile.data');
    Route::patch('/profile/info', [MyProfileController::class, 'updateInfo'])->name('self-service.profile.update-info');
    Route::post('/profile/photo', [MyProfileController::class, 'uploadPhoto'])->name('self-service.profile.upload-photo');
    Route::delete('/profile/photo', [MyProfileController::class, 'removePhoto'])->name('self-service.profile.remove-photo');

    // Education CRUD
    Route::post('/profile/educations', [MyProfileController::class, 'storeEducation'])->name('self-service.educations.store');
    Route::delete('/profile/educations/{id}', [MyProfileController::class, 'destroyEducation'])->name('self-service.educations.destroy');

    // Experience CRUD
    Route::post('/profile/experiences', [MyProfileController::class, 'storeExperience'])->name('self-service.experiences.store');
    Route::delete('/profile/experiences/{id}', [MyProfileController::class, 'destroyExperience'])->name('self-service.experiences.destroy');

    // Skills CRUD
    Route::post('/profile/skills', [MyProfileController::class, 'storeSkill'])->name('self-service.skills.store');
    Route::delete('/profile/skills/{id}', [MyProfileController::class, 'destroySkill'])->name('self-service.skills.destroy');

    // Account Management
    Route::patch('/profile/account', [MyProfileController::class, 'updateAccount'])->name('self-service.account.update');
    Route::put('/profile/password', [MyProfileController::class, 'updatePassword'])->name('self-service.password.update');
    Route::delete('/profile/account', [MyProfileController::class, 'deleteAccount'])->name('self-service.account.destroy');
});
