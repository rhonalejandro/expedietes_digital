<?php

/**
 * Desarrollo - Permisos de Usuarios - UI - 2026-02-18
 * 
 * Request: UpdatePermissionRequest
 * 
 * Propósito: Validar actualización de permisos
 * 
 * @package App\Http\Requests\Settings
 */

namespace App\Http\Requests\Settings;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class UpdatePermissionRequest
 */
class UpdatePermissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'modulo' => ['sometimes', 'string', 'max:50', 'alpha_dash'],
            'codigo' => ['sometimes', 'string', 'max:50', 'alpha_dash'],
            'nombre' => ['sometimes', 'string', 'max:100'],
            'descripcion' => ['nullable', 'string', 'max:500'],
            'tipo' => ['sometimes', 'in:general,granular'],
            'estado' => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'modulo.alpha_dash' => 'El módulo solo puede contener letras, números y guiones.',
            'codigo.alpha_dash' => 'El código solo puede contener letras, números y guiones.',
            'nombre.max' => 'El nombre no puede exceder 100 caracteres.',
            'tipo.in' => 'El tipo debe ser "general" o "granular".',
        ];
    }
}
