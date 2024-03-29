<?php

namespace App\Http\Resources\API\V1;

use App\HelperMethods\HelperMethod;
use App\Types\UserType;
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
//        base data is data required for mobile application
        $base = [
            'id' => $this->id,
            'title' => HelperMethod::extractValueDependOnLanguageOfRequestUser($this->title),
            'description' => HelperMethod::extractValueDependOnLanguageOfRequestUser($this->description),
            "image" => $this->image ? asset($this->image) : null,
        ];

        if ($request->user()->type === UserType::ADMIN){
            $base = array_merge($base , [
                'is_active' => boolval($this->is_active),
                'sort' => $this->sort,
                'created_at' => $this->created_at,
                'updated_at' => $this->updated_at,
            ]);
        }
        return $base;
    }
}
