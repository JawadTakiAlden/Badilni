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
        $languageKey = auth()->user()->language ?? 'en';
        $title = json_decode($jsonValue, true);
        $titleValue = $title[$languageKey] ?? null;
        return $titleValue;
    }
}
