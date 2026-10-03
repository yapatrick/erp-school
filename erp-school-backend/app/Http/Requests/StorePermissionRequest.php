<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StorePermissionRequest extends FormRequest
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
            'nom'         => ['required', 'string', 'max:100'],
            'slug'        => ['required', 'string', 'max:100', 'unique:permissions,slug'],
            'description' => ['nullable', 'string', 'max:255'],
            'categorie'   => ['required', 'string', 'max:50'],
            'actif'       => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'slug.unique' => "Ce slug de permission existe déjà.",
        ];
    }
}
