<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProfileRequest extends FormRequest
{
    /**
     * Détermine si l'utilisateur est autorisé à faire cette requête.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true; // Changez ceci si vous avez des contrôles d'autorisation
    }

    /**
     * Obtenez les règles de validation qui s'appliquent à la requête.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            // Règles pour les attributs de User
            'phone_number' => 'required|string|max:15|unique:users,phone_number',
            'fullName' => 'required|string|max:255',
            'password' => 'required|string|min:6|confirmed',

            // Règles pour les attributs de Detail
            'details' => 'required|array',
            'details.email' => 'required|email|unique:details,email',
            // 'details.age' => 'required|integer|min:0',
            'details.date' => 'required|date',
            'details.gender' => 'required|string|in:male,female,other', // Exemples: male, female, other
            'details.avatar_url' => 'nullable|url',
            'details.bio' => 'nullable|string|max:1000',
            'details.company_name' => 'nullable|string|max:255',
            'details.address' => 'nullable|string|max:255',
            'details.domaine' => 'nullable|string|max:255',
        ];
    }

    /**
     * Obtenez les messages de validation personnalisés.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'phone_number.required' => 'Le numéro de téléphone est requis.',
            'phone_number.unique' => 'Le numéro de téléphone est déjà utilisé.',
            'fullName.required' => 'Le nom complet est requis.',
            'password.required' => 'Le mot de passe est requis.',
            'details.email.required' => 'L\'adresse e-mail est requise.',
            'details.email.unique' => 'L\'adresse e-mail est déjà utilisée.',
            // 'details.age.required' => 'L\'âge est requis.',
            'details.date.required' => 'L\'date est requise.',

            'details.gender.required' => 'Le genre est requis.',
            'details.gender.in' => 'Le genre doit être l\'un des suivants : male, female, other.',
            'details.avatar_url.url' => 'L\'URL de l\'avatar doit être valide.',
            'details.bio.max' => 'La biographie ne peut pas dépasser 1000 caractères.',
            'details.company_name.max' => 'Le nom de la société ne peut pas dépasser 255 caractères.',
            'details.address.max' => 'L\'adresse ne peut pas dépasser 255 caractères.',
            'details.domaine.max' => 'Le domaine ne peut pas dépasser 255 caractères.',
        ];
    }
}
