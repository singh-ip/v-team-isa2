<?php

Route::group(['prefix' => 'api/v1', 'middleware' => ['auth:sanctum', 'verified']], function () {
    Route::get('/organisation', function () {
        return ['Symfony' => 2.9];
    })->name('organisation.api');
});