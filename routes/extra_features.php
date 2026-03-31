<?php

use App\Http\Controllers\Audit\AuditController;
use App\Http\Controllers\Assets\AssetController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'tenant', 'tenant.active'])->group(function () {
    // P4: Audit
    Route::prefix('audit')->group(function () {
    // P4: Audit Grid (Livewire 3)
    Route::prefix('audit')->group(function () {
        Route::get('/', \App\Livewire\Audit\AuditHub::class)->name('audit.index');
    });
    });

    // P5: Assets (Livewire 3)
    Route::prefix('assets')->group(function () {
        Route::get('/', \App\Livewire\Assets\AssetManager::class)->name('assets.index');
    });

    // P6: Payroll Hub (Livewire 3)
    Route::prefix('payroll')->group(function () {
        Route::get('/', \App\Livewire\Payroll\PayrollHub::class)->name('payroll.index');
        Route::get('/payslips/{payslip}/pdf', [\App\Http\Controllers\Payroll\PayrollController::class, 'downloadPdf'])->name('payroll.download-pdf');
        Route::get('/payslips/{payslip}/preview', [\App\Http\Controllers\Payroll\PayrollController::class, 'viewPdf'])->name('payroll.view-pdf');
    });

    // P7: Shifts
    Route::prefix('shifts')->group(function () {
        Route::get('/', \App\Livewire\Attendance\ShiftRoster::class)->name('shifts.index');
    });

    // P8: Attendance Management (HR/Admin)
    Route::prefix('attendance-management')->group(function () {
        Route::get('/', \App\Livewire\Attendance\AttendanceManagement::class)->name('attendance.index');
    });

    // P9: Company Settings (Livewire 3)
    Route::prefix('settings')->group(function () {
        Route::get('/', \App\Livewire\CompanySettings::class)->name('settings.index');
    });
});
