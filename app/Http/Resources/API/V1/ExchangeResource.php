<?php

namespace App\Http\Resources\API\V1;

use App\Models\Item;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExchangeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $exchange_user = json_decode($this->exchange_user);
        $exchange_user = User::where('id' , $exchange_user['id'])->first();
        $owner_user = json_decode($this->owner_user);
        $owner_user = User::where('id' , $owner_user['id'])->first();
        $exchanged_item = json_decode($this->exchanged_item);
        $exchanged_item = Item::where('id' , $exchanged_item['id'])->first();

        if ($this->my_item) {
            $my_item = json_decode($this->my_item);
            $my_item = Item::where('id' , $my_item['id'])->first();
        }

        return [
            'id' => $this->id,
            'exchange_user' => UserResource::make($exchange_user),
            'owner_user' => UserResource::make($owner_user),
            'my_item' => $this->my_item ? ItemResource::make($my_item) : null,
            'exchanged_item' => ItemResource::make($exchanged_item),
            'exchange_user_id' => $this->exchange_user_id,
            'owner_user_id' => $this->owner_user_id,
            'extra_money' => $this->extra_money,
            'offer_money' => $this->offer_modey,
            'price' => $this->price,
            'exchange_type' => $this->exchange_type
        ];
    }
}
