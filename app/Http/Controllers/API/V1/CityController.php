<?php

namespace App\Http\Controllers\API\V1;

use App\HelperMethods\HelperMethod;
use App\Http\Controllers\Controller;
use App\Http\Requests\API\V1\City\CreateCityRequest;
use App\Http\Requests\API\V1\City\UpdateCityRequest;
use App\Http\Resources\API\V1\CityResource;
use App\HttpResponse\HTTPResponse;
use App\Models\Area;
use App\Models\City;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
            $cities = City::all();
            return  $this->success(CityResource::collection($cities));
        }catch (\Throwable $th){
            return $this->helpers->getErrorResponse($th);
        }
    }

    public function getActive(){
        try {
            $cities = City::where('is_active' , true)->get();
            return  $this->success(CityResource::collection($cities));
        }catch (\Throwable $th){
            return $this->helpers->getErrorResponse($th);
        }
    }

    public function createCity(CreateCityRequest $request){
        try {
            DB::beginTransaction();
            $city = City::create($request->only(['title','is_active', 'country_id']));
            Area::create([
                'city_id' => $city->id,
                'title' => json_encode([
                    'en' => 'other',
                    'ar' => 'اخرى'
                ])
            ]);
            DB::commit();
            return $this->success(CityResource::make($city),__('messages.v1.city.create_city'));
        }catch (\Throwable $th){
            DB::rollBack();
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
            return $this->success(CityResource::make($city) , __("messages.v1.city.update_city"));
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
            return $this->success(CityResource::make($city) , __("messages.v1.country.delete_city"));
        }catch (\Throwable $th){
            return $this->helpers->getErrorResponse($th);
        }
    }
}
