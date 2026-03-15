<?php

/**
 * Desarrollo - Permisos de Usuarios - UI - 2026-02-18
 * 
 * Request: StorePermissionRequest
 * 
 * Propósito: Validar creación de permisos
 * 
 * @package App\Http\Requests\Settings
 */

namespace App\Http\Requests\Settings;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class StorePermissionRequest
 */
class StorePermissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'modulo' => ['required', 'string', 'max:50', 'alpha_dash'],
            'codigo' => ['required', 'string', 'max:50', 'alpha_dash'],
            'nombre' => ['required', 'string', 'max:100'],
            'descripcion' => ['nullable', 'string', 'max:500'],
            'tipo' => ['required', 'in:general,granular'],
        ];
    }

    public function messages(): array
    {
        return [
            'modulo.required' => 'El módulo es obligatorio.',
            'modulo.alpha_dash' => 'El módulo solo puede contener letras, números y guiones.',
            'codigo.required' => 'El código es obligatorio.',
            'codigo.alpha_dash' => 'El código solo puede contener letras, números y guiones.',
            'nombre.required' => 'El nombre es obligatorio.',
            'nombre.max' => 'El nombre no puede exceder 100 caracteres.',
            'tipo.in' => 'El tipo debe ser "general" o "granular".',
        ];
    }
}
