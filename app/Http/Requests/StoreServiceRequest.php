<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreServiceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Allow all users for now, customize as needed
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<string>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'location' => 'required|string',
            'deadline' => 'nullable|string',
            'price' => 'nullable|numeric|min:0',
            'service_type' => 'required|in:work,material',
            'image' => 'nullable|file|mimes:jpg,png,jpeg',        ];
    }
}
