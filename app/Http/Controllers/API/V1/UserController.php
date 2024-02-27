<?php

namespace App\Http\Controllers\API\V1;

use App\HelperMethods\HelperMethod;
use App\Http\Controllers\Controller;
use App\Http\Requests\API\V1\User\UpdateProfileRequest;
use App\Http\Resources\API\V1\UserResource;
use App\HttpResponse\HTTPResponse;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class UserController extends Controller
{
    use HTTPResponse;
    private HelperMethod $helpers;

    public function __construct()
    {
        $this->helpers = new HelperMethod();
    }

    public function getUserByID($userID , array $with = []){
        return User::with($with)->where('id' , $userID)->first();
    }

    public function getMyProfile(){
        try {
            $user = Auth::user();
            return $this->success(UserResource::make($user));
        }catch (\Throwable $th){
            return $this->helpers->getErrorResponse($th);
        }
    }

    public function getProfileOfUser($userID){
        try {
            $user = $this->getUserByID($userID);
            if (!$user){
                return $this->helpers->getNotFoundResourceRespone(__('messages.v1.account_account_not_found'));
            }
            return $this->success(UserResource::make($user));
        }catch (\Throwable $th){
            return $this->helpers->getErrorResponse($th);
        }
    }

    public function updateProfile(UpdateProfileRequest $request, $userID){
        try {
            $user = $this->getUserByID($userID);
            if (!$user){
                return $this->helpers->getNotFoundResourceRespone(__('messages.v1.account_account_not_found'));
            }
            $currentImage = $user->image;
            $user->update($request->only(['name' , 'phone' , 'image' , 'country_id']));
            if ($request->image && $currentImage){
                File::delete(public_path($currentImage));
            }
            return $this->success(UserResource::make($user));
        }catch (\Throwable $th){
            return $this->helpers->getErrorResponse($th);
        }
    }
}
