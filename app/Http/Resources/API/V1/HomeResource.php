<?php

namespace App\Http\Resources\API\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HomeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'sections' => SectionResource::collection($this->resource['sections']),
            'categories' => CategoryResource::collection($this->resource['categories']),
            'sliders' => SliderResource::collection($this->resource['sliders'])
        ];
    }
}
