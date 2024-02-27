<?php

namespace App\Http\Requests\API\V1\Item;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateItemRequest extends FormRequest
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
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'nullable|numeric',
            'is_active' => 'boolean',
            'area_id' => 'required|exists:areas,id',
            'sub_category_id' => [
                'required',
                Rule::exists('categories', 'id')->where(function ($query) {
                    $query->whereNotNull('parent_id');
                })
            ],
            'images' => 'required|array|min:1',
            'images.*.imageFile' => 'required|image|mimes:jpg,png,jpeg|max:3072',
            'images.*.is_default' => [
                'required',
                'boolean',
                function ($attribute, $value, $fail) {
                    $defaultCount = count(array_filter($this->input('images'), function ($image) {
                        return $image['is_default'] === true;
                    }));
                    if ($defaultCount > 1 ) {
                        $fail('Exactly one image must be marked as default.');
                    }
                    if ($defaultCount === 0 ) {
                        $fail('at least one image must be marked as default.');
                    }
                },
            ],
        ];
    }
}
