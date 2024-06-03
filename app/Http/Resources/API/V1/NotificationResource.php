<?php

namespace App\Http\Resources\API\V1;

use App\HelperMethods\HelperMethod;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NotificationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'title' =>$this->title,
            'body' => $this->body,
            'created_at' => Carbon::parse($this->created_at)->format('l F Y')
        ];
    }
}
