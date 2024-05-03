<?php

use App\Http\Controllers\API\V1\AreaController;
use App\Http\Controllers\API\V1\AuthController;
use App\Http\Controllers\API\V1\CategoryController;
use App\Http\Controllers\API\V1\CityController;
use App\Http\Controllers\API\V1\CountryController;
use App\Http\Controllers\API\V1\ExchangeController;
use App\Http\Controllers\API\V1\HomeController;
use App\Http\Controllers\API\V1\ItemController;
use App\Http\Controllers\API\V1\NotificationController;
use App\Http\Controllers\API\V1\SectionController;
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

        Route::middleware('admin')->group(function (){
            Route::prefix('/users')->group(function (){
                Route::post('/createAdminAccount' , [UserController::class , 'createAdminAccount']);
            });

            Route::prefix('/countries')->group(function (){
                Route::get('/getAll' , [CountryController::class , 'getAll']);
                Route::post('/create' , [CountryController::class , 'createCountry']);
                Route::patch('/update/{countryID}' , [CountryController::class , 'updateCountry']);
                Route::delete('/delete/{countryID}' , [CountryController::class , 'delete']);
            });

            Route::prefix('/slider')->group(function (){
                Route::get('/getAll' , [SliderController::class , 'getAllSlider']);
                Route::post('/create' , [SliderController::class , 'createSlider']);
                Route::post('/update/{slide_id}' , [SliderController::class , 'updateSlider']);
                Route::delete('/delete/{slide_id}' , [SliderController::class , 'deleteSlider']);
            });

            Route::prefix('/cities')->group(function (){
                Route::get('/getAll' , [CityController::class , 'getAll']);
                Route::post('/create' , [CityController::class , 'createCity']);
                Route::patch('/update/{cityID}' , [CityController::class , 'updateCity']);
                Route::delete('/delete/{cityID}' , [CityController::class , 'delete']);
            });

            Route::prefix('/areas')->group(function (){
                Route::get('/getAll' , [AreaController::class , 'getAll']);
                Route::post('/create' , [AreaController::class , 'createArea']);
                Route::patch('/update/{areaID}' , [AreaController::class , 'updateArea']);
                Route::delete('/delete/{areaID}' , [AreaController::class , 'delete']);
            });

            Route::prefix('/categories')->group(function (){
                Route::get('/getAll' , [CategoryController::class , 'getAllCategories']);
                Route::post('/createCategory' , [CategoryController::class , 'createCategory']);
                Route::post('/createSub' , [CategoryController::class , 'createSubCategory']);
                Route::get('/getSubCategoryOfCategory/{categoryID}' , [CategoryController::class , 'getAllSubCategoryOfCategory']);
                Route::post('/update/{category_id}' , [CategoryController::class , 'updateCategory']);
                Route::delete('/delete/{category_id}' , [CategoryController::class , 'deleteCategory']);
            });

            Route::prefix('/items')->group(function (){
                Route::get('/getAll' , [ItemController::class , 'getAll']);
            });

            Route::prefix('/sections')->group(function (){
                Route::get('/getAll' , [SectionController::class , 'getAll']);
                Route::post('/create' , [SectionController::class , 'createSection']);
                Route::patch('/update/{sectionID}' , [SectionController::class , 'editSection']);
                Route::delete('/delete/{sectionID}' , [SectionController::class , 'delete']);
            });

            Route::prefix('/exchangesOffer')->group(function (){
                Route::get('/getAll' , [ExchangeController::class , 'getAllExchangeOffers']);
            });
        });

        Route::prefix('/auth')->group(function (){
            Route::post('/logout', [AuthController::class , 'logout']);
            Route::post('/changePassword', [AuthController::class , 'changePassword']);
        });

        Route::prefix('/users')->group(function (){
            Route::get('/profile' , [UserController::class , 'getMyProfile']);
            Route::get('/profileOf/{userID}' , [UserController::class , 'getProfileOfUser']);
            Route::post('/updateProfile/{userID}' , [UserController::class , 'updateProfile']);
        });

        Route::prefix('/areas')->group(function (){
            Route::get('/getActive' , [AreaController::class , 'getActive']);
        });

        Route::prefix('/countries')->group(function (){
            Route::get('/getActive' , [CountryController::class , 'getActive']);
        });

        Route::prefix('/slider')->group(function (){
            Route::get('/getHome' , [SliderController::class , 'getHomeSlider']);
        });

        Route::prefix('/categories')->group(function (){
            Route::get('/getActive' , [CategoryController::class , 'getActiveCategories']);
            Route::get('/getActiveSubCategoryOfCategory/{categoryID}' , [CategoryController::class , 'getActiveSubCategoryOfCategory']);
        });

        Route::prefix('/sections')->group(function (){
            Route::get('/getActive' , [SectionController::class , 'getActive']);
        });

        Route::get('/home' , [HomeController::class , 'getHome']);

        Route::prefix('/items')->group(function (){
           Route::get('/getActive' , [ItemController::class , 'getActive']);
           Route::get('/getHome' , [ItemController::class , 'getHome']);
           Route::get('/search' , [ItemController::class , 'search']);
           Route::get('/show/{itemID}' , [ItemController::class , 'showItem']);
           Route::get('/myItems' , [ItemController::class , 'myItems']);
           Route::post('/addToFavorite/{itemID}' , [ItemController::class , 'addToFavorite']);
           Route::get('/myFavorites' , [ItemController::class , 'myFavoritesItem']);
           Route::post('/add' , [ItemController::class , 'addItem']);
           Route::post('/edit/{itemID}' , [ItemController::class , 'editItem']);
           Route::delete('/delete/{itemID}' , [ItemController::class , 'deleteItem']);
           Route::post('/exchange' , [ExchangeController::class , 'exchangeItems']);
        });

        Route::prefix('/exchangesOffer')->group(function (){
            Route::get('/get' , [ExchangeController::class , 'getExchangeOffers']);
            Route::patch('/acceptExchange/{exchange}' , [ExchangeController::class , 'acceptExchange']);
            Route::patch('/rejectExchange/{exchange}' , [ExchangeController::class , 'rejectExchange']);
            Route::patch('/cancelExchange/{exchange}' , [ExchangeController::class , 'cancelExchange']);
        });

        Route::prefix('/notification')->group(function (){
            Route::get('/numberOfUnReadNotification' , [NotificationController::class , 'numberOfUnReadNotification']);
            Route::get('/myNotification' , [NotificationController::class , 'myNotification']);
        });

    });
});


