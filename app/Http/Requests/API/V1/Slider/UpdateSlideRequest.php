<?php

namespace App\Http\Requests\API\V1\Slider;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSlideRequest extends FormRequest
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
            'is_active' => 'required|boolean',
            'title' => 'required|string|max:255',
            'image' => "image|mimes:jpg,pnj,jpeg,max:6144",
            'type' => 'required|in:home,splash',
            'sort' => 'required|numeric|min:0'
        ];
    }
}
