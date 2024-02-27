<?php

namespace App\Http\Requests\API\V1\Category;

use App\Rules\JsonContainsKey;
use Illuminate\Foundation\Http\FormRequest;

class CreateSubCategoryRequest extends FormRequest
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
            'title' =>  ['required','json',new JsonContainsKey()],
            'description' =>  ['json',new JsonContainsKey()],
            'is_active' => 'boolean',
            'parent_id' => 'required|exists:categories,id',
            'sort' => 'required|numeric',
            'image' => 'required|image|mimes:jpg,png,jpeg|max:3072'
        ];
    }
}
