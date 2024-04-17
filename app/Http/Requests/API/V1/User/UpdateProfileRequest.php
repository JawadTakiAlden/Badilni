<?php

namespace App\Http\Requests\API\V1\User;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
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
            'name' => 'string|max:30',
            'phone' => 'string',
            'image' => 'image|mimes:jpg,png,jpeg|max:4096',
            'country_id' => 'numeric|exists:countries,id',
            'gender' => 'string|in:male,female',
            'birthdate' => 'date',
            'language' => 'string|in:en,ar'
        ];
    }
}
