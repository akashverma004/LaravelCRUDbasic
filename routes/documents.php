<?php

use App\Http\Controllers\Documents\DocumentController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Document Management Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'tenant', 'tenant.active'])->prefix('documents')->group(function () {
    // Document Vault (Livewire 3)
    Route::get('/', \App\Livewire\Documents\DocumentVault::class)->name('documents.index');
});
