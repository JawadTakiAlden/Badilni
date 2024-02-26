<?php

namespace App\Http\Requests\API\V1\Slider;

use Illuminate\Foundation\Http\FormRequest;

class CreateSlideRequest extends FormRequest
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
            'image' => "required|image|mimes:jpg,pnj,jpeg,max:6144",
            'type' => 'required|in:home,splash',
            'sort' => 'numeric|min:0'
        ];
    }
}
