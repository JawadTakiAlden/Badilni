<?php

namespace App\Http\Controllers\API\V1;

use App\Events\SendMessageEvent;
use App\Http\Controllers\Controller;
use App\Http\Requests\API\V1\Message\SendMessageRequest;
use App\Http\Resources\API\V1\MessageResource;
use App\HttpResponse\HTTPResponse;
use App\Models\Exchange;
use App\Models\Message;
use App\Models\User;
use App\Notifications\FirebaseNotification;
use Illuminate\Support\Facades\DB;

class MessageController extends Controller
{
    use HTTPResponse;
    public function store(SendMessageRequest $request){
        try {
            $message = Message::create(array_merge($request->only(['to' , 'exchange_id' , 'body']) , ['from' => $request->user()->id]));
            $exchange = Exchange::where('id' , $request->exchange_id)->first();
            if (!$exchange) {
                return $this->error('Invalid exchange ID.', 404);
            }

            $authorizedUserIds = [$exchange->owner_user_id, $exchange->exchange_user_id];
            if (!in_array($request->user()->id, $authorizedUserIds)) {
                return $this->error('Unauthorized to send message for this exchange.', 403);
            }

            $recipientId = ($exchange->owner_user_id === $request->user()->id)
                ? $exchange->exchange_user_id
                : $exchange->owner_user_id;
            $user = User::where('id' , $recipientId)->first();
            $notificationBody = [
                'body' => $message->body,
                'type' => "message",
                'exchange_id' => $request->exchange_id
            ];
            $firebaseNotification = new FirebaseNotification();
            $firebaseNotification->BasicSendNotification('new message' , $notificationBody , $user->userDevices->pluck('notification_token'));
            event(new SendMessageEvent($request->exchange_id , $recipientId , $message));
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
