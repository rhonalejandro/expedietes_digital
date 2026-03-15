<?php

/**
 * Desarrollo - Permisos de Usuarios - UI - 2026-02-18
 * 
 * Controlador: UserPermissionsController
 * 
 * Propósito: Asignar permisos a usuarios
 * 
 * @package App\Http\Controllers\Settings
 */

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\Usuario;
use App\Models\PlantillaPermiso;
use App\Services\Permissions\PermissionService;
use Illuminate\Http\Request;

/**
 * Class UserPermissionsController
 */
class UserPermissionsController extends Controller
{
    private PermissionService $permissionService;

    public function __construct(PermissionService $permissionService)
    {
        $this->permissionService = $permissionService;
    }

    /**
     * Mostrar lista de usuarios para asignar permisos.
     */
    public function index(Request $request)
    {
        $search = $request->get('search', '');
        
        $usuarios = Usuario::with('persona', 'roles', 'permisos')
            ->when($search, function ($query, $search) {
                $query->whereHas('persona', function ($q) use ($search) {
                    $q->where('nombre', 'like', "%{$search}%")
                      ->orWhere('apellido', 'like', "%{$search}%");
                })
                ->orWhere('email', 'like', "%{$search}%");
            })
            ->orderBy('estado', 'desc')
            ->orderBy('nombre')
            ->paginate(20);

        return view('modules.settings.permissions.users.index', compact('usuarios', 'search'));
    }

    /**
     * Mostrar formulario para asignar permisos a usuario.
     */
    public function edit(int $id)
    {
        $usuario = Usuario::with('persona', 'roles.plantilla', 'permisos')->findOrFail($id);
        $permisos = $this->permissionService->getAllPermissionsGroupedByModule(true);
        $plantillas = PlantillaPermiso::where('es_activa', true)
            ->orderBy('orden')
            ->get();

        // Obtener IDs de permisos actuales
        $permisosActuales = $usuario->permisos->pluck('id')->toArray();

        return view('modules.settings.permissions.users.edit', compact(
            'usuario',
            'permisos',
            'plantillas',
            'permisosActuales'
        ));
    }

    /**
     * Asignar permisos a usuario.
     */
    public function update(Request $request, int $id)
    {
        $validated = $request->validate([
            'permisos' => ['array'],
            'permisos.*' => ['integer', 'exists:permisos,id'],
            'plantilla_id' => ['nullable', 'integer', 'exists:plantillas_permisos,id'],
        ]);

        try {
            $usuario = Usuario::findOrFail($id);

            // Si se seleccionó una plantilla, asignar sus permisos
            if (!empty($validated['plantilla_id'])) {
                $this->permissionService->assignTemplateToUser(
                    $usuario,
                    $validated['plantilla_id'],
                    auth()->user()
                );
            }

            // Sincronizar permisos directos
            if (isset($validated['permisos'])) {
                $this->permissionService->syncUserPermissions(
                    $usuario,
                    $validated['permisos'],
                    auth()->user()
                );
            }

            return redirect()
                ->route('settings.permissions.users.index')
                ->with('success', 'Permisos asignados correctamente.');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Asignar plantilla rápida a usuario.
     */
    public function assignTemplate(Request $request)
    {
        $validated = $request->validate([
            'usuario_id' => ['required', 'integer', 'exists:usuarios,id'],
            'plantilla_id' => ['required', 'integer', 'exists:plantillas_permisos,id'],
        ]);

        try {
            $usuario = Usuario::findOrFail($validated['usuario_id']);
            $plantilla = PlantillaPermiso::findOrFail($validated['plantilla_id']);

            $this->permissionService->assignTemplateToUser(
                $usuario,
                $plantilla,
                auth()->user()
            );

            return back()->with('success', "Plantilla '{$plantilla->nombre}' asignada a {$usuario->nombre}.");
        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Ver permisos de un usuario.
     */
    public function show(int $id)
    {
        $usuario = Usuario::with('persona', 'roles', 'permisos')->findOrFail($id);
        $permisosPorModulo = $usuario->getPermisosPorModulo();

        return view('modules.settings.permissions.users.show', compact(
            'usuario',
            'permisosPorModulo'
        ));
    }
}
