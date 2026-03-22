<?php

use App\Http\Controllers\Audit\AuditController;
use App\Http\Controllers\Assets\AssetController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'tenant', 'tenant.active'])->group(function () {
    // P4: Audit
    Route::prefix('audit')->group(function () {
        Route::get('/', [AuditController::class, 'index'])->name('audit.index');
        Route::get('/data', [AuditController::class, 'data'])->name('audit.data');
    });

    // P5: Assets
    Route::prefix('assets')->group(function () {
        Route::get('/', [AssetController::class, 'index'])->name('assets.index');
        Route::get('/data', [AssetController::class, 'data'])->name('assets.data');
        Route::post('/', [AssetController::class, 'store'])->name('assets.store');
        Route::patch('/{asset}', [AssetController::class, 'update'])->name('assets.update');
    });

    // P6: Payroll
    Route::prefix('payroll')->group(function () {
        Route::get('/', [\App\Http\Controllers\Payroll\PayrollController::class, 'index'])->name('payroll.index');
        Route::get('/data', [\App\Http\Controllers\Payroll\PayrollController::class, 'data'])->name('payroll.data');
        Route::post('/structures', [\App\Http\Controllers\Payroll\PayrollController::class, 'storeStructure'])->name('payroll.structures.store');
        Route::put('/structures/{payStructure}', [\App\Http\Controllers\Payroll\PayrollController::class, 'updateStructure'])->name('payroll.structures.update');
        Route::delete('/structures/{payStructure}', [\App\Http\Controllers\Payroll\PayrollController::class, 'destroyStructure'])->name('payroll.structures.destroy');
        Route::post('/generate', [\App\Http\Controllers\Payroll\PayrollController::class, 'generatePayslips'])->name('payroll.generate');
        Route::post('/payslips/{payslip}/pay', [\App\Http\Controllers\Payroll\PayrollController::class, 'markAsPaid'])->name('payroll.payslips.pay');
    });

    // P7: Shifts
    Route::prefix('shifts')->group(function () {
        Route::get('/', [\App\Http\Controllers\Shifts\ShiftController::class, 'index'])->name('shifts.index');
        Route::get('/data', [\App\Http\Controllers\Shifts\ShiftController::class, 'data'])->name('shifts.data');
        Route::post('/templates', [\App\Http\Controllers\Shifts\ShiftController::class, 'storeShift'])->name('shifts.templates.store');
        Route::post('/assign', [\App\Http\Controllers\Shifts\ShiftController::class, 'assign'])->name('shifts.assign');
        Route::delete('/schedule/{schedule}', [\App\Http\Controllers\Shifts\ShiftController::class, 'destroy'])->name('shifts.schedule.destroy');
    });

    // P8: Attendance Management (HR/Admin)
    Route::prefix('attendance-management')->group(function () {
        Route::get('/', [\App\Http\Controllers\Hrms\AttendanceManagementController::class, 'index'])->name('attendance.index');
        Route::get('/data', [\App\Http\Controllers\Hrms\AttendanceManagementController::class, 'data'])->name('attendance.data');
        Route::post('/update', [\App\Http\Controllers\Hrms\AttendanceManagementController::class, 'update'])->name('attendance.update');
    });
});
