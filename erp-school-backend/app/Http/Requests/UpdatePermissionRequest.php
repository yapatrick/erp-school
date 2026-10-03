<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdatePermissionRequest extends FormRequest
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
        $id = $this->route('permission');

        return [
            'nom'         => ['sometimes', 'string', 'max:100'],
            'slug'        => [
                'sometimes', 'string', 'max:100',
                Rule::unique('permissions', 'slug')->ignore($id, 'id_permission'),
            ],
            'description' => ['sometimes', 'nullable', 'string', 'max:255'],
            'categorie'   => ['sometimes', 'string', 'max:50'],
            'actif'       => ['sometimes', 'boolean'],
        ];
    }
}
