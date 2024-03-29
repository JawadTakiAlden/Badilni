<?php

namespace App\Http\Resources\API\V1;

use App\HelperMethods\HelperMethod;
use App\Types\UserType;
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
        $base = [
            'id' => $this->id,
            'image' => asset($this->image),
            'title' => $this->title,
        ];

        if ($request->user()->type === UserType::ADMIN){
            $base = array_merge($base , [
                'type' => $this->type,
                'sort' => intval($this->sort),
                'is_active' => boolval($this->is_active)
            ]);
        }
        return $base;
    }
}
