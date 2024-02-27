<?php

namespace App\Http\Resources\API\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CityResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {

        $languageKey = auth()->user()->language ?? 'en'; // Assuming 'en' as default if user not authenticated or language not set
        $title = json_decode($this->title, true);
        $titleValue = $title[$languageKey] ?? null;

        return [
            'id' => $this->id,
            'title' => $titleValue,
            'is_active' => boolval($this->is_active),
            'country_name' => $this->country->name,
            'areas' => $this->areas
        ];
    }
}
