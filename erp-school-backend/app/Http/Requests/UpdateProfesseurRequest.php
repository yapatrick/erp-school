<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateProfesseurRequest extends FormRequest
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
        $id = $this->route('professeur');

        return [
            'matricule' => [
                'sometimes', 'string', 'max:30',
                Rule::unique('professeurs', 'matricule')->ignore($id, 'id_professeur'),
            ],
            'nom'           => ['sometimes', 'string', 'max:100'],
            'prenom'        => ['sometimes', 'string', 'max:100'],
            'telephone'     => ['sometimes', 'nullable', 'string', 'max:20'],
            'email'         => [
                'sometimes', 'nullable', 'email', 'max:150',
                Rule::unique('professeurs', 'email')->ignore($id, 'id_professeur'),
            ],
            'specialite'    => ['sometimes', 'nullable', 'string', 'max:100'],
            'date_embauche' => ['sometimes', 'nullable', 'date'],
            'salaire'       => ['sometimes', 'nullable', 'numeric', 'min:0'],
            'actif'         => ['sometimes', 'boolean'],
        ];
    }
}
