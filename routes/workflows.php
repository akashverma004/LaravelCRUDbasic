<?php

use App\Http\Controllers\Workflows\WorkflowController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'tenant', 'tenant.active'])->prefix('workflows')->group(function () {
    // Protocol Hub (Livewire 3)
    Route::get('/', \App\Livewire\Workflows\WorkflowHub::class)->name('workflows.index');
});
