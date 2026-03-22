<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\Empresa;
use App\Services\Settings\EmpresaService;
use App\Services\Settings\SucursalService;
use Illuminate\Http\Request;

/**
 * SettingsController
 * 
 * Controlador principal para configuración.
 * Principio Single Responsibility: Solo orquesta la vista principal.
 */
class SettingsController extends Controller
{
    /**
     * @var EmpresaService
     */
    private EmpresaService $empresaService;

    /**
     * @var SucursalService
     */
    private SucursalService $sucursalService;

    /**
     * Constructor con inyección de dependencias.
     * 
     * @param EmpresaService $empresaService
     * @param SucursalService $sucursalService
     */
    public function __construct(
        EmpresaService $empresaService,
        SucursalService $sucursalService
    ) {
        $this->empresaService = $empresaService;
        $this->sucursalService = $sucursalService;
    }

    /**
     * Mostrar página principal de configuración.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $empresa = $this->empresaService->getEmpresa();
        $sucursales = $this->sucursalService->getAll();

        $stats = [
            'total_sucursales' => $this->sucursalService->countTotal(),
            'sucursales_activas' => $this->sucursalService->countActive(),
        ];

        return view('modules.settings.index', compact('empresa', 'sucursales', 'stats'));
    }

    public function updateCitas(Request $request)
    {
        $request->validate([
            'modo_agenda' => ['required', 'in:estricto,sobrecarga'],
        ]);

        $empresa = Empresa::first();
        if ($empresa) {
            $empresa->modo_agenda = $request->modo_agenda;
            $empresa->save();
        }

        return back()->with('success', 'Configuración de citas guardada correctamente.');
    }
}
