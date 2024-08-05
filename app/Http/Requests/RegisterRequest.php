<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'phone_number' => 'required|string|digits:11|unique:users,phone_number',
            'fullName' => 'required|string',
            'role' => 'required|string',
            'password' => [
                'required',
                'min:8',
                'confirmed'
            ],
            'password_confirmation' => 'required',
        ];
    }
}
