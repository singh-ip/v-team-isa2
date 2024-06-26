<?php

use Modules\UserInvitation\Http\Controllers\v1\MemberCountController;
use Modules\UserInvitation\Http\Controllers\v1\UserInvitationController;

Route::group(['prefix' => 'api/v1'], function () {
    Route::post('invitations/resend/{email}', [UserInvitationController::class, 'resendInvite'])
        ->middleware(['throttle:6,1'])
        ->name('invite.resend');
    Route::group(['middleware' => ['auth:sanctum']], function () {
        Route::get('members/count', [MemberCountController::class, 'get'])
            ->name('members.count')
            ->can('view users');
        Route::controller(UserInvitationController::class)
            ->prefix('/invitations')
            ->group(function () {
                Route::group(['middleware' => 'verified'], function () {
                    Route::get('/', 'index')->name('invitations.index')->can('view users');
                    Route::post('/', 'store')->name('invitations.store')->can('create user');
                    Route::delete('/{id}', 'destroy')->name('invitations.destroy')->can('create user');
                });
            })
            ->group(function () {
                Route::post('/resend', 'resend')->name('invitations.resend');
            });
    });
});