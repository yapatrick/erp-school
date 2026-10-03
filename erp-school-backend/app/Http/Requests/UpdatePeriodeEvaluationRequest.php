<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdatePeriodeEvaluationRequest extends FormRequest
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
            'id_annee_scolaire' => ['sometimes', 'exists:annees_scolaires,id_annee_scolaire'],
            'libelle'           => ['sometimes', 'string', 'max:50'],
            'date_debut'        => ['sometimes', 'date'],
            'date_fin'          => ['sometimes', 'date'],
            'ordre'             => ['sometimes', 'integer', 'min:1', 'max:20'],
            'active'            => ['sometimes', 'boolean'],
        ];
    }
}
