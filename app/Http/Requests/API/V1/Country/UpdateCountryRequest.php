<?php

namespace App\Http\Requests\API\V1\Country;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCountryRequest extends FormRequest
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
            'name' => 'sometimes|string|max:40',
            'title' => 'sometimes|json',
            'flag' => 'sometimes|string',
            'state_key' => 'sometimes|string',
            'is_active' => 'sometimes|boolean',
            'is_default' => 'sometimes|boolean',
        ];
    }
}