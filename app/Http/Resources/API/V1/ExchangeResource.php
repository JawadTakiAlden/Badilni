<?php

namespace App\Http\Resources\API\V1;

use App\HelperMethods\HelperMethod;
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

        $owner_user = json_decode($this->owner_user);

        $exchanged_item = json_decode($this->exchanged_item);

        $my_item = json_decode($this->my_item);

        $baseData = [
            'id' => $this->id,
            'exchange_type' => $this->exchange_type,
            'price' => $this->price,
            'extra_money' => $this->extra_money,
            'offer_money' => $this->offer_modey,
            'status' => $this->status,
            'exchanged_user_id' => $this->exchange_user_id,
            'owner_user_id' => $this->owner_user_id,
            'exchanged_item' => [
                'title' => $exchanged_item->title,
                'description' =>  $exchanged_item->description,
                'image' => $exchanged_item->image,
                'category' => HelperMethod::extractValueDependOnLanguageOfRequestUser($exchanged_item->category_name)
            ],
            'my_item' => $my_item ? [
                'title' => $my_item->title,
                'description' =>  $my_item->description,
                'image' => $my_item->image,
                'category' => HelperMethod::extractValueDependOnLanguageOfRequestUser($my_item->category_name)
            ] : null,
            'exchange_user' => [
                'id' => $exchange_user->id,
                'name' => $exchange_user->name,
                'image' => $exchange_user->image,
                'gender' => $exchange_user->gender,
                'phone' => $this->exchangeUser->phone,
                'location' => HelperMethod::extractValueDependOnLanguageOfRequestUser($exchange_user->location)
            ],
            'owner_user' => [
                'id' => $owner_user->id,
                'name' => $owner_user->name,
                'image' => $owner_user->image,
                'gender' => $owner_user->gender,
                'phone' => $this->ownerUser->phone,
                'location' => HelperMethod::extractValueDependOnLanguageOfRequestUser($owner_user->location)
            ],
        ];
        return $baseData;
    }
}
