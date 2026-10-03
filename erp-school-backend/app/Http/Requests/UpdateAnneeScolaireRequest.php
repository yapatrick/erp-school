<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateAnneeScolaireRequest extends FormRequest
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
        $id = $this->route('annee_scolaire'); // ou ->id_annee_scolaire selon route binding

        return [
            'libelle' => [
                'required', 'string', 'max:50',
                Rule::unique('annees_scolaires', 'libelle')->ignore($id, 'id_annee_scolaire'),
            ],
            'date_debut' => ['required', 'date'],
            'date_fin'   => ['required', 'date', 'after:date_debut'],
            'en_cours'   => ['sometimes', 'boolean'],
        ];
    }
}
