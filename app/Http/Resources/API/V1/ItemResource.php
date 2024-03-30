<?php

namespace App\Http\Resources\API\V1;

use App\HelperMethods\HelperMethod;
use App\Models\ItemImage;
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
        $base = [
            'id' => $this->id,
            'title' => $this->title,
            'price' => $this->price,
            'category_name' => HelperMethod::extractValueDependOnLanguageOfRequestUser($this->category->title),
            'default_image' => ItemImageResource::make($this->images->where('is_default' , true)->first())
        ];

        if ($request->query('page') === 'widthDetails'){
            $base = array_merge($base , [
               'images' => $this->images,
                'description' => $this->description,
            ]);
        }

        return $base;
    }
}
