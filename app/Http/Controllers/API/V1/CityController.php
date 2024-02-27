<?php

namespace App\Http\Controllers\API\V1;

use App\HelperMethods\HelperMethod;
use App\Http\Controllers\Controller;
use App\Http\Requests\API\V1\City\CreateCityRequest;
use App\Http\Requests\API\V1\City\UpdateCityRequest;
use App\Http\Resources\API\V1\CityResource;
use App\HttpResponse\HTTPResponse;
use App\Models\City;
use Illuminate\Http\Request;

class CityController extends Controller
{
    use HTTPResponse;
    private HelperMethod $helpers;

    public function __construct()
    {
        $this->helpers = new HelperMethod();
    }

    private function getCityByID($couuntryID , array $with = []){
        return City::with($with)->where('id' , $couuntryID)->first();
    }

    public function getAll(){
        try {
            $countries = City::all();
            return  $this->success(CityResource::collection($countries));
        }catch (\Throwable $th){
            return $this->helpers->getErrorResponse($th);
        }
    }

    public function getActive(){
        try {
            $countries = City::where('is_active' , true)->orderBy('sort')->get();
            return  $this->success(CityResource::collection($countries));
        }catch (\Throwable $th){
            return $this->helpers->getErrorResponse($th);
        }
    }

    public function createCity(CreateCityRequest $request){
        try {
            $country = City::create($request->only(['title','is_active', 'country_id']));
            return $this->success(CityResource::make($country),__('messages.v1.city.create_city'));
        }catch (\Throwable $th){
            return $this->helpers->getErrorResponse($th);
        }
    }

    public function updateCity(UpdateCityRequest $request , $cityID){
        try {
            $city = $this->getCityByID($cityID);
            if (!$city){
                return $this->helpers->getNotFoundResourceRespone(__("messages.v1.city.city_not_found"));
            }
            $city->update($request->only(['title','is_active', 'country_id']));
            return $this->success($city , __("messages.v1.city.update_city"));
        }catch (\Throwable $th){
            return $this->helpers->getErrorResponse($th);
        }
    }

    public function delete($cityID){
        try {
            $city = $this->getCityByID($cityID);
            if (!$city){
                return $this->helpers->getNotFoundResourceRespone(__("messages.v1.city.city_not_found"));
            }
            $city->delete();
            return $this->success($city , __("messages.v1.country.delete_city"));
        }catch (\Throwable $th){
            return $this->helpers->getErrorResponse($th);
        }
    }
}
