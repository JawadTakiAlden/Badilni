<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\V1\Item\ExchangeItemRequest;
use App\HttpResponse\HTTPResponse;
use App\Models\Exchange;
use App\Models\Item;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExchangeController extends Controller
{
    use HTTPResponse;
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
                  'exchange_type' => $exchange_type
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
                    'exchange_type' => $exchange_type
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
}
