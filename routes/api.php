<?php

use App\Http\Controllers\Api\Front\ForgotController;
use App\Http\Controllers\Api\Front\LoginController;
use App\Http\Controllers\Api\Front\LogoutController;
use App\Http\Controllers\Api\Front\RegisterController;
use App\Http\Controllers\Api\Front\ResetController;
use App\Http\Controllers\Api\PermissionController;
use App\Http\Controllers\Api\RoleController;
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

Route::middleware(
    'api'
)->group(function () {
// Unauthenticated Users
    Route::post('register',         [RegisterController::class, 'register']);
    Route::post('login',            [LoginController::class, 'login']);
    Route::post('forgot-password',  [ForgotController::class, 'forgot']);
    Route::post('reset-password',   [ResetController::class, 'reset']);
});

Route::middleware([
    'auth:api',
])->group(function (){
    Route::get('logout',         [LogoutController::class,  'logout']);
    // Role Crud
    Route::get('/roles',         [RoleController::class,    'index']);
    Route::post('/roles',        [RoleController::class,    'store']);
    Route::get('/roles/{id}',    [RoleController::class,    'show']);
    Route::put('/roles/{id}',    [RoleController::class,    'update']);
    Route::delete('/roles/{id}', [RoleController::class,    'destroy']);
// Permission index
    Route::get('/permissions',   [PermissionController::class, 'index']);
});

