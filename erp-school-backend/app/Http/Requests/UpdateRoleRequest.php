<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateRoleRequest extends FormRequest
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
        $id = $this->route('role'); // si route model binding, sinon ->id_role

        return [
            'nomRole'       => ['sometimes', 'string', 'max:100'],
            'slug'          => [
                'sometimes', 'string', 'max:50',
                Rule::unique('roles', 'slug')->ignore($id, 'id_role'),
            ],
            'description'   => ['sometimes', 'nullable', 'string', 'max:255'],
            'actif'         => ['sometimes', 'boolean'],
            'permissions'   => ['sometimes', 'array'],
            'permissions.*' => ['integer', 'exists:permissions,id_permission'],
        ];
    }
}
