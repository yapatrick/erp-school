<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreAnneeScolaireRequest extends FormRequest
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
            'libelle'    => ['required', 'string', 'max:50', 'unique:annees_scolaires,libelle'],
            'date_debut' => ['required', 'date'],
            'date_fin'   => ['required', 'date', 'after:date_debut'],
            'en_cours'   => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'libelle.unique'   => "Cette année scolaire existe déjà.",
            'date_fin.after'   => "La date de fin doit être postérieure à la date de début.",
        ];
    }
}
