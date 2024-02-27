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
            'area' => $this->area,
            'sub_category' => $this->subCategory,
            'category' => $this->subCategory->category,
            'is_active' => $this->is_active,
            'images' => $this->images,
        ];
    }
}
