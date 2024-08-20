<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    /**
     * Détermine si l'utilisateur est autorisé à effectuer cette demande.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Obtenir les règles de validation qui s'appliquent à la demande.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'phone_number' => 'required|string|digits:11|unique:users,phone_number',
            'fullName' => 'required|string|unique:users,fullName',
            'role' => 'required|string',
            'password' => [
                'required',
                'min:4',
                'confirmed'
            ],
            'password_confirmation' => 'required',
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
            'phone_number.digits' => "Le numéro de téléphone n'est pas valide.",
            'phone_number.unique' => "Ce numéro de téléphone est déjà utilisé.",
            'fullName.required' => "Le nom complet est requis.",
            'fullName.unique' => "Ce nom est deja utiliser.",

            'fullName.string' => "Le nom complet doit être une chaîne de caractères.",
            'role.required' => "Le rôle est requis.",
            'role.string' => "Le rôle doit être une chaîne de caractères.",
            'password.required' => "Le mot de passe est requis.",
            'password.min' => "Le mot de passe doit contenir au moins 4 caractères.",
            'password.confirmed' => "Les mots de passe ne correspondent pas.",
            'password_confirmation.required' => "La confirmation du mot de passe est requise.",
        ];
    }
}
