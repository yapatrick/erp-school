<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateClasseRequest extends FormRequest
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
        $id = $this->route('classe');

        return [
            'nom_classe' => [
                'sometimes', 'string', 'max:50',
                Rule::unique('classes', 'nom_classe')
                    ->where('id_annee_scolaire', $this->input('id_annee_scolaire'))
                    ->ignore($id, 'id_classe'),
            ],
            'niveau'            => ['sometimes', 'string', 'max:50'],
            'capacite_max'      => ['sometimes', 'integer', 'min:1', 'max:200'],
            'frais_inscription' => ['sometimes', 'numeric', 'min:0'],
            'frais_scolarite'   => ['sometimes', 'numeric', 'min:0'],
            'id_annee_scolaire' => ['sometimes', 'exists:annees_scolaires,id_annee_scolaire'],
            'active'            => ['sometimes', 'boolean'],
        ];
    }
}
