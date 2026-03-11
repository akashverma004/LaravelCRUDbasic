<?php

use App\Http\Controllers\Workflows\WorkflowController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'tenant', 'tenant.active'])->prefix('workflows')->group(function () {
    Route::get('/', [WorkflowController::class, 'index'])->name('workflows.index');
    Route::get('/data', [WorkflowController::class, 'data'])->name('workflows.data');
    Route::post('/', [WorkflowController::class, 'store'])->name('workflows.store');
    Route::post('/templates', [WorkflowController::class, 'storeTemplate'])->name('workflows.templates.store');
    Route::patch('/templates/{template}', [WorkflowController::class, 'updateTemplate'])->name('workflows.templates.update');
    Route::post('/templates/{template}/archive', [WorkflowController::class, 'archiveTemplate'])->name('workflows.templates.archive');
    Route::get('/{workflow}/attachment', [WorkflowController::class, 'downloadAttachment'])->name('workflows.attachment');
    Route::get('/{workflow}', [WorkflowController::class, 'show'])->name('workflows.show');
    Route::post('/{workflow}/approve', [WorkflowController::class, 'approve'])->name('workflows.approve');
    Route::post('/{workflow}/cancel', [WorkflowController::class, 'cancel'])->name('workflows.cancel');
    Route::post('/{workflow}/resubmit', [WorkflowController::class, 'resubmit'])->name('workflows.resubmit');
    Route::post('/{workflow}/reject', [WorkflowController::class, 'reject'])->name('workflows.reject');
    Route::post('/{workflow}/fulfill-asset', [WorkflowController::class, 'fulfillAsset'])->name('workflows.fulfill-asset');
});
