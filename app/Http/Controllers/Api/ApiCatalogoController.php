<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Especialista;
use App\Models\Servicio;
use App\Models\Sucursal;
use Illuminate\Http\JsonResponse;

class ApiCatalogoController extends Controller
{
    // GET /api/v1/especialistas
    public function especialistas(): JsonResponse
    {
        $especialistas = Especialista::with('persona')
            ->activos()
            ->get()
            ->map(fn($e) => [
                'id'         => $e->id,
                'nombre'     => $e->nombre_completo,
                'profesion'  => $e->profesion,
                'especialidad' => $e->especialidad,
            ]);

        return response()->json($especialistas);
    }

    // GET /api/v1/servicios
    public function servicios(): JsonResponse
    {
        $servicios = Servicio::where('estado', true)
            ->get(['id', 'nombre', 'descripcion', 'precio']);

        return response()->json($servicios);
    }

    // GET /api/v1/sucursales
    public function sucursales(): JsonResponse
    {
        $sucursales = Sucursal::where('estado', true)
            ->get(['id', 'nombre', 'direccion', 'telefono', 'hora_apertura', 'hora_cierre']);

        return response()->json($sucursales);
    }
}
