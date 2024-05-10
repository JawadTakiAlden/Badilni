<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\V1\Message\SendMessageRequest;
use App\Http\Resources\API\V1\MessageResource;
use App\HttpResponse\HTTPResponse;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
    public function getMessagesBetween($anotherUserID){
        try {
            DB::beginTransaction();
            $messages = [];
            if (\request()->query('mode') === 'send'){
                $messages = Message::where('from' , \request()->user()->id)->where('to' , $anotherUserID)->get();
            }else if (\request()->query('mode') === 'received'){
                $messages = Message::where('from' , $anotherUserID)->where('to' , \request()->user()->id)->get();
            }
            foreach ($messages  as $message){
                $message->update([
                   'is_read' => true
                ]);
            }
            DB::commit();
            return $this->success(MessageResource::collection($messages));
        }catch (\Throwable $th){
            DB::rollBack();
            return $this->serverError();
        }
    }

//    public function getMyConversation() {
//        try {
//            $userId = \request()->user()->id;
//
//            $messages = Message::where(function ($query) use ($userId) {
//                $query->where('from', $userId)
//                    ->orWhere('to', $userId);
//            })->get();
//
//            $conversations = $messages->groupBy(function ($message) use ($userId) {
//                return $message->from === $userId ? $message->to : $message->from;
//            });
//
//            $conversations = $conversations->map(function ($messageGroup, $userId) {
//                $user = User::find($userId);
//                $messages = $messageGroup->all();
//                return (object) [
//                    'user' => $user,
//                    'messages' => $messages,
//                ];
//            });
//
//            return $this->success($conversations);
//        }catch (\Throwable $th){
//            return $this->serverError();
//        }
//    }
}
