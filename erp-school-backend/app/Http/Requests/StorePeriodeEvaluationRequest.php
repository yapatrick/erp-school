<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StorePeriodeEvaluationRequest extends FormRequest
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
            'id_annee_scolaire' => ['required', 'exists:annees_scolaires,id_annee_scolaire'],
            'libelle'           => ['required', 'string', 'max:50'],
            'date_debut'        => ['required', 'date'],
            'date_fin'          => ['required', 'date', 'after:date_debut'],
            'ordre'             => ['required', 'integer', 'min:1', 'max:20'],
            'active'            => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'id_annee_scolaire.exists' => "L'année scolaire sélectionnée n'existe pas.",
            'date_fin.after'           => "La date de fin doit être postérieure à la date de début.",
        ];
    }
}
