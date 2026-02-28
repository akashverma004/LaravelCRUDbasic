<?php

use App\Http\Controllers\HrmsController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HrmsController::class, 'dashboard'])->name('hrms.dashboard');
Route::post('/departments', [HrmsController::class, 'storeDepartment'])->name('hrms.departments.store');
Route::post('/employees', [HrmsController::class, 'storeEmployee'])->name('hrms.employees.store');
Route::post('/leave-requests', [HrmsController::class, 'storeLeave'])->name('hrms.leave.store');
