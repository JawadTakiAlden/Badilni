<?php

namespace App\Http\Requests\API\V1\Country;

use App\Rules\JsonContainsKey;
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
            'name' => 'string|max:40',
            'title' =>  ['json',new JsonContainsKey()],
            'flag' => 'string',
            'state_key' => 'string',
            'is_active' => 'boolean',
            'is_default' => 'boolean',
        ];
    }
}
