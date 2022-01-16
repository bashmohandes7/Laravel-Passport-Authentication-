<?php

use App\Http\Controllers\Api\Front\CategoryController;
use App\Http\Controllers\Api\Front\UserController;
use App\Http\Controllers\Api\Front\UserProfileController;
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

Route::middleware('api')->group(function (){
    Route::post('register',         [UserController::class,'register']);
    Route::post('verify',           [UserController::class,'verify']);
    Route::post('login',            [UserController::class,'login']);
    Route::post('forgot-password',  [UserController::class, 'forgot']);
    Route::post('reset-password',   [UserController::class, 'reset']);
    Route::group(['middleware' => 'auth:api'], function () {
        Route::get('/profile',          [UserProfileController::class, 'profile']);
        Route::get('logout',            [UserProfileController::class, 'logout']);
        Route::post('change-password',  [UserProfileController::class, 'changePassword']);
        // Category Crud
        Route::get('/categories',         [CategoryController::class, 'index']);
        Route::post('/categories',        [CategoryController::class, 'store']);
        Route::get('/categories/{id}',    [CategoryController::class, 'show']);
        Route::put('/categories/{id}',    [CategoryController::class, 'update']);
        Route::delete('/categories/{id}', [CategoryController::class, 'destroy']);
    });
});
