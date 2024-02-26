<?php

namespace App\Http\Controllers\API\V1;

use App\HelperMethods\HelperMethod;
use App\Http\Controllers\Controller;
use App\Http\Requests\API\V1\Auth\ChangePasswordRequest;
use App\Http\Requests\API\V1\Auth\ForgetPasswordChangeRequest;
use App\Http\Requests\API\V1\Auth\ForgetPasswordRequest;
use App\Http\Requests\API\V1\Auth\ForgetPasswordVerifyCodeRequest;
use App\Http\Requests\API\V1\Auth\LoginRequest;
use App\Http\Requests\API\V1\Auth\LogoutRequest;
use App\Http\Requests\API\V1\Auth\ResendForgetPasswordVerificationCodeRequest;
use App\Http\Requests\API\V1\Auth\ResendVerifyCodeRequest;
use App\Http\Requests\API\V1\Auth\SingUpRequest;
use App\Http\Requests\API\V1\Auth\VerifyEmailRequest;
use App\Http\Resources\API\V1\UserResource;
use App\HttpResponse\HTTPResponse;
use App\Mail\EmailVerification;
use App\Models\User;
use App\Models\UserDevice;
use App\Models\VerificationCode;
use App\Notifications\ResetPasswordNotification;
use App\Types\VerificationCodeType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\Token;

class AuthController extends Controller
{
    use HTTPResponse;
    private HelperMethod $helpers;

    public function __construct()
    {
        $this->helpers = new HelperMethod();
    }

    private function GenerateCode($user , $codeType){
        $code = rand(100000, 999999);
        while (VerificationCode::where('code' , $code)->exists()){
            $code = rand(100000, 999999);
        }
        VerificationCode::create([
            'user_id' => $user->id,
            'code' => $code,
            'type' => $codeType
        ]);
        return $code;
    }

    private function SendEmail($user , $data){
        \Mail::to($user->email)->send(new EmailVerification($data));
    }

    private function GenerateCodeAndSendEmail($user , $codeType = VerificationCodeType::REGISTRATION_CODE){
        $code = $this->GenerateCode($user , $codeType);
        $data = array("user" => $user, "code" => $code);
        $this->SendEmail($user , $data);
    }

    public function signup(SingUpRequest $request) {
        try {
            DB::beginTransaction();
            $user = User::create($request->only(['name' , 'email' , 'password']));
            $this->GenerateCodeAndSendEmail($user);
            DB::commit();
            return $this->success([
                'user' => $user,
            ] , __('messages.v1.auth.create_account'));
        }catch (\Throwable $th){
            DB::rollBack();
            return $this->helpers->getErrorResponse($th);
        }
    }


    public function login(LoginRequest $request){
        try {

            $user = User::where('email' , $request->email)->first();
            if (!$user){
                return $this->helpers->getNotFoundResourceRespone(__('messages.v1.auth.account_not_found'));
            }
            if (!Auth::attempt($request->only(['email' , 'password']))){
                return $this->error(__('messages.v1.auth.login_credentials_error') , 401);
            }
            if (!$user->is_active){
                return $this->error(__('messages.v1.account.account_disabled') , 403);
            }
            if ($user->email_verified_at == null) {
                return $this->error(__('messages.v1.auth.account_not_verified') , 403);
            }
          $token = $user->createToken("UserToken")->accessToken;
            $currentDevice = $user->userDevices->where('device_uuid' , $request->device_uuid)->first();
            if ($currentDevice){
                $currentDevice->update([
                    "device_type" => $request->device_type,
                    "device_model" => $request->device_model,
                    "notification_token" => $request->notification_token,
                    "auth_token" => $token
                ]);
            }else{
                UserDevice::create([
                   'user_id' => $user->id,
                    "device_type" => $request->device_type,
                    "device_uuid" => $request->device_uuid,
                    "device_model" => $request->device_model,
                    "notification_token" => $request->notification_token,
                    "auth_token" => $token
                ]);
            }
            return $this->success([
                'user' => UserResource::make($user),
                "access_token" => $token
            ] , __('messages.v1.auth.login_successfully' , ["user_name" => $user->name]));
        }catch (\Throwable $th){
            return $this->helpers->getErrorResponse($th);
        }
    }

    public function changePassword(ChangePasswordRequest $request){
        try {
            $user = $request->user();
            if (Hash::check($request->current_password , $user->password) || $user->password === null){
                $user->password = bcrypt($request->new_password);
                $user->update();
                $devices = $user->userDevices->where('device_uuid' , "!=" , $request->device_uuid);
                foreach ($devices as $device){
                    $token = Token::where('id' , $devices->auth_token);
                    if ($token){
                        $token->revoke();
                    }
                    $device->delete();
                }
            }
        }catch (\Throwable $th){
            return $this->helpers->getErrorResponse($th);
        }
    }

    public function logout(LogoutRequest $request){
        try {
            $userDevice = UserDevice::where('device_uuid' , $request->deivce_uuid)
                ->orWhere('auth_token' , $request->bearerToken())
                ->first();
            if ($userDevice){
                $userDevice->delete();
            }
            $request->user()->token()->revoke();
            return $this->success(null , __("messages.v1.auth.logout_successfully"));
        }catch (\Throwable $th){
            return $this->helpers->getErrorResponse($th);
        }
    }

    public function verifyEmail(VerifyEmailRequest $request){
        try {
            DB::beginTransaction();
            $user = User::where('email', $request->email)->first();
            if (!$user){
                return $this->helpers->getNotFoundResourceRespone(__('messages.v1.account.account_not_found'));
            }
            $code = VerificationCode::where('user_id', $user->id)
                ->where('used', false)
                ->where('type', VerificationCodeType::REGISTRATION_CODE)
                ->where('code' , strval($request->code))
                ->first();
            if (!$code){
                return $this->error(__('messages.v1.auth.code_incorrect'), 400);
            }
            $user->email_verified_at = now();
            $user->update();
            $user->verificationCodes->where('type' , VerificationCodeType::REGISTRATION_CODE)->map(fn($verificationCode) =>
                $verificationCode->delete()
            );
            DB::commit();
            return $this->success([
                "user" => UserResource::make($user)
            ] , __('messages.v1.auth.verified_successfully'));
        }catch (\Throwable $th){
            DB::rollBack();
            return $this->helpers->getErrorResponse($th);
        }
    }

    public function sendVerifyCode(ResendVerifyCodeRequest $request){
        try {
            $user = User::where('email' , $request->email)->first();
            if (!$user){
                return $this->helpers->getNotFoundResourceRespone(__('messages.v1.account.account_not_found'));
            }
            $this->GenerateCodeAndSendEmail($user);
            return $this->success([
                'user' => UserResource::make($user),
            ] , __('messages.v1.auth.resend_code'));
        }catch (\Throwable $th){
            return $this->helpers->getErrorResponse($th);
        }
    }


    public function forgetPassword(ForgetPasswordRequest $request){
        try {
            $user = User::where('email' , $request->email)->first();
            if (!$user){
                return $this->helpers->getNotFoundResourceRespone(__('messages.v1.account.account_not_found'));
            }
            $code = $this->GenerateCode($user , VerificationCodeType::RESET_PASSWORD_CODE);
            $user->notify(new ResetPasswordNotification($code));
            return $this->success([
              'user' => UserResource::make($user),
            ] , __("messages.v1.auth.reset_password"));
        }catch (\Throwable $th){
            return $this->helpers->getErrorResponse($th);
        }
    }

    public function resendForgetPasswordCode(ResendForgetPasswordVerificationCodeRequest $request){
        try {
            $user = User::where('email' , $request->email)->first();
            if (!$user){
                return $this->helpers->getNotFoundResourceRespone(__('messages.v1.account.account_not_found'));
            }
            $code = $this->GenerateCode($user , VerificationCodeType::RESET_PASSWORD_CODE);
            $user->notify(new ResetPasswordNotification($code));
            return $this->success([
                'user' => UserResource::make($user),
            ] , __('messages.v1.auth.resend_code'));
        }catch (\Throwable $th){
            return $this->helpers->getErrorResponse($th);
        }
    }

    public function forgetPasswordVerifyCode(ForgetPasswordVerifyCodeRequest $request){
        try {
            DB::beginTransaction();
            $user = User::where('email', $request->email)->first();
            if (!$user){
                return $this->helpers->getNotFoundResourceRespone(__('messages.v1.account.account_not_found'));
            }
            $code = VerificationCode::where('code' , strval($request->code))
                ->where('user_id' , $user->id)
                ->where('used' , false)
                ->where('type' , VerificationCodeType::RESET_PASSWORD_CODE)
                ->first();
            if (!$code){
                return $this->error(__('messages.v1.auth.code_incorrect'), 400);
            }
            $user->verificationCodes
                ->where('type' , VerificationCodeType::RESET_PASSWORD_CODE)
                ->map(fn($verificationCode) =>
                    $verificationCode->delete()
                );
            DB::commit();
            return $this->success([
                "user" => UserResource::make($user)
            ] , __('messages.v1.auth.forget_password_verified'));
        }catch (\Throwable $th){
            DB::rollBack();
            return $this->helpers->getErrorResponse($th);
        }
    }

    public function forgetPasswordChange(ForgetPasswordChangeRequest $request){
        try {
            $user = User::where('email', $request->email)->first();
            if (!$user){
                return $this->helpers->getNotFoundResourceRespone(__('messages.v1.account.account_not_found'));
            }
            $user->update($request->only(['password']));
            return $this->success(UserResource::make($user) , __('messages.v1.auth.password_changed'));
        }catch (\Throwable $th){
            return $this->helpers->getErrorResponse($th);
        }
    }
}
