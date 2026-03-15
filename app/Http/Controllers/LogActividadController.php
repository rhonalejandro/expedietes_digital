<?php

namespace App\Http\Controllers;

use App\Models\LogCita;
use App\Models\LogCliente;
use App\Models\LogEmpresa;
use App\Models\LogEspecialista;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * LogActividadController
 * Endpoint unificado para el componente <x-log-actividad>.
 * GET /log-actividad/{modulo}/{id}?page=N
 *
 * Módulos soportados: pacientes | especialistas | citas | empresa
 */
class LogActividadController extends Controller
{
    private const MODULOS = [
        'pacientes'    => [LogCliente::class,     'cliente_id'],
        'especialistas'=> [LogEspecialista::class, 'especialista_id'],
        'citas'        => [LogCita::class,         'cita_id'],
        'empresa'      => [LogEmpresa::class,      'empresa_id'],
    ];

    public function index(Request $request, string $modulo, int $id): JsonResponse
    {
        abort_unless(array_key_exists($modulo, self::MODULOS), 404, 'Módulo no reconocido.');

        [$modelClass, $fk] = self::MODULOS[$modulo];

        $paginator = $modelClass::where($fk, $id)
            ->with('usuario:id,nombre')
            ->orderByDesc('fecha')
            ->paginate(10);

        // Normalizar cada item para el componente
        $items = collect($paginator->items())->map(fn ($log) => [
            'id'          => $log->id,
            'tipo_accion' => $log->tipo_accion,
            'fecha'       => $log->fecha?->format('Y-m-d H:i:s'),
            'usuario'     => $log->usuario?->nombre ?? ($log->detalles['usuario'] ?? 'Sistema'),
            'ip'          => $log->detalles['ip'] ?? null,
            'detalles'    => $log->detalles ?? [],
        ]);

        return response()->json([
            'data'          => $items,
            'current_page'  => $paginator->currentPage(),
            'last_page'     => $paginator->lastPage(),
            'next_page_url' => $paginator->nextPageUrl(),
            'total'         => $paginator->total(),
        ]);
    }
}
