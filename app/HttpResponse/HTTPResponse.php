<?php

namespace App\HttpResponse;

trait HTTPResponse
{
    public function success($data = null , $message = "request was successfully" , $code = 200){
        return response([
           'data' => $data,
           'message' => $message,
        ] , $code);
    }

    public function error($message = null , $code = null){
        return response([
            'message' => $message,
        ] , $code);
    }

    public function serverError(){
        return response([
            'message' => __('messages.v1.error.server_error'),
        ] , 500);
    }
}
