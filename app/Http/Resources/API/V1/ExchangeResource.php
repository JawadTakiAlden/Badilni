<?php

namespace App\Http\Resources\API\V1;

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
        return [
            'id' => $this->id,
            'exchange_user' => UserResource::make(json_decode($this->exchange_user)),
            'owner_user' => UserResource::make(json_decode($this->owner_user)),
            'my_item' => ItemResource::make(json_decode($this->my_item)),
            'exchanged_item' => ItemResource::make(json_decode($this->exchanged_item)),
            'exchange_user_id' => $this->exchange_user_id,
            'owner_user_id' => $this->owner_user_id,
            'extra_money' => $this->extra_money,
            'offer_money' => $this->offer_modey,
            'price' => $this->price,
            'exchange_type' => $this->exchange_type
        ];
    }
}
