<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDetailRequest extends FormRequest
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
            // 'age' => 'nullable|integer',
            'date' => 'nullable|date',

            'email' => 'nullable|string|email|max:255',
            'gender' => 'nullable|string',
            'avatar_url' => 'nullable|string',
            'bio' => 'nullable|string',
            'company_name' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
        ];
    }
}
