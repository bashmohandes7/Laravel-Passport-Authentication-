<?php

use App\Http\Controllers\Api\Admin\LoginController;
use App\Http\Controllers\Api\Admin\LogoutController;
use App\Http\Controllers\Api\Admin\AdminController;
use App\Http\Controllers\Api\PermissionController;
use App\Http\Controllers\Api\RoleController;
use Illuminate\Support\Facades\Route;

// Admin  Routes

Route::prefix('admin')
    ->group(function () {
        Route::post('login',            [LoginController::class, 'login']);

        Route::group(['middleware' =>        ['auth:admin']], function () {
            Route::get('/logout',        [LogoutController::class, 'logout']);
            Route::get('/profile',       [LoginController::class, 'profile']);
            Route::group(['middleware' => 'role:super-admin'], function(){
                // User Crud
                Route::get('/admins',         [AdminController::class, 'index']);
                Route::post('/admins',        [AdminController::class, 'store']);
                Route::get('/admins/{id}',    [AdminController::class, 'show']);
                Route::put('/admins/{id}',    [AdminController::class, 'update']);
                Route::delete('/admins/{id}', [AdminController::class, 'destroy']);
                // Role Crud
                Route::get('/roles',         [RoleController::class, 'index']);
                Route::post('/roles',        [RoleController::class, 'store']);
                Route::get('/roles/{id}',    [RoleController::class, 'show']);
                Route::put('/roles/{id}',    [RoleController::class, 'update']);
                Route::delete('/roles/{id}', [RoleController::class, 'destroy']);
                // Permission Crud
                Route::get('/permissions', [PermissionController::class, 'index']);
            });
        });
    });
