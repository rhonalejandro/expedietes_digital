<?php

/**
 * Desarrollo - Permisos de Usuarios - UI - 2026-02-18
 * 
 * Controlador: PermissionTemplatesController
 * 
 * Propósito: Gestionar plantillas de permisos
 * 
 * @package App\Http\Controllers\Settings
 */

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\PlantillaPermiso;
use App\Models\Permiso;
use App\Services\Permissions\PermissionService;
use Illuminate\Http\Request;

/**
 * Class PermissionTemplatesController
 */
class PermissionTemplatesController extends Controller
{
    private PermissionService $permissionService;

    public function __construct(PermissionService $permissionService)
    {
        $this->permissionService = $permissionService;
    }

    /**
     * Mostrar lista de plantillas.
     */
    public function index()
    {
        $plantillas = PlantillaPermiso::withCount('permisos')
            ->orderBy('orden')
            ->orderBy('nombre')
            ->get();

        return view('modules.settings.permissions.templates.index', compact('plantillas'));
    }

    /**
     * Mostrar formulario para crear plantilla.
     */
    public function create()
    {
        $permisos = $this->permissionService->getAllPermissionsGroupedByModule(true);
        
        return view('modules.settings.permissions.templates.create', compact('permisos'));
    }

    /**
     * Guardar nueva plantilla.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => ['required', 'string', 'max:100', 'unique:plantillas_permisos'],
            'descripcion' => ['nullable', 'string', 'max:500'],
            'es_sistema' => ['boolean'],
            'es_activa' => ['boolean'],
            'color' => ['nullable', 'string', 'max:7'],
            'icono' => ['nullable', 'string', 'max:50'],
            'permisos' => ['array'],
            'permisos.*' => ['integer', 'exists:permisos,id'],
        ]);

        try {
            $plantilla = PlantillaPermiso::create([
                'nombre' => $validated['nombre'],
                'descripcion' => $validated['descripcion'] ?? null,
                'es_sistema' => $validated['es_sistema'] ?? false,
                'es_activa' => $validated['es_activa'] ?? true,
                'color' => $validated['color'] ?? '#667eea',
                'icono' => $validated['icono'] ?? 'ti ti-shield',
            ]);

            // Asignar permisos
            if (!empty($validated['permisos'])) {
                foreach ($validated['permisos'] as $permisoId) {
                    $plantilla->agregarPermiso($permisoId);
                }
            }

            return redirect()
                ->route('settings.permissions.templates.index')
                ->with('success', 'Plantilla creada correctamente.');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Mostrar formulario para editar plantilla.
     */
    public function edit(int $id)
    {
        $plantilla = PlantillaPermiso::with('permisos')->findOrFail($id);
        $permisos = $this->permissionService->getAllPermissionsGroupedByModule(true);
        
        // Obtener IDs de permisos asignados
        $permisosAsignados = $plantilla->permisos->pluck('id')->toArray();

        return view('modules.settings.permissions.templates.edit', compact(
            'plantilla',
            'permisos',
            'permisosAsignados'
        ));
    }

    /**
     * Actualizar plantilla.
     */
    public function update(Request $request, int $id)
    {
        $validated = $request->validate([
            'nombre' => ['required', 'string', 'max:100', 'unique:plantillas_permisos,nombre,'.$id],
            'descripcion' => ['nullable', 'string', 'max:500'],
            'es_sistema' => ['boolean'],
            'es_activa' => ['boolean'],
            'color' => ['nullable', 'string', 'max:7'],
            'icono' => ['nullable', 'string', 'max:50'],
            'permisos' => ['array'],
            'permisos.*' => ['integer', 'exists:permisos,id'],
        ]);

        try {
            $plantilla = PlantillaPermiso::findOrFail($id);
            
            $plantilla->update([
                'nombre' => $validated['nombre'],
                'descripcion' => $validated['descripcion'] ?? null,
                'es_sistema' => $validated['es_sistema'] ?? false,
                'es_activa' => $validated['es_activa'] ?? true,
                'color' => $validated['color'] ?? '#667eea',
                'icono' => $validated['icono'] ?? 'ti ti-shield',
            ]);

            // Sincronizar permisos
            $plantilla->permisos()->detach();
            
            if (!empty($validated['permisos'])) {
                foreach ($validated['permisos'] as $permisoId) {
                    $plantilla->agregarPermiso($permisoId);
                }
            }

            return redirect()
                ->route('settings.permissions.templates.index')
                ->with('success', 'Plantilla actualizada correctamente.');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Eliminar plantilla.
     */
    public function destroy(int $id)
    {
        try {
            $plantilla = PlantillaPermiso::findOrFail($id);
            
            if ($plantilla->es_sistema) {
                return back()->with('error', 'No se pueden eliminar plantillas del sistema.');
            }

            $plantilla->delete();

            return back()->with('success', 'Plantilla eliminada correctamente.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Toggle estado de plantilla.
     */
    public function toggleStatus(int $id)
    {
        try {
            $plantilla = PlantillaPermiso::findOrFail($id);
            $plantilla->update(['es_activa' => !$plantilla->es_activa]);

            return back()->with('success', 'Estado actualizado.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }
}
