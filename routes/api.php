<?php

use Illuminate\Support\Facades\Route;
Route::group([], function ($router) {
    require base_path('routes/API/V1/v1.php');
});
