<?php

namespace App\Http\Requests\Settings;

use Illuminate\Foundation\Http\FormRequest;

/**
 * StoreSucursalRequest
 * 
 * Validación para crear nueva sucursal.
 * Principio Single Responsibility: Solo valida creación de sucursales.
 */
class StoreSucursalRequest extends FormRequest
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
            'nombre' => ['required', 'string', 'max:150'],
            'direccion' => ['required', 'string', 'max:300'],
            'telefono' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:150'],
            'encargado'      => ['nullable', 'string', 'max:100'],
            'hora_apertura'  => ['required', 'date_format:H:i'],
            'hora_cierre'    => ['required', 'date_format:H:i', 'after:hora_apertura'],
            'estado'         => ['nullable', 'boolean'],
            'imagen'         => ['nullable', 'image', 'max:2048'],
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
            'nombre.required' => 'El nombre de la sucursal es obligatorio.',
            'nombre.max' => 'El nombre no puede exceder los 150 caracteres.',
            'direccion.required' => 'La dirección es obligatoria.',
            'direccion.max' => 'La dirección no puede exceder los 300 caracteres.',
            'telefono.max' => 'El teléfono no puede exceder los 20 caracteres.',
            'email.email' => 'El correo electrónico debe ser válido.',
            'email.max' => 'El correo no puede exceder los 150 caracteres.',
            'encargado.max' => 'El nombre del encargado no puede exceder los 100 caracteres.',
        ];
    }
}
