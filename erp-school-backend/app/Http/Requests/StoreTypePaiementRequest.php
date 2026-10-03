<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreTypePaiementRequest extends FormRequest
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
            'code_type'   => ['required', 'string', 'max:30', 'unique:types_paiement,code_type'],
            'libelle'     => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:255'],
            'actif'       => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'code_type.unique' => "Ce code de type de paiement existe déjà.",
        ];
    }
}
