<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\V1\Item\ExchangeItemRequest;
use App\Http\Resources\API\V1\ExchangeResource;
use App\HttpResponse\HTTPResponse;
use App\Models\Exchange;
use App\Models\Item;
use App\Models\Notification;
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
            $exhanged_item = Item::with(['images','user'])->where('id',$request->exchanged_item)->first();
            if ($exhanged_item->user->id === $request->user()->id){
                return $this->error(__('messages.error.exchange_with_yourself' , 422));
            }
            DB::beginTransaction();
            if ($exchange_type === 'chas'){
                $data = [
                  'exchanged_item' => json_encode($exhanged_item),
                  'exchange_type' => $exchange_type,
                  'exchange_user_id' => auth()->user()->id,
                  'owner_user_id' => $exhanged_item->user->id,
                  'exchange_user' => auth()->user(),
                  'owner_user' => $exhanged_item->user,
                ];
                $data = array_merge($data, $request->only(['price']));
                $exchange = Exchange::create($data);
                Notification::create([
                   'title' => json_encode([
                       "en" => "title of notification",
                       "ar" => "عنوان الاشعار"
                   ]),
                    "body" => json_encode([
                        "en" => "body of notification",
                        "ar" => "موضوع الاشعار"
                    ]),
                    'notified_user_id' => $exhanged_item->user->id
                ]);
                DB::commit();
                return $this->success($exchange , __('exchanged_asked_successfully'));
            }else if ($exchange_type === 'change'){
                $my_item = Item::with(['images' , 'user'])->where('id' , $request->my_item)->first();
                $data = [
                    'exchanged_item' => json_encode($exhanged_item),
                    'my_item' => json_encode($my_item),
                    'exchange_type' => $exchange_type,
                    'exchange_user_id' => auth()->user()->id,
                    'owner_user_id' => $exhanged_item->user->id,
                    'exchange_user' => auth()->user(),
                    'owner_user' => $exhanged_item->user,
                ];
                $data = array_merge($data, $request->only(['extra_money' , 'offer_money']));
                $exchange = Exchange::create($data);
                Notification::create([
                    'title' => json_encode([
                        "en" => "title of notification",
                        "ar" => "عنوان الاشعار"
                    ]),
                    "body" => json_encode([
                        "en" => "body of notification",
                        "ar" => "موضوع الاشعار"
                    ]),
                    'notified_user_id' => $exhanged_item->user->id
                ]);
                DB::commit();
                return $this->success($exchange , __('exchanged_asked_successfully'));
            }else{
                DB::rollBack();
                return $this->error(__('messages.error.unknown_exchange_type'),422);
            }
        }catch (\Throwable $throwable){
            DB::rollBack();
            return $this->serverError();
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
        }
    }

    public function acceptExchange($exchangeID){
        try {
            $exchange = Exchange::where('id' , $exchangeID)->first();
            if (!$exchange){
                return $this->error(__('messages.exchange_not_found' , 404));
            }
            if ($exchange->owner_user_id !== auth()->user()->id){
                return $this->error(__('cant_accept_other_exchanges') , 403);
            }
            $exchange->update([
                'status' => 'accepted'
            ]);

            return $this->success(ExchangeResource::make($exchange) , __('messages.exchange_accepted'));
        }catch (\Throwable $th){
            return $this->serverError();
        }
    }

    public function rejectExchange($exchangeID){
        try {
            $exchange = Exchange::where('id' , $exchangeID)->first();
            if (!$exchange){
                return $this->error(__('messages.exchange_not_found' , 404));
            }
            if ($exchange->owner_user_id !== auth()->user()->id){
                return $this->error(__('cant_reject_other_exchanges') , 403);
            }
            $exchange->update([
                'status' => 'rejected'
            ]);

            return $this->success(ExchangeResource::make($exchange) , __('messages.exchange_rejected'));
        }catch (\Throwable $th){
            return $this->serverError();
        }
    }


    public function cancelExchange($exchangeID){
        try {
            $exchange = Exchange::where('id' , $exchangeID)->first();
            if (!$exchange){
                return $this->error(__('messages.exchange_not_found' , 404));
            }
            if ($exchange->owner_user_id !== auth()->user()->id){
                return $this->error(__('cant_cancel_other_exchanges') , 403);
            }
            $exchange->delete();
            return $this->success(ExchangeResource::make($exchange) , __('messages.exchange_canceled'));
        }catch (\Throwable $th){
            return $this->serverError();
        }
    }
}
