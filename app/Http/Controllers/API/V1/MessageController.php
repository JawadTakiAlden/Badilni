<?php

namespace App\Http\Controllers\API\V1;

use App\Events\SendMessageEvent;
use App\Http\Controllers\Controller;
use App\Http\Requests\API\V1\Message\SendMessageRequest;
use App\Http\Resources\API\V1\MessageResource;
use App\HttpResponse\HTTPResponse;
use App\Models\Message;
use Illuminate\Support\Facades\DB;

class MessageController extends Controller
{
    use HTTPResponse;
    public function store(SendMessageRequest $request){
        try {
            $message = Message::create(array_merge($request->only(['to' , 'exchange_id' , 'body']) , ['from' => $request->user()->id]));
            event(new SendMessageEvent($request->exchange_id , $message));
            return $this->success(MessageResource::make($message));
        }catch (\Throwable $th){
//            return $this->serverError();
            return $this->error($th , 500);
        }
    }
    public function getMessages($exchangeID){
        try {
            DB::beginTransaction();
                $messages = Message::where('exchange_id' , $exchangeID)->get();
            Message::where('exchange_id', $exchangeID)
                ->update(['is_read' => true]);
            DB::commit();
            return $this->success(MessageResource::collection($messages));
        }catch (\Throwable $th){
            DB::rollBack();
            return $this->error($th->getMessage() , 500);
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
