<?php

use App\Http\Controllers\Api\Policies\AttendancePolicyApiController;
use App\Http\Controllers\Api\Policies\CodeOfConductPolicyApiController;
use App\Http\Controllers\Api\Policies\HolidayPolicyApiController;
use App\Http\Controllers\Api\Policies\LeavePolicyApiController;
use App\Http\Controllers\Api\Policies\NoticePeriodPolicyApiController;
use App\Http\Controllers\Api\Policies\OvertimePolicyApiController;
use App\Http\Controllers\Api\Policies\PayrollPolicyApiController;
use App\Http\Controllers\Api\Policies\ProbationPolicyApiController;
use App\Http\Controllers\Api\Policies\ReimbursementPolicyApiController;
use App\Http\Controllers\Api\Policies\WfhPolicyApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware(['auth:sanctum', 'tenant', 'role:admin,hr_manager'])->prefix('policies')->group(function () {
    Route::apiResource('leave', LeavePolicyApiController::class)->parameters(['leave' => 'id']);
    Route::post('leave/{id}/evaluate', [LeavePolicyApiController::class, 'evaluate']);
    Route::post('leave/evaluate-active', [LeavePolicyApiController::class, 'evaluateActive']);

    Route::apiResource('attendance', AttendancePolicyApiController::class)->parameters(['attendance' => 'id']);
    Route::post('attendance/{id}/evaluate', [AttendancePolicyApiController::class, 'evaluate']);
    Route::post('attendance/evaluate-active', [AttendancePolicyApiController::class, 'evaluateActive']);

    Route::apiResource('holiday', HolidayPolicyApiController::class)->parameters(['holiday' => 'id']);
    Route::post('holiday/{id}/evaluate', [HolidayPolicyApiController::class, 'evaluate']);
    Route::post('holiday/evaluate-active', [HolidayPolicyApiController::class, 'evaluateActive']);

    Route::apiResource('payroll', PayrollPolicyApiController::class)->parameters(['payroll' => 'id']);
    Route::post('payroll/{id}/evaluate', [PayrollPolicyApiController::class, 'evaluate']);
    Route::post('payroll/evaluate-active', [PayrollPolicyApiController::class, 'evaluateActive']);

    Route::apiResource('probation', ProbationPolicyApiController::class)->parameters(['probation' => 'id']);
    Route::post('probation/{id}/evaluate', [ProbationPolicyApiController::class, 'evaluate']);
    Route::post('probation/evaluate-active', [ProbationPolicyApiController::class, 'evaluateActive']);

    Route::apiResource('notice-period', NoticePeriodPolicyApiController::class)->parameters(['notice-period' => 'id']);
    Route::post('notice-period/{id}/evaluate', [NoticePeriodPolicyApiController::class, 'evaluate']);
    Route::post('notice-period/evaluate-active', [NoticePeriodPolicyApiController::class, 'evaluateActive']);

    Route::apiResource('overtime', OvertimePolicyApiController::class)->parameters(['overtime' => 'id']);
    Route::post('overtime/{id}/evaluate', [OvertimePolicyApiController::class, 'evaluate']);
    Route::post('overtime/evaluate-active', [OvertimePolicyApiController::class, 'evaluateActive']);

    Route::apiResource('wfh', WfhPolicyApiController::class)->parameters(['wfh' => 'id']);
    Route::post('wfh/{id}/evaluate', [WfhPolicyApiController::class, 'evaluate']);
    Route::post('wfh/evaluate-active', [WfhPolicyApiController::class, 'evaluateActive']);

    Route::apiResource('reimbursement', ReimbursementPolicyApiController::class)->parameters(['reimbursement' => 'id']);
    Route::post('reimbursement/{id}/evaluate', [ReimbursementPolicyApiController::class, 'evaluate']);
    Route::post('reimbursement/evaluate-active', [ReimbursementPolicyApiController::class, 'evaluateActive']);

    Route::apiResource('code-of-conduct', CodeOfConductPolicyApiController::class)->parameters(['code-of-conduct' => 'id']);
    Route::post('code-of-conduct/{id}/evaluate', [CodeOfConductPolicyApiController::class, 'evaluate']);
    Route::post('code-of-conduct/evaluate-active', [CodeOfConductPolicyApiController::class, 'evaluateActive']);
});
