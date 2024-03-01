<?php

namespace App\Http\Controllers\API\V1;

use App\HelperMethods\HelperMethod;
use App\Http\Controllers\Controller;
use App\Http\Resources\API\V1\HomeResource;
use App\HttpResponse\HTTPResponse;
use App\Models\Category;
use App\Models\Section;
use App\Models\Slider;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class HomeController extends Controller
{
    use HTTPResponse;
    private HelperMethod $helper;
    public function __construct()
    {
        $this->helper = new HelperMethod();
    }

    public function getHome(){
        try {
            $sections = Section::where('is_active' , true)->get();
            $categories = Category::where('is_active' , true)->orderBy('sort' , 'desc')->get();
            $sliders = Slider::where('is_active' , true)->where('type'  , 'home')->orderBy('sort' , 'desc')->get();
            $collection = new Collection([
                'sections' => $sections,
                'categories' => $categories,
                'sliders' => $sliders
            ]);

            return new HomeResource($collection);
        }catch (\Throwable $th){
            return $this->helper->getErrorResponse($th);
        }
    }
}
