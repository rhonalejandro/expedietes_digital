<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use App\Models\Sucursal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * Controlador de Configuración de Empresa
 *
 * Maneja la configuración de la empresa y gestión de sucursales.
 */
class SettingsController extends Controller
{
    /**
     * Mostrar la página de configuración de empresa.
     */
    public function index()
    {
        $empresa = Empresa::first();
        $sucursales = Sucursal::orderBy('created_at', 'desc')->get();
        
        return view('settings', compact('empresa', 'sucursales'));
    }

    /**
     * Actualizar información de la empresa.
     */
    public function updateEmpresa(Request $request)
    {
        $validated = $request->validate([
            'nombre' => ['required', 'string', 'max:200'],
            'identificacion' => ['required', 'string', 'max:50'],
            'email' => ['required', 'email', 'max:150'],
            'telefono' => ['nullable', 'string', 'max:20'],
            'direccion' => ['nullable', 'string', 'max:300'],
            'logo' => ['nullable', 'image', 'max:2048'],
            'logo_rectangular' => ['nullable', 'image', 'max:2048'],
        ]);

        $empresa = Empresa::first();

        if (!$empresa) {
            $empresa = new Empresa();
            $empresa->estado = true;
        }

        $empresa->nombre = $validated['nombre'];
        $empresa->identificacion = $validated['identificacion'];
        $empresa->email = $validated['email'];
        $empresa->telefono = $validated['telefono'] ?? null;
        $empresa->direccion = $validated['direccion'] ?? null;

        // Manejar subida de logo redondo
        if ($request->hasFile('logo')) {
            // Eliminar logo anterior si existe
            if ($empresa->logo) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($empresa->logo);
            }
            $logoPath = $request->file('logo')->store('logos/empresas', 'public');
            $empresa->logo = $logoPath;
        }

        // Manejar subida de logo rectangular
        if ($request->hasFile('logo_rectangular')) {
            // Eliminar logo rectangular anterior si existe
            if ($empresa->logo_rectangular) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($empresa->logo_rectangular);
            }
            $logoPath = $request->file('logo_rectangular')->store('logos/empresas', 'public');
            $empresa->logo_rectangular = $logoPath;
        }

        $empresa->save();

        return back()->with('success', 'Información de la empresa actualizada correctamente.');
    }

    /**
     * Agregar nueva sucursal.
     */
    public function storeSucursal(Request $request)
    {
        $validated = $request->validate([
            'nombre' => ['required', 'string', 'max:150'],
            'direccion' => ['required', 'string', 'max:300'],
            'telefono' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:150'],
            'encargado' => ['nullable', 'string', 'max:100'],
            'estado' => ['boolean'],
        ]);

        // Usar telefono o contacto segun corresponda
        $telefono = $validated['telefono'] ?? null;

        Sucursal::create([
            'nombre' => $validated['nombre'],
            'direccion' => $validated['direccion'],
            'telefono' => $telefono,
            'email' => $validated['email'] ?? null,
            'encargado' => $validated['encargado'] ?? null,
            'estado' => $validated['estado'] ?? true,
        ]);

        return back()->with('success', 'Sucursal agregada correctamente.');
    }

    /**
     * Actualizar sucursal.
     */
    public function updateSucursal(Request $request, $id)
    {
        $validated = $request->validate([
            'nombre' => ['required', 'string', 'max:150'],
            'direccion' => ['required', 'string', 'max:300'],
            'telefono' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:150'],
            'encargado' => ['nullable', 'string', 'max:100'],
            'estado' => ['boolean'],
        ]);

        $sucursal = Sucursal::findOrFail($id);
        
        // Si hay un campo empresa_id en el futuro
        // $validated['empresa_id'] = auth()->user()->empresa_id ?? null;
        
        $sucursal->update($validated);

        return back()->with('success', 'Sucursal actualizada correctamente.');
    }

    /**
     * Eliminar sucursal.
     */
    public function destroySucursal($id)
    {
        $sucursal = Sucursal::findOrFail($id);
        $sucursal->delete();

        return back()->with('success', 'Sucursal eliminada correctamente.');
    }

    /**
     * Cambiar estado de sucursal.
     */
    public function toggleSucursal($id)
    {
        $sucursal = Sucursal::findOrFail($id);
        $sucursal->estado = !$sucursal->estado;
        $sucursal->save();

        return back()->with('success', 'Estado de sucursal actualizado.');
    }
}
