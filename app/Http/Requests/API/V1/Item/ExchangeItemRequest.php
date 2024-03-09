<?php

namespace App\Http\Requests\API\V1\Item;

use Illuminate\Foundation\Http\FormRequest;

class ExchangeItemRequest extends FormRequest
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
            'exchange_type' => 'required|string|in:cash,change',
            'exchanged_item' => 'required|numeric|exists:items,id',
            'extra_money' => 'numeric|sometimes',
            'offer_money' => 'numeric|sometimes',
            'my_item' => 'required_if:type,change|numeric|exists:items,id',
            'price' => 'required_if:type,cash|numeric|min:0',
        ];
    }
}
