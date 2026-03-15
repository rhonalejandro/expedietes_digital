<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\UpdateEmpresaRequest;
use App\Services\Settings\EmpresaService;

/**
 * EmpresaController
 * 
 * Controlador para gestión de configuración de empresa.
 * Principio Single Responsibility: Solo maneja lógica de empresa.
 */
class EmpresaController extends Controller
{
    /**
     * @var EmpresaService
     */
    private EmpresaService $service;

    /**
     * Constructor con inyección de dependencias.
     * 
     * @param EmpresaService $service
     */
    public function __construct(EmpresaService $service)
    {
        $this->service = $service;
    }

    /**
     * Mostrar formulario de empresa.
     * 
     * @return \Illuminate\View\View
     */
    public function edit()
    {
        $empresa = $this->service->getOrCreateDefault();
        
        return view('modules.settings.empresa', compact('empresa'));
    }

    /**
     * Actualizar información de empresa.
     *
     * @param UpdateEmpresaRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateEmpresaRequest $request)
    {
        try {
            $empresa = $this->service->updateEmpresa(
                $request->validated(),
                $request->file('logo'),
                $request->file('logo_rectangular')
            );

            // Redirigir con un mensaje de éxito
            // Los datos se recargarán desde el controller index
            return redirect()
                ->route('settings.index')
                ->with('success', 'Información de la empresa actualizada correctamente.');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Error al actualizar la empresa: ' . $e->getMessage());
        }
    }
}
