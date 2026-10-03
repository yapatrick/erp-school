<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreProfesseurRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'matricule'     => ['required', 'string', 'max:30', 'unique:professeurs,matricule'],
            'nom'           => ['required', 'string', 'max:100'],
            'prenom'        => ['required', 'string', 'max:100'],
            'telephone'     => ['nullable', 'string', 'max:20'],
            'email'         => ['nullable', 'email', 'max:150', 'unique:professeurs,email'],
            'specialite'    => ['nullable', 'string', 'max:100'],
            'date_embauche' => ['nullable', 'date'],
            'salaire'       => ['nullable', 'numeric', 'min:0'],
            'actif'         => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'matricule.unique' => "Ce matricule est déjà utilisé.",
            'email.unique'     => "Cet email est déjà utilisé par un autre professeur.",
        ];
    }
}
