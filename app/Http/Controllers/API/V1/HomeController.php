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
            $data = collect()->add([
               'sections' =>  $sections,
                'categories' => $categories,
                'sliders' => $sliders
            ]);

            return $data;
        }catch (\Throwable $th){
            return $this->helper->getErrorResponse($th);
        }
    }
}
