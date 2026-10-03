<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreClasseRequest extends FormRequest
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
            'nom_classe'         => [
                'required', 'string', 'max:50',
                Rule::unique('classes', 'nom_classe')
                    ->where('id_annee_scolaire', $this->id_annee_scolaire),
            ],
            'niveau'             => ['required', 'string', 'max:50'],
            'capacite_max'       => ['required', 'integer', 'min:1', 'max:200'],
            'frais_inscription'  => ['required', 'numeric', 'min:0'],
            'frais_scolarite'    => ['required', 'numeric', 'min:0'],
            'id_annee_scolaire'  => ['required', 'exists:annees_scolaires,id_annee_scolaire'],
            'active'             => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'nom_classe.unique'         => "Une classe avec ce nom existe déjà pour cette année scolaire.",
            'id_annee_scolaire.exists'  => "L'année scolaire sélectionnée n'existe pas.",
        ];
    }
}
