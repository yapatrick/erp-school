<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateTypePaiementRequest extends FormRequest
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
        $id = $this->route('type_paiement');

        return [
            'code_type' => [
                'sometimes', 'string', 'max:30',
                Rule::unique('types_paiement', 'code_type')->ignore($id, 'id_type'),
            ],
            'libelle'     => ['sometimes', 'string', 'max:100'],
            'description' => ['sometimes', 'nullable', 'string', 'max:255'],
            'actif'       => ['sometimes', 'boolean'],
        ];
    }
}
