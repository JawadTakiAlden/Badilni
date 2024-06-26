<?php

namespace App\Http\Controllers\API\V1;

use App\HelperMethods\HelperMethod;
use App\Http\Controllers\Controller;
use App\Http\Requests\API\V1\Item\ExchangeItemRequest;
use App\Http\Resources\API\V1\ExchangeResource;
use App\HttpResponse\HTTPResponse;
use App\Models\Exchange;
use App\Models\Item;
use App\Models\Notification;
use App\Notifications\FirebaseNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExchangeController extends Controller
{
    use HTTPResponse;

    public function getAllExchangeOffers(){
        try {
            $exchanges = Exchange::all();
            return $this->success(ExchangeResource::collection($exchanges));
        }catch (\Throwable $th){
            return $this->serverError();
        }
    }

    public function exchangeItems(ExchangeItemRequest $request){
        try {
            $exchange_type = $request->exchange_type;
            $exhanged_item = Item::with(['category','images','user'])->where('id',$request->exchanged_item)->first();
            if (!$exhanged_item){
                return $this->error(__('messages.v1.items.item_not_found') , 422);
            }
            if ($exhanged_item->user_id === $request->user()->id){
                return $this->error(__('messages.v1.exchange.exchange_with_yourself') , 422);
            }
            DB::beginTransaction();
            $owner_user = $exhanged_item->user;
            $exchange_user = $request->user();
            $data = [
                'exchanged_item' => json_encode([
                    'id' => $exhanged_item->id,
                    'image' => $exhanged_item->images,
                    'title' => $exhanged_item->title,
                    'description' => $exhanged_item->description,
                    'category_name' => $exhanged_item->category->title
                ]),
                'exchange_type' => $exchange_type,
                'exchange_user_id' => $exchange_user->id,
                'owner_user_id' => $exhanged_item->user_id,
                'exchange_user' => json_encode([
                    'id' => $exchange_user->id,
                    'name' => $exchange_user->name,
                    'image' => $exchange_user->image,
                    'gender' => $exchange_user->gender,
                    'location' => $exchange_user->country?->title
                ]),
                'owner_user' => json_encode([
                    'id' => $owner_user->id,
                    'name' => $owner_user->name,
                    'image' => $owner_user->image,
                    'gender' => $owner_user->gender,
                    'location' => $owner_user->country?->title
                ]),
            ];
            if ($exchange_type === 'cash'){
                $data = array_merge($data, $request->only(['price']));
            }
            else if ($exchange_type === 'change'){
                $my_item = Item::with(['images' , 'user'])->where('id' , $request->my_item)->first();
                $data = array_merge($data, $request->only(['extra_money' , 'offer_money']));
                $data = array_merge($data, [
                    'my_item' => json_encode([
                        'id' => $my_item->id,
                        'image' => $my_item->images,
                        'title' => $my_item->title,
                        'description' => $my_item->description,
                        'category_name' => $my_item->category->title
                    ]),
                ]);
            }else{
                DB::rollBack();
                return $this->error(__('messages.v1.exchange.unknown_exchange_type'),422);
            }
            $exchange = Exchange::create($data);
            $notification = Notification::create([
                'title' =>  "new exchange send for you",
                "body" => $owner_user->name . " ask to exchange ".$exhanged_item->title . ' ,see more details about the request',
                'notified_user_id' => $owner_user->id
            ]);
            $firebaseNotification = new FirebaseNotification();
            $firebaseNotification->BasicSendNotification($notification->title , $notification->body , $notification->user->userDevices->pluck('notification_token') , [
                'type' => 'exchange',
                'exchange_id' => $exchange->id
            ]);
            DB::commit();
            return $this->success(null , __('messages.exchange_successfully_requested'));
        }catch (\Throwable $throwable){
            DB::rollBack();
//            return $this->serverError();
            return $this->error([$throwable->getMessage() , $throwable->getFile() , $throwable->getLine()] , 500);
        }
    }

    public function getExchangeOffers(){
        try {
            $filter = \request('exchange_filter');
            if ($filter === 'received'){
                $exchanges = Exchange::where('owner_user_id' , auth()->user()->id)->orderBy('created_at' , 'desc')->get();
                return $this->success(ExchangeResource::collection($exchanges));
            }else if ($filter === 'send'){
                $exchanges = Exchange::where('exchange_user_id' , auth()->user()->id)->orderBy('created_at' , 'desc')->get();
                return $this->success(ExchangeResource::collection($exchanges));
            }else{
                return $this->error(__('messages.error.unknown_exchange_filter'),422);
            }
        }catch (\Throwable $throwable){
            return $this->serverError();
//            return [$throwable->getMessage() , $throwable->getFile() , $throwable->getLine()];
        }
    }

    public function acceptExchange($exchangeID){
        try {
            DB::beginTransaction();
            $exchange = Exchange::where('id' , $exchangeID)->first();
            if (!$exchange){
                return $this->error(__('messages.v1.exchange.exchange_not_found' , 404));
            }
            if ($exchange->owner_user_id !== auth()->user()->id){
                return $this->error(__('messages.v1.exchange.permission_denied') , 403);
            }

            $exchange->update([
                'status' => 'accepted'
            ]);
            $exchangedItemID = json_decode($exchange->exchanged_item)->id;
            Item::where('id' , $exchangedItemID)->update([
                'flag' => "exchanged"
            ]);
            $notification = Notification::create([
                'title' =>  "your exchange accepted !",
                "body" => "one of your exchanges request accepted by ts owner",
                'notified_user_id' => $exchange->exchange_user_id
            ]);
            $firebaseNotification = new FirebaseNotification();
            $firebaseNotification->BasicSendNotification($notification->title , $notification->body , $notification->user->userDevices->pluck('notification_token') , [
                'type' => 'accept_exchange',
                'exchange_id' => $exchangeID
            ]);
            DB::commit();
            return $this->success(ExchangeResource::make($exchange) , __('messages.exchange_accepted'));
        }catch (\Throwable $th){
            DB::rollBack();
            return $this->serverError();
        }
    }

    public function rejectExchange($exchangeID){
        try {
            $exchange = Exchange::where('id' , $exchangeID)->first();
            if (!$exchange){
                return $this->error(__('messages.v1.exchange.exchange_not_found' , 404));
            }
            if ($exchange->owner_user_id !== auth()->user()->id){
                return $this->error(__('messages.v1.exchange.permission_denied') , 403);
            }
            $exchange->update([
                'status' => 'rejected'
            ]);
            Notification::create([
                'title' =>  "notification rejected",
                "body" => "from ahmad notification to reject the exchange ",
                'notified_user_id' => $exchange->exchange_user_id
            ]);
            return $this->success(null , __('messages.v1.exchange.exchange_rejected'));
        }catch (\Throwable $th){
            return $this->serverError();
        }
    }


    public function cancelExchange($exchangeID){
        try {
            $exchange = Exchange::where('id' , $exchangeID)->first();
            if (!$exchange){
                return $this->error(__('messages.v1.exchange.exchange_not_found' , 404));
            }
            if ($exchange->exchange_user_id !== auth()->user()->id){
                return $this->error(__('messages.v1.exchange.permission_denied') , 403);
            }
            $exchange->delete();
            return $this->success(null , __('messages.v1.exchange.exchange_canceled'));
        }catch (\Throwable $th){
            return $this->serverError();
        }
    }
}
