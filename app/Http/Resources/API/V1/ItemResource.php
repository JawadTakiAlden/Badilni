<?php

namespace App\Http\Resources\API\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ItemResource extends JsonResource
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
            'title' => $this->title,
            'description' => $this->description,
            'area' => AreaResource::make($this->area),
            'sub_category' => CategoryResource::make($this->subCategory),
            'category' => $this->subCategory->category,
            'status' => $this->status,
            'is_active' => boolval($this->is_active),
            'images' => ItemImageResource::collection($this->images),
        ];
    }
}
