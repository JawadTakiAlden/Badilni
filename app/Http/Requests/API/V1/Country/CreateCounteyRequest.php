<?php

namespace App\Http\Requests\API\V1\Country;

use Illuminate\Foundation\Http\FormRequest;

class CreateCounteyRequest extends FormRequest
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
            'name' => 'required|string|max:40',
            'title' => 'required|json',
            'flag' => 'required|string',
            'state_key' => 'required|string',
            'is_active' => 'required|boolean',
            'is_default' => 'required|boolean',
        ];
    }
}
