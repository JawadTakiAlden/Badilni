<?php

namespace App\Http\Requests\API\V1\Category;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCategoryRequest extends FormRequest
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
            'title' => 'json',
            'description' => 'json',
            'is_active' => 'boolean',
            'sort' => 'numeric',
            'parent_id' => 'numeric|exists:categories,id',
            'image' => 'image|mimes:jpg,png,jpeg|max:3072'
        ];
    }
}
