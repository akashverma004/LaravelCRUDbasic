<?php

use App\Http\Controllers\Api\Notifications\NotificationController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Notification Routes
|--------------------------------------------------------------------------
|
| All notification endpoints return JSON and use session-based auth.
| Included from RouteServiceProvider with web middleware.
|
*/

Route::middleware(['auth', 'tenant'])->prefix('api/notifications')->group(function () {
    Route::get('/', [NotificationController::class, 'index'])->name('api.notifications.index');
    Route::get('/unread-count', [NotificationController::class, 'unreadCount'])->name('api.notifications.unread-count');
    Route::post('/{id}/read', [NotificationController::class, 'markAsRead'])->name('api.notifications.read');
    Route::post('/read-all', [NotificationController::class, 'markAllAsRead'])->name('api.notifications.read-all');
});
