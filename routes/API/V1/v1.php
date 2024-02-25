<?php

use App\Http\Controllers\API\V1\AuthController;
use Illuminate\Support\Facades\Route;

Route::post('/v1/auth/signup',                   [AuthController::class, "signup"]);
Route::post('/v1/auth/sendVerifyCode',           [AuthController::class, "sendVerifyCode"]);
Route::post('/v1/auth/verifyEmail',              [AuthController::class, "verifyEmail"]);
Route::post('/v1/auth/login',                    [AuthController::class, "login"]);
Route::post('/v1/auth/forgetPassword',           [AuthController::class, "forgetPassword"]);
Route::post('/v1/auth/resendForgetPasswordVerifyCode',                       [AuthController::class, "resendForgetPasswordCode"]);
Route::post('/v1/auth/forgetPasswordVerifyCode', [AuthController::class, "forgetPasswordVerifyCode"]);
Route::post('/v1/auth/forgetPasswordChange',              [AuthController::class, "forgetPasswordChange"]);
Route::middleware('auth:api')->group(fn() =>
    Route::post('/logout', [AuthController::class , 'logout'])
);
