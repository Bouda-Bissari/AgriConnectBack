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
        $detail = $this->route('detail');
        return [
            'phone_number' => [
                'nullable',
                'string',
                'max:15',
                Rule::unique('users', 'phone_number')->ignore($userId),

            ],
            'fullName' => 'nullable|string|max:255',
            'password' => 'nullable|string|min:6|confirmed',
// 'email' => [
//     'nullable',
//     'email',
//     Rule::unique('details', 'email')->ignore($detail->id),
// ],

                     'date' => 'nullable',
            'gender' => 'nullable|string|in:Masculin,Feminin,other',
            'image' => 'nullable|file|mimes:jpg,png,jpeg',
            'bio' => 'nullable|string|max:1000',
            'company_name' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
            'domaine' => 'nullable|string|max:255',
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
            'email.email' => 'L\'email doit être valide.',
            'date.required' => 'La date est requise.',
            'gender.required' => 'Le genre est requis.',
            'gender.in' => 'Le genre doit être l\'un des suivants : Masculin, Féminin, autre.',
            'image.file' => 'Le fichier doit être une image.',
            'image.mimes' => 'L\'image doit être de type : jpeg, png, jpg.',
            'bio.max' => 'La biographie ne peut pas dépasser 1000 caractères.',
            'company_name.max' => 'Le nom de la société ne peut pas dépasser 255 caractères.',
            'address.max' => 'L\'adresse ne peut pas dépasser 255 caractères.',
            'domaine.max' => 'Le domaine ne peut pas dépasser 255 caractères.',
        ];
    }
}
