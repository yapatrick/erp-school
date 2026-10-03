<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateeEnseignementRequest extends FormRequest
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
            'id_classe'     => ['sometimes', 'exists:classes,id_classe'],
            'id_matiere'    => ['sometimes', 'exists:matieres,id_matiere'],
            'id_professeur' => ['sometimes', 'exists:professeurs,id_professeur'],

            'heures_semaine'=> ['sometimes', 'integer', 'min:1', 'max:40'],

            'jour_semaine'  => [
                'sometimes',
                Rule::in(['lundi','mardi','mercredi','jeudi','vendredi','samedi']),
            ],

            'heure_debut'   => ['sometimes', 'date_format:H:i'],
            'heure_fin'     => ['sometimes', 'date_format:H:i'],

            'salle'         => ['sometimes', 'nullable', 'string', 'max:50'],
        ];
    }
}
