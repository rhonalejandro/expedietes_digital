<?php

/**
 * Desarrollo - Permisos de Usuarios - UI - 2026-02-18
 * 
 * Controlador: PermissionsController
 * 
 * Propósito: Gestionar CRUD de permisos desde la UI
 * 
 * Principios:
 * - Single Responsibility: Solo gestiona permisos
 * - Dependency Injection: Usa PermissionService
 * 
 * @package App\Http\Controllers\Settings
 */

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\StorePermissionRequest;
use App\Http\Requests\Settings\UpdatePermissionRequest;
use App\Services\Permissions\PermissionService;
use Illuminate\Http\Request;

/**
 * Class PermissionsController
 */
class PermissionsController extends Controller
{
    /**
     * @var PermissionService
     */
    private PermissionService $service;

    /**
     * Constructor con inyección de dependencias.
     */
    public function __construct(PermissionService $service)
    {
        $this->service = $service;
    }

    /**
     * Mostrar lista de permisos.
     */
    public function index(Request $request)
    {
        $modulo = $request->get('modulo', 'all');
        $tipo = $request->get('tipo', 'all');
        
        $permisos = $this->service->getAllPermissionsGroupedByModule(true);
        $modulos = array_keys($permisos);
        
        // Filtrar por módulo si se seleccionó uno
        if ($modulo !== 'all' && isset($permisos[$modulo])) {
            $permisos = [$modulo => $permisos[$modulo]];
        }
        
        // Filtrar por tipo
        if ($tipo !== 'all') {
            foreach ($permisos as $mod => &$perms) {
                $perms = array_filter($perms, fn($p) => $p['tipo'] === $tipo);
            }
            $permisos = array_filter($permisos, fn($p) => !empty($p));
        }
        
        $stats = $this->service->getStats();
        
        return view('modules.settings.permissions.index', compact(
            'permisos',
            'modulos',
            'modulo',
            'tipo',
            'stats'
        ));
    }

    /**
     * Mostrar formulario para crear permiso.
     */
    public function create()
    {
        $modulos = $this->getModulosList();
        
        return view('modules.settings.permissions.create', compact('modulos'));
    }

    /**
     * Guardar nuevo permiso.
     */
    public function store(StorePermissionRequest $request)
    {
        try {
            $this->service->registerPermission(
                $request->validated('modulo'),
                $request->validated('codigo'),
                $request->validated('nombre'),
                $request->validated('descripcion'),
                $request->validated('tipo')
            );

            return redirect()
                ->route('settings.permissions.index')
                ->with('success', 'Permiso creado correctamente.');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Error al crear el permiso: ' . $e->getMessage());
        }
    }

    /**
     * Mostrar formulario para editar permiso.
     */
    public function edit(int $id)
    {
        $permiso = \App\Models\Permiso::findOrFail($id);
        $modulos = $this->getModulosList();
        
        return view('modules.settings.permissions.edit', compact('permiso', 'modulos'));
    }

    /**
     * Actualizar permiso.
     */
    public function update(UpdatePermissionRequest $request, int $id)
    {
        try {
            $permiso = \App\Models\Permiso::findOrFail($id);
            $permiso->update($request->validated());

            return redirect()
                ->route('settings.permissions.index')
                ->with('success', 'Permiso actualizado correctamente.');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Error al actualizar: ' . $e->getMessage());
        }
    }

    /**
     * Activar permiso.
     */
    public function toggleStatus(int $id)
    {
        try {
            $permiso = \App\Models\Permiso::findOrFail($id);
            $permiso->update(['estado' => !$permiso->estado]);

            return back()->with('success', 'Estado actualizado.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Eliminar permiso.
     */
    public function destroy(int $id)
    {
        try {
            $this->service->deletePermission($id);

            return back()->with('success', 'Permiso eliminado.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Obtener lista de módulos disponibles.
     */
    private function getModulosList(): array
    {
        return [
            'settings' => 'Configuración',
            'users' => 'Usuarios',
            'permissions' => 'Permisos',
            'clients' => 'Clientes',
            'specialists' => 'Especialistas',
            'appointments' => 'Citas',
            'cases' => 'Casos',
            'medical_records' => 'Expedientes',
            'services' => 'Servicios',
            'products' => 'Productos',
            'payments' => 'Pagos',
            'reports' => 'Reportes',
            'branches' => 'Sucursales',
        ];
    }
}
