<?php

namespace App\Http\Requests\API\V1\Section;

use App\Rules\JsonContainsKey;
use Illuminate\Foundation\Http\FormRequest;

class UpdateSectionRequest extends FormRequest
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
            'title' =>[
                'json',
                new JsonContainsKey()
            ],
            'is_active' => 'boolean'
        ];
    }
}
