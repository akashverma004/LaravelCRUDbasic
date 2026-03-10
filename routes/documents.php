<?php

use App\Http\Controllers\Documents\DocumentController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Document Management Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'tenant', 'tenant.active'])->prefix('documents')->group(function () {
    Route::get('/', [DocumentController::class, 'index'])->name('documents.index');
    Route::get('/data', [DocumentController::class, 'data'])->name('documents.data');
    Route::post('/', [DocumentController::class, 'store'])->name('documents.store');
    Route::get('/{id}/download', [DocumentController::class, 'download'])->name('documents.download');
    Route::delete('/{id}', [DocumentController::class, 'destroy'])->name('documents.destroy');
    Route::get('/employees', [DocumentController::class, 'employees'])->name('documents.employees');
});
