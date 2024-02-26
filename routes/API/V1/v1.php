<?php

use App\Http\Controllers\API\V1\AuthController;
use Illuminate\Support\Facades\Route;
Route::prefix("/v1")->group(function (){
    Route::prefix('/auth')->group(function (){
        Route::post('/signup',                   [AuthController::class, "signup"]);
        Route::post('/sendVerifyCode',           [AuthController::class, "sendVerifyCode"]);
        Route::post('/verifyEmail',              [AuthController::class, "verifyEmail"]);
        Route::post('/login',                    [AuthController::class, "login"]);
        Route::post('/forgetPassword',           [AuthController::class, "forgetPassword"]);
        Route::post('/resendForgetPasswordVerifyCode',                       [AuthController::class, "resendForgetPasswordCode"]);
        Route::post('/forgetPasswordVerifyCode', [AuthController::class, "forgetPasswordVerifyCode"]);
        Route::post('/forgetPasswordChange',              [AuthController::class, "forgetPasswordChange"]);
    });
    Route::middleware('auth:api')->group(function(){
        Route::prefix('/auth')->group(function (){
            Route::post('/logout', [AuthController::class , 'logout']);
            Route::post('/changePassword', [AuthController::class , 'changePassword']);
        });
    });
});


