<?php

namespace App\Http\Controllers\API\V1;

use App\HelperMethods\HelperMethod;
use App\Http\Controllers\Controller;
use App\Http\Requests\API\V1\Country\CreateCounteyRequest;
use App\Http\Requests\API\V1\Country\UpdateCountryRequest;
use App\Http\Resources\API\V1\CountryResource;
use App\HttpResponse\HTTPResponse;
use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CountryController extends Controller
{
    use HTTPResponse;
    private HelperMethod $helpers;

    public function __construct()
    {
        $this->helpers = new HelperMethod();
    }

    private function getCountryByID($couuntryID , array $with = []){
        return Country::with($with)->where('id' , $couuntryID)->first();
    }

    public function getAll(){
        try {
            $countries = Country::all();
            return  $this->success(CountryResource::collection($countries));
        }catch (\Throwable $th){
            return $this->helpers->getErrorResponse($th);
        }
    }

    public function getActive(){
        try {
            $countries = Country::where('is_active' , true)->get();
            return  $this->success(CountryResource::collection($countries));
        }catch (\Throwable $th){
            return $this->helpers->getErrorResponse($th);
        }
    }

    public function createCountry(CreateCounteyRequest $request){
        try {
            $count = Country::count();
            $is_default = $request->is_default;
            if ($count === 0 || !$is_default){
                $is_default = true;
            }
            DB::beginTransaction();
            if ($count > 0 && $is_default){
                Country::where('is_active' , true)->get()->map(function ($country) {
                   $country->update([
                       'is_default' => false
                   ]) ;
                });
            }
            $country = Country::create(array_merge($request->only(['name', 'title', 'flag', 'state_key', 'is_active']) ,['is_default' => $is_default]));
            DB::commit();
            return $this->success(CountryResource::make($country),__('messages.v1.country.crete_country'));
        }catch (\Throwable $th){
            DB::commit();
            return $this->helpers->getErrorResponse($th);
        }
    }

    public function updateCountry(UpdateCountryRequest $request , $countryID){
        try {
            $country = $this->getCountryByID($countryID);
            if (!$country){
                return $this->helpers->getNotFoundResourceRespone(__("messages.v1.country.country_not_found"));
            }
            if ($request->is_defautl && !$country->is_default){
                Country::where('id' , $countryID)->where('is_active' , true)->get()->map(function ($country) {
                    $country->update([
                        'is_default' => false
                    ]) ;
                });
            }
            $country->update($request->only(['name', 'title', 'flag', 'state_key', 'is_active', 'is_default']));
            return $this->success($country , __("messages.v1.country.update_country"));
        }catch (\Throwable $th){
            return $this->helpers->getErrorResponse($th);
        }
    }

    public function delete($countryID){
        try {
            $country = $this->getCountryByID($countryID);
            if (!$country){
                return $this->helpers->getNotFoundResourceRespone(__("messages.v1.country.country_not_found"));
            }
            $country->delete();
            return $this->success($country , __("messages.v1.country.delete_country"));
        }catch (\Throwable $th){
            return $this->helpers->getErrorResponse($th);
        }
    }
}
