<?php

Route::group(['prefix' => 'api/v1', 'middleware' => ['auth:sanctum', 'verified']], function () {
    Route::get('/team', function () {
        return ['Symfony' => 2.9];
    })->name('team.api');
});