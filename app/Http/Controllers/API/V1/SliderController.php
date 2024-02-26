<?php

namespace App\Http\Controllers\API\V1;

use App\HelperMethods\HelperMethod;
use App\Http\Controllers\Controller;
use App\Http\Requests\API\V1\Slider\CreateSlideRequest;
use App\Http\Requests\API\V1\Slider\UpdateSlideRequest;
use App\Http\Resources\API\V1\SliderResource;
use App\HttpResponse\HTTPResponse;
use App\Models\Slider;
use Illuminate\Http\Request;

class SliderController extends Controller
{

    use HTTPResponse;
    private HelperMethod $helpers;

    public function __construct()
    {
        $this->helpers = new HelperMethod();
    }

    public function getSlideByID($id , array $with = []){
        return Slider::with($with)->where('id' , $id)->first();
    }

    public function getAllSlider(){
        try {
            $sliders = Slider::orderBy('sort')->get();
            return $this->success(SliderResource::collection($sliders));
        }catch (\Throwable $th){
            return $this->helpers->getErrorResponse($th);
        }
    }

    public function createSlider(CreateSlideRequest $request){
        try {
            $slide = Slider::create($request->only(['is_active' , 'image' , 'title' , 'sort' , 'type']));
            return $this->success(SliderResource::make($slide) , __('messages.v1.slider.create_new_slide'));
        }catch (\Throwable $th){
            return $this->helpers->getErrorResponse($th);
        }
    }

    public function updateSlider(UpdateSlideRequest $request , $slide_id){
        try {
            $slide = $this->getSlideByID($slide_id);
            if (!$slide){
                return $this->helpers->getNotFoundResourceRespone(__('messages.v1.slider.slide_not_found'));
            }
            $slide->update($request->only(['is_active' , 'image' , 'title' , 'sort' , 'type']));
            return $this->success(SliderResource::make($slide) , __('messages.v1.slider.create_slide'));
        }catch (\Throwable $th){
            return $this->helpers->getErrorResponse($th);
        }
    }

    public function deleteSlider($slide_id){
        try {
            $slide = $this->getSlideByID($slide_id);
            if (!$slide){
                return $this->helpers->getNotFoundResourceRespone(__('messages.v1.slider.slide_not_found'));
            }
            $slide->delete();
            return $this->success(SliderResource::make($slide) , __('messages.v1.slider.delete_slide'));
        }catch (\Throwable $th){
            return $this->helpers->getErrorResponse($th);
        }
    }
}
