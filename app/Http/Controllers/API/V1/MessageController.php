<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\V1\Message\SendMessageRequest;
use App\Http\Resources\API\V1\MessageResource;
use App\HttpResponse\HTTPResponse;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    use HTTPResponse;
    public function store(SendMessageRequest $request){
        try {
            $message = Message::create(array_merge($request->only(['to' , 'body']) , ['from' => $request->user()->id]));
            return $this->success(MessageResource::make($message));
        }catch (\Throwable $th){
            return $this->serverError();
        }
    }

    public function getMyConversation() {
        try {

        }catch (\Throwable $th){
            return $this->serverError();
        }
    }
}
