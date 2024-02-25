<?php

namespace App\Http\Requests\API\V1\Auth;

use Illuminate\Foundation\Http\FormRequest;

class SingUpRequest extends FormRequest
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
            'email'                 => 'required|string|unique:users,email',
            'password'              => 'required|string|min:6',
            'name'                  => 'required|string|max:28'
        ];
    }
    public function filters()
    {
        return [
            'email' => 'trim|strip_tags|escape|lowercase',
            'password' => 'trim|strip_tags',
            'name' => 'trim|strip_tags|escape'
        ];
    }
}
