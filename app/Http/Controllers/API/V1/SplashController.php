<?php

namespace App\Http\Controllers\API\V1;

use App\HelperMethods\HelperMethod;
use App\Http\Controllers\Controller;
use App\Http\Resources\API\V1\SliderResource;
use App\HttpResponse\HTTPResponse;
use App\Models\Slider;
use Illuminate\Http\Request;

class SplashController extends Controller
{
    use HTTPResponse;
    private HelperMethod $helpers;

    public function __construct()
    {
        $this->helpers = new HelperMethod();
    }
    public function getSplashSlides(){
        try {
            $splashSlides = Slider::where('type' , 'splash')->orderBy('sort')->get();
            return $this->success(SliderResource::collection($splashSlides));
        }catch (\Throwable $th){
            return $this->helpers->getErrorResponse($th);
        }
    }
}
