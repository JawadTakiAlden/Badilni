<?php

namespace App\HelperMethods;

use App\HttpResponse\HTTPResponse;

class HelperMethod
{
    use HTTPResponse;
    public function getErrorResponse($error){
        return $this->error($error->getMessage() , 500);
    }

    public function getNotFoundResourceRespone($message){
        if (!$message){
            $message = "Resource you are requested not found";
        }
        return $this->error($message , 404);
    }

    public static function extractValueDependOnLanguageOfRequestUser($jsonValue){
        if (!$jsonValue){
            return null;
        }
        $languageKey = auth()->user()->language ?? 'en';
        $title = json_decode($jsonValue, true);
        if (isset($title[$languageKey])){
            $titleValue = $title[$languageKey];
        }else{
            $titleValue = $title['en'];
        }
        return $titleValue;
    }

    public function requestUnAuthorizedResponse(){
        return $this->error(__('messages.v1.middleware.unAuthorized'), '403');
    }
}
