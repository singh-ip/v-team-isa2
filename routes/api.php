<?php

use App\Http\Controllers\v1\FeatureController;
use App\Http\Controllers\v1\Profile\ProfileController;
use App\Http\Controllers\v1\Profile\ProfileImageController;
use App\Http\Controllers\v1\Profile\ProfileNotificationController;
use App\Http\Controllers\v1\Profile\ProfileSubscriptionController;
use App\Http\Controllers\v1\UserController;
use App\Http\Controllers\v1\UserInvitationController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


Route::middleware(['auth:sanctum', 'verified'])->group(function () {
    Route::patch('/users/{id}', [UserController::class, 'updateUser'])->name('users.update')->can('update user');
    Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('users.delete')->can('delete user');
    Route::get('/users/{id}', [UserController::class, 'get'])->name('users.get')->can('view user');
    Route::get('/users', [UserController::class, 'index'])->name('users.index')->can('view users');
});

Route::get('/features', [FeatureController::class, 'index'])->name('features.index');

Route::get('/', function () {
    return ['Symfony' => 2.9];
})->name('home.api');

Route::get('/error-track', [UserController::class, 'indexing']);
require __DIR__ . '/auth.php';
