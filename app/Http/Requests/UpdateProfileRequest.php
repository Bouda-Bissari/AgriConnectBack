<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProfileRequest extends FormRequest
{
    /**
     * Détermine si l'utilisateur est autorisé à faire cette requête.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Obtenez les règles de validation qui s'appliquent à la requête.
     *
     * @return array
     */
    public function rules(): array
    {
        $userId = $this->route('profile'); 

        return [
            'phone_number' => [
                'nullable',
                'string',
                'max:15',
                Rule::unique('users', 'phone_number')->ignore($userId),
            ],
            'fullName' => 'nullable|string|max:255',
            'password' => 'nullable|string|min:6|confirmed',

            'details' => 'nullable|array',

            'details.email' => [
                'nullable',
                'email',
                Rule::unique('details', 'email')->ignore($this->route('profile')),
            ],
            // 'details.age' => 'nullable|integer|min:0',
            'details.date' => 'nullable',

            'details.gender' => 'nullable|string|in:Masculin,Feminin,other',
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
            'phone_number.unique' => 'Le numéro de téléphone doit être unique.',
            'details.email.unique' => 'L\'email doit être unique.',
            // 'details.age.required' => 'L\'âge est requis.',
            'details.date.required' => 'La date est requise.',

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
