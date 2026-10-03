<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreEnseignementRequest extends FormRequest
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
            'id_classe'     => ['required', 'exists:classes,id_classe'],
            'id_matiere'    => ['required', 'exists:matieres,id_matiere'],
            'id_professeur' => ['required', 'exists:professeurs,id_professeur'],

            'heures_semaine'=> ['required', 'integer', 'min:1', 'max:40'],

            'jour_semaine'  => [
                'required',
                Rule::in(['lundi','mardi','mercredi','jeudi','vendredi','samedi']),
            ],

            'heure_debut'   => ['required', 'date_format:H:i'],
            'heure_fin'     => ['required', 'date_format:H:i', 'after:heure_debut'],

            'salle'         => ['nullable', 'string', 'max:50'],
        ];
    }

    public function messages(): array
    {
        return [
            'id_classe.exists'      => "La classe sélectionnée n'existe pas.",
            'id_matiere.exists'     => "La matière sélectionnée n'existe pas.",
            'id_professeur.exists'  => "Le professeur sélectionné n'existe pas.",
            'jour_semaine.in'       => "Le jour doit être : lundi, mardi, mercredi, jeudi, vendredi ou samedi.",
            'heure_fin.after'       => "L'heure de fin doit être postérieure à l'heure de début.",
        ];
    }
}
