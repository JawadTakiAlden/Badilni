<?php

namespace App\Http\Requests\API\V1\City;

use App\Rules\JsonContainsKey;
use Illuminate\Foundation\Http\FormRequest;

class CreateCityRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => [
                'required',
                'json',
                new JsonContainsKey()
            ],
            'country_id' => 'required|exists:countries,id',
            'is_active' => 'required|boolean'
        ];
    }
}
