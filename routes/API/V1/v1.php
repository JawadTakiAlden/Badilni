<?php

use App\Http\Controllers\API\V1\AuthController;
use App\Http\Controllers\API\V1\CategoryController;
use App\Http\Controllers\API\V1\HomeController;
use App\Http\Controllers\API\V1\SliderController;
use App\Http\Controllers\API\V1\SplashController;
use App\Http\Controllers\API\V1\UserController;
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

    Route::get('/splash' , [SplashController::class , 'getSplashSlides']);

    Route::middleware('auth:api')->group(function(){
        Route::prefix('/auth')->group(function (){
            Route::post('/logout', [AuthController::class , 'logout']);
            Route::post('/changePassword', [AuthController::class , 'changePassword']);
        });

        Route::prefix('/users')->group(function (){
           Route::get('/profile' , [UserController::class , 'getMyProfile']);
           Route::get('/profileOf/{userID}' , [UserController::class , 'getProfileOfUser']);
           Route::post('/updateProfile/{userID}' , [UserController::class , 'updateProfile']);
        });

        Route::get('/home' , [HomeController::class , 'getHome']);

        Route::prefix('/slider')->group(function (){
            Route::get('/getAll' , [SliderController::class , 'getAllSlider']);
            Route::get('/getHome' , [SliderController::class , 'getHomeSlider']);
            Route::post('/create' , [SliderController::class , 'createSlider']);
            Route::post('/update/{slide_id}' , [SliderController::class , 'updateSlider']);
            Route::delete('/delete/{slide_id}' , [SliderController::class , 'deleteSlider']);
        });

        Route::prefix('/categories')->group(function (){
            Route::get('/getAll' , [CategoryController::class , 'getAllCategories']);
            Route::get('/getActive' , [CategoryController::class , 'getActiveCategories']);
            Route::post('/createCategory' , [CategoryController::class , 'createCategory']);
            Route::post('/createSubCategory' , [CategoryController::class , 'createSubCategory']);
            Route::post('/update/{category_id}' , [CategoryController::class , 'updateCategory']);
            Route::delete('/delete/{category_id}' , [CategoryController::class , 'deleteCategory']);
        });
    });
});


