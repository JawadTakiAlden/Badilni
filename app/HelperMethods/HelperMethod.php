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
}
