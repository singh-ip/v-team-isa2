<?php

use Modules\Profile\Http\Controllers\v1\ProfileController;
use Modules\Profile\Http\Controllers\v1\ProfileImageController;
use Modules\Profile\Http\Controllers\v1\ProfileSubscriptionController;
use Modules\Profile\Http\Controllers\v1\ProfileNotificationController;

Route::middleware(['auth:sanctum', 'verified'])->prefix('api/v1/profile')->group(function () {
    Route::controller(ProfileController::class)
        ->group(function () {
            Route::get('/', 'show')->name('profile.show');
            Route::patch('/', 'update')->name('profile.update');
            Route::delete('/', 'destroy')->name('profile.destroy');
        });
    Route::get('subscription', ProfileSubscriptionController::class)
        ->name('profile-subscription.show');
    Route::post('image', [ProfileImageController::class, 'store'])
        ->name('profile-image.store');
    Route::delete('image', [ProfileImageController::class, 'destroy'])
        ->name('profile-image.destroy');
    Route::get('image/status', [ProfileImageController::class, 'status'])
        ->name('profile-image.status');
    Route::get('notifications', [ProfileNotificationController::class, 'show'])
        ->name('profile-notification.show');
    Route::patch('notifications', [ProfileNotificationController::class, 'update'])
        ->name('profile-notification.update');
});