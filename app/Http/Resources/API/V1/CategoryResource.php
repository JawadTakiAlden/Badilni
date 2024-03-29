<?php

namespace App\Http\Resources\API\V1;

use App\HelperMethods\HelperMethod;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
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
            'title' => HelperMethod::extractValueDependOnLanguageOfRequestUser($this->title),
            'description' => HelperMethod::extractValueDependOnLanguageOfRequestUser($this->description),
            'is_active' => boolval($this->is_active),
            'sort' => $this->sort,
            "image" => $this->image ? asset($this->image) : null,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
