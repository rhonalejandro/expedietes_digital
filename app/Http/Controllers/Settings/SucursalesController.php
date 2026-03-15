<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\StoreSucursalRequest;
use App\Http\Requests\Settings\UpdateSucursalRequest;
use App\Services\Settings\SucursalService;

/**
 * SucursalesController
 * 
 * Controlador para gestión de sucursales.
 * Principio Single Responsibility: Solo maneja lógica de sucursales.
 */
class SucursalesController extends Controller
{
    /**
     * @var SucursalService
     */
    private SucursalService $service;

    /**
     * Constructor con inyección de dependencias.
     * 
     * @param SucursalService $service
     */
    public function __construct(SucursalService $service)
    {
        $this->service = $service;
    }

    /**
     * Mostrar lista de sucursales.
     * 
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $sucursales = $this->service->getAll();
        $total = $this->service->countTotal();
        $activas = $this->service->countActive();
        
        return view('modules.settings.sucursales', compact('sucursales', 'total', 'activas'));
    }

    /**
     * Crear nueva sucursal.
     *
     * @param StoreSucursalRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StoreSucursalRequest $request)
    {
        try {
            $this->service->createSucursal(
                $request->validated(),
                $request->file('imagen')
            );

            return back()->with('success', 'Sucursal creada correctamente.');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Error al crear la sucursal: ' . $e->getMessage());
        }
    }

    /**
     * Actualizar sucursal.
     *
     * @param UpdateSucursalRequest $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateSucursalRequest $request, int $id)
    {
        try {
            $this->service->updateSucursal(
                $id,
                $request->validated(),
                $request->file('imagen')
            );

            return back()->with('success', 'Sucursal actualizada correctamente.');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Error al actualizar la sucursal: ' . $e->getMessage());
        }
    }

    /**
     * Eliminar sucursal.
     * 
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(int $id)
    {
        try {
            $this->service->deleteSucursal($id);

            return back()->with('success', 'Sucursal eliminada correctamente.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al eliminar la sucursal: ' . $e->getMessage());
        }
    }

    /**
     * Cambiar estado de sucursal.
     * 
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function toggleStatus(int $id)
    {
        try {
            $this->service->toggleStatus($id);

            return back()->with('success', 'Estado de sucursal actualizado.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al cambiar el estado: ' . $e->getMessage());
        }
    }
}
