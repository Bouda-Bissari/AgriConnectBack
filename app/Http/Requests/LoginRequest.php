<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'phone_number' => 'required|string|exists:users,phone_number',
            'password' => 'required|string',
        ];
    }


    /**
     * Personnaliser les messages de validation.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'phone_number.required' => "Le numéro de téléphone est requis.",
            'phone_number.string' => "Le numéro de téléphone doit être une chaîne de caractères.",
            'phone_number.exists' => "Le numéro de téléphone n'existe pas dans notre base de données.",
            'password.required' => "Le mot de passe est requis.",
            'password.string' => "Le mot de passe doit être une chaîne de caractères.",
        ];
    }
}
