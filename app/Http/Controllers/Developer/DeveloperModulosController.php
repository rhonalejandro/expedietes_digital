<?php

namespace App\Http\Controllers\Developer;

use App\Http\Controllers\Controller;
use App\Models\Modulo;
use App\Models\Permiso;
use App\Services\Permissions\PermissionService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

/**
 * DeveloperModulosController
 *
 * CRUD de módulos del sistema + gestión de sus acciones (permisos).
 * Solo accesible desde el Developer Panel (/developer).
 *
 * Flujo:
 *   Crear módulo  → registro en `modulos`
 *   Crear acción  → usa PermissionService::registerPermission() → escribe en `permisos`
 *   Editar acción → actualiza directamente el modelo Permiso
 *   Eliminar acción → PermissionService::deletePermission() (falla si está en uso)
 */
class DeveloperModulosController extends Controller
{
    public function __construct(private PermissionService $permissionService) {}

    // ── MÓDULOS: CRUD ─────────────────────────────────────────────────────────

    public function index()
    {
        $modulos = Modulo::ordenados()->withCount([])->get()->map(function ($modulo) {
            $modulo->total_acciones = $modulo->totalAcciones();
            return $modulo;
        });

        $stats = [
            'total_modulos'   => Modulo::count(),
            'total_activos'   => Modulo::activos()->count(),
            'total_permisos'  => Permiso::count(),
        ];

        return view('developer.modulos.index', compact('modulos', 'stats'));
    }

    public function create()
    {
        return view('developer.modulos.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre'      => 'required|string|max:100',
            'slug'        => 'required|string|max:50|unique:modulos,slug|regex:/^[a-z0-9_]+$/',
            'url'         => 'nullable|string|max:255',
            'descripcion' => 'nullable|string|max:500',
            'icono'       => 'nullable|string|max:60',
            'orden'       => 'nullable|integer|min:0|max:999',
        ], [
            'slug.regex' => 'El slug solo puede contener letras minúsculas, números y guiones bajos.',
        ]);

        $data['icono']  = $data['icono']  ?? 'ti ti-box';
        $data['orden']  = $data['orden']  ?? 0;
        $data['activo'] = true;

        $modulo = Modulo::create($data);

        return redirect()->route('developer.modulos.show', $modulo->id)
            ->with('success', "Módulo <strong>{$modulo->nombre}</strong> creado. Ahora agrega sus acciones.");
    }

    public function show(int $id)
    {
        $modulo   = Modulo::findOrFail($id);
        $permisos = $modulo->permisos();

        return view('developer.modulos.show', compact('modulo', 'permisos'));
    }

    public function edit(int $id)
    {
        $modulo = Modulo::findOrFail($id);

        return view('developer.modulos.edit', compact('modulo'));
    }

    public function update(Request $request, int $id)
    {
        $modulo = Modulo::findOrFail($id);

        $data = $request->validate([
            'nombre'      => 'required|string|max:100',
            'slug'        => 'required|string|max:50|unique:modulos,slug,' . $id . '|regex:/^[a-z0-9_]+$/',
            'url'         => 'nullable|string|max:255',
            'descripcion' => 'nullable|string|max:500',
            'icono'       => 'nullable|string|max:60',
            'orden'       => 'nullable|integer|min:0|max:999',
            'activo'      => 'boolean',
        ]);

        // Si el slug cambia, actualizar también en permisos existentes
        if ($modulo->slug !== $data['slug']) {
            Permiso::where('modulo', $modulo->slug)->update(['modulo' => $data['slug']]);
        }

        $modulo->update($data);

        return redirect()->route('developer.modulos.show', $modulo->id)
            ->with('success', 'Módulo actualizado correctamente.');
    }

    public function destroy(int $id)
    {
        $modulo = Modulo::findOrFail($id);

        if ($modulo->totalAcciones() > 0) {
            return back()->with('error',
                "No se puede eliminar el módulo <strong>{$modulo->nombre}</strong> porque tiene "
                . $modulo->totalAcciones() . " acción(es) registrada(s). Elimínalas primero."
            );
        }

        $modulo->delete();

        return redirect()->route('developer.modulos.index')
            ->with('success', "Módulo <strong>{$modulo->nombre}</strong> eliminado.");
    }

    // ── ACCIONES (PERMISOS): Gestión dentro de un módulo ─────────────────────

    public function createAccion(int $moduloId)
    {
        $modulo = Modulo::findOrFail($moduloId);

        return view('developer.acciones.create', compact('modulo'));
    }

    public function storeAccion(Request $request, int $moduloId)
    {
        $modulo = Modulo::findOrFail($moduloId);

        $data = $request->validate([
            'nombre'      => 'required|string|max:100',
            'codigo'      => 'required|string|max:50|regex:/^[a-z0-9_]+$/|unique_with:permisos,modulo=' . $modulo->slug,
            'descripcion' => 'nullable|string|max:500',
            'tipo'        => 'required|in:general,granular',
        ], [
            'codigo.regex' => 'El código solo puede tener letras minúsculas, números y guiones bajos.',
        ]);

        // Verificar unicidad manualmente (unique_with puede no estar disponible)
        $exists = Permiso::where('modulo', $modulo->slug)->where('codigo', $data['codigo'])->exists();
        if ($exists) {
            return back()->withInput()->with('error',
                "Ya existe una acción con el código <strong>{$data['codigo']}</strong> en este módulo."
            );
        }

        $this->permissionService->registerPermission(
            $modulo->slug,
            $data['codigo'],
            $data['nombre'],
            $data['descripcion'] ?? null,
            $data['tipo']
        );

        return redirect()->route('developer.modulos.show', $modulo->id)
            ->with('success', "Acción <strong>{$data['nombre']}</strong> registrada correctamente.");
    }

    public function editAccion(int $moduloId, int $permisoId)
    {
        $modulo  = Modulo::findOrFail($moduloId);
        $permiso = Permiso::where('modulo', $modulo->slug)->findOrFail($permisoId);

        return view('developer.acciones.edit', compact('modulo', 'permiso'));
    }

    public function updateAccion(Request $request, int $moduloId, int $permisoId)
    {
        $modulo  = Modulo::findOrFail($moduloId);
        $permiso = Permiso::where('modulo', $modulo->slug)->findOrFail($permisoId);

        $data = $request->validate([
            'nombre'      => 'required|string|max:100',
            'descripcion' => 'nullable|string|max:500',
            'tipo'        => 'required|in:general,granular',
            'estado'      => 'boolean',
        ]);

        $permiso->update($data);

        return redirect()->route('developer.modulos.show', $modulo->id)
            ->with('success', "Acción <strong>{$permiso->nombre}</strong> actualizada.");
    }

    public function destroyAccion(int $moduloId, int $permisoId)
    {
        $modulo  = Modulo::findOrFail($moduloId);
        $permiso = Permiso::where('modulo', $modulo->slug)->findOrFail($permisoId);

        try {
            $this->permissionService->deletePermission($permisoId);
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect()->route('developer.modulos.show', $modulo->id)
            ->with('success', "Acción eliminada correctamente.");
    }
}
