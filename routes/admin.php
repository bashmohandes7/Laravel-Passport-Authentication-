<?php

use App\Http\Controllers\Api\Admin\LoginController;
use App\Http\Controllers\Api\Admin\LogoutController;
use App\Http\Controllers\Api\Admin\UserController;
use Illuminate\Support\Facades\Route;

// Admin  Routes

Route::middleware([
    'api',
])
    ->prefix('admin')
    ->group(function () {
        Route::post('login',            [LoginController::class, 'login']);

        Route::group(['middleware' =>        ['auth:api']], function () {
            Route::get('/logout',        [LogoutController::class, 'logout']);
            // User Crud
            Route::get('/users',         [UserController::class, 'index']);
            Route::post('/users',        [UserController::class, 'store']);
            Route::get('/users/{id}',    [UserController::class, 'show']);
            Route::put('/users/{id}',    [UserController::class, 'update']);
            Route::delete('/users/{id}', [UserController::class, 'destroy']);
            Route::get('/profile',       [UserController::class, 'profile']);
        });
    });
