<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\CompanySignupController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TenantInvitationController;
use App\Http\Controllers\Auth\SocialController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('auth/{provider}/redirect', [SocialController::class, 'redirect'])->name('social.redirect');
    Route::get('auth/{provider}/callback', [SocialController::class, 'callback'])->name('social.callback');

    Route::get('register', \App\Livewire\Auth\Register::class)->name('register');
    Route::get('login', \App\Livewire\Auth\Login::class)->name('login');

    Route::get('forgot-password', \App\Livewire\Auth\ForgotPassword::class)->name('password.request');

    Route::get('reset-password/{token}', \App\Livewire\Auth\ResetPassword::class)->name('password.reset');

    Route::get('company/signup', \App\Livewire\Auth\CompanySignup::class)->name('company-signup.create');

    Route::get('invitation/{token}', \App\Livewire\Auth\AcceptInvitation::class)->name('tenant-invitations.accept');
});

Route::middleware('auth')->group(function () {
    Route::get('verify-email', \App\Livewire\Auth\VerifyEmail::class)->name('verification.notice');

    Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
                ->middleware(['signed', 'throttle:6,1'])
                ->name('verification.verify');

    Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
                ->middleware('throttle:6,1')
                ->name('verification.send');

    Route::get('confirm-password', \App\Livewire\Auth\ConfirmPassword::class)->name('password.confirm');

    Route::put('password', [PasswordController::class, 'update'])->name('password.update');

    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
                ->name('logout');

    // Profile Routes
    Route::get('profile', \App\Livewire\Profile\AccountSettings::class)->name('profile.edit');
    Route::patch('profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
