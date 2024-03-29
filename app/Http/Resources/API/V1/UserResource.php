<?php

namespace App\Http\Resources\API\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'email' => $this->email,
            'name' => $this->name,
            'phone' => $this->phone,
            'image' => $this->image ? asset($this->image) : null,
            'birthdate' => $this->birthdate,
            'gender' => $this->gender,
            'language' => $this->language,
            'un_read_notification' => $this->unReadNotification()
        ];
    }
}
