<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreRoleRequest extends FormRequest
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
                'nomRole'        => ['required', 'string', 'max:100'],
                'slug'           => ['required', 'string', 'max:50', 'unique:roles,slug'],
                'description'    => ['nullable', 'string', 'max:255'],
                'actif'          => ['sometimes', 'boolean'],
                'permissions'    => ['sometimes', 'array'],
                'permissions.*'  => ['integer', 'exists:permissions,id_permission'],
            ];
        }

        public function messages(): array
        {
            return [
                'slug.unique'             => "Ce slug de rôle existe déjà.",
                'permissions.*.exists'    => "Une permission sélectionnée est invalide.",
            ];
        }
}
