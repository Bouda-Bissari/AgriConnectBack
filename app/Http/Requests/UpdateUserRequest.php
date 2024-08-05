<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    /**
     * Détermine si l'utilisateur est autorisé à faire cette requête.
     *
     * @return bool
     */
    public function authorize()
    {
        return true; // Changez cela en fonction de vos besoins d'autorisation
    }

    /**
     * Obtenez les règles de validation qui s'appliquent à la requête.
     *
     * @return array
     */
    public function rules()
    {

        return [
            'phone_number' => ['required','string','max:255',Rule::unique('users')->ignore($this->route('user'))], 
            'password' => 'nullable|string|min:6|confirmed',
            'roles' => 'nullable|array|exists:roles,id',
        ];
    }

    /**
     * Messages personnalisés pour les règles de validation.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'phone_number.required' => 'Le numéro de téléphone est requis.',
            'phone_number.unique' => 'Le numéro de téléphone est déjà utilisé.',
            'password.confirmed' => 'Les mots de passe ne correspondent pas.',
            'roles.exists' => 'Un ou plusieurs rôles sont invalides.',
        ];
    }
}
