<?php

namespace App\Http\Requests\API\V1\Item;

use App\Types\ImageFlag;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateItemRequest extends FormRequest
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
            'title' => 'string|max:255',
            'description' => 'string',
            'price' => 'nullable|numeric',
            'is_active' => 'boolean',
            'area_id' => 'exists:areas,id',
            'status' => 'string|in:new,old',
            'category_id' => [
                Rule::exists('categories', 'id')
            ],
            'images' => 'array|min:1',
            'images.*.imageFile' => [
                'required_if:images.*.flag,' . ImageFlag::ADD,
                'image',
                'mimes:jpg,png,jpeg',
                'max:3072',
            ],
            'images.*.id' => [
                'required_if:images.*.flag,' . implode(',',[ ImageFlag::DELETE , ImageFlag::UPDATE_IS_DEFAULT]),
                'numeric',
                'exists:item_images,id'
            ],
            'images.*.is_default' => [
                'required_if:images.*.flag,' . implode(',',[ImageFlag::UPDATE_IS_DEFAULT . ImageFlag::ADD]) ,
                'boolean'
            ],
            'images.*.flag' => 'required|numeric|in:'. implode(',', [ImageFlag::DELETE, ImageFlag::ADD, ImageFlag::UPDATE_IS_DEFAULT])
        ];
    }
}
