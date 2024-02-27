<?php

namespace App\Http\Resources\API\V1;

use App\HelperMethods\HelperMethod;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SliderResource extends JsonResource
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
            'type' => $this->type,
            'image' => asset($this->image),
            'title' => HelperMethod::extractValueDependOnLanguageOfRequestUser($this->title),
            'sort' => intval($this->sort),
            'is_active' => boolval($this->is_active)
        ];
    }
}
