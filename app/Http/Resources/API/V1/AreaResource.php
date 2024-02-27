<?php

namespace App\Http\Resources\API\V1;

use App\HelperMethods\HelperMethod;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AreaResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' =>$this->id,
            'title' => HelperMethod::extractValueDependOnLanguageOfRequestUser($this->title),
            'is_active' => boolval($this->is_active),
            'city' => HelperMethod::extractValueDependOnLanguageOfRequestUser($this->city->title),
        ];
    }
}
