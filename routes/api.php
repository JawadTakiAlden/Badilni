<?php

use Illuminate\Support\Facades\Route;
Route::group([], function ($router) {
    require base_path('routes/API/v1/v1.php');
});
