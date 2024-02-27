<?php

namespace App\Http\Controllers\API\V1;

use App\HelperMethods\HelperMethod;
use App\Http\Controllers\Controller;
use App\Http\Requests\API\V1\Area\CreateAreaRequest;
use App\Http\Requests\API\V1\Area\UpdateAreaRequest;
use App\Http\Resources\API\V1\AreaResource;
use App\HttpResponse\HTTPResponse;
use App\Models\Area;
use Illuminate\Http\Request;

class AreaController extends Controller
{
    use HTTPResponse;
    private HelperMethod $helpers;

    public function __construct()
    {
        $this->helpers = new HelperMethod();
    }
    private function getAreaByID($areaID , array $with = []){
        return Area::with($with)->where('id' , $areaID)->first();
    }
    public function getAll(){
        try {
            $areas = Area::all();
            return $this->success(AreaResource::collection($areas));
        }catch (\Throwable $th){
            return $this->helpers->getErrorResponse($th);
        }
    }

    public function getActive(){
        try {
            $areas = Area::where('is_active' , true)->get();
            return $this->success(AreaResource::collection($areas));
        }catch (\Throwable $th){
            return $this->helpers->getErrorResponse($th);
        }
    }

    public function createArea(CreateAreaRequest $request){
        try {
            $area = Area::create($request->only(['title' , 'city_id' , 'is_active']));
            return $this->success(AreaResource::make($area) , __('messages.v1.area.create_area'));
        }catch (\Throwable $th){
            return $this->helpers->getErrorResponse($th);
        }
    }

    public function updateArea(UpdateAreaRequest $request , $areaID){
        try {
            $area = $this->getAreaByID($areaID);
            if (!$area){
                return $this->helpers->getNotFoundResourceRespone(__('messages.v1.area.not_found_area'));
            }
            $area->update($request->only(['title' , 'city_id' , 'is_active']));
            return $this->success(AreaResource::make($area) , __('messages.v1.area.update_area'));
        }catch (\Throwable $th){
            return $this->helpers->getErrorResponse($th);
        }
    }

    public function delete($areaID){
        try {
            $area = $this->getAreaByID($areaID);
            if (!$area){
                return $this->helpers->getNotFoundResourceRespone(__('messages.v1.area.not_found_area'));
            }
            $area->delete();
            return $this->success(AreaResource::make($area) , __('messages.v1.area.delete_area'));
        }catch (\Throwable $th){
            return $this->helpers->getErrorResponse($th);
        }
    }
}
