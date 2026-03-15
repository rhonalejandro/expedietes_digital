<?php

namespace App\Http\Requests\Settings;

use Illuminate\Foundation\Http\FormRequest;

/**
 * UpdateEmpresaRequest
 * 
 * Validación para actualizar información de empresa.
 * Principio Single Responsibility: Solo valida datos de empresa.
 */
class UpdateEmpresaRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nombre' => ['required', 'string', 'max:200'],
            'tipo_identificacion' => ['nullable', 'string', 'max:50'],
            'identificacion' => ['required', 'string', 'max:50'],
            'email' => ['required', 'email', 'max:150'],
            'telefono' => ['nullable', 'string', 'max:20'],
            'direccion' => ['nullable', 'string', 'max:300'],
            'logo' => ['nullable', 'image', 'max:2048', 'mimes:png,jpg,jpeg'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'nombre.required' => 'El nombre de la empresa es obligatorio.',
            'nombre.max' => 'El nombre no puede exceder los 200 caracteres.',
            'identificacion.required' => 'La identificación es obligatoria.',
            'identificacion.max' => 'La identificación no puede exceder los 50 caracteres.',
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'El correo electrónico debe ser válido.',
            'telefono.max' => 'El teléfono no puede exceder los 20 caracteres.',
            'direccion.max' => 'La dirección no puede exceder los 300 caracteres.',
            'logo.image' => 'El logo debe ser una imagen.',
            'logo.max' => 'El logo no puede exceder los 2MB.',
            'logo.mimes' => 'El logo debe ser PNG, JPG o JPEG.',
        ];
    }
}
