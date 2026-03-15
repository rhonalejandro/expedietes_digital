<?php

namespace App\Http\Controllers;

use App\Models\Servicio;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ServicioController extends Controller
{
    // ── Listado ───────────────────────────────────────────────────────────────

    public function index(Request $request)
    {
        $query = Servicio::query();

        if ($request->filled('q')) {
            $query->where('nombre', 'ilike', "%{$request->q}%")
                  ->orWhere('descripcion', 'ilike', "%{$request->q}%");
        }

        if ($request->filled('estado') && in_array($request->estado, ['0', '1'], true)) {
            $query->where('estado', (bool) $request->estado);
        }

        $servicios = $query->orderBy('nombre')->paginate(15)->withQueryString();

        $stats = [
            'total'   => Servicio::count(),
            'activos' => Servicio::activos()->count(),
            'precio_promedio' => Servicio::activos()->avg('precio') ?? 0,
        ];

        return view('modules.servicios.index', compact('servicios', 'stats'));
    }

    // ── Guardar (modal crear) ─────────────────────────────────────────────────

    public function store(Request $request): JsonResponse
    {
        $data = $this->validar($request);

        $servicio = Servicio::create($data);

        return response()->json([
            'success'  => true,
            'servicio' => $servicio,
            'mensaje'  => 'Servicio creado correctamente.',
        ], 201);
    }

    // ── Obtener para modal editar ─────────────────────────────────────────────

    public function show(int $id): JsonResponse
    {
        return response()->json(Servicio::findOrFail($id));
    }

    // ── Actualizar (modal editar) ─────────────────────────────────────────────

    public function update(Request $request, int $id): JsonResponse
    {
        $servicio = Servicio::findOrFail($id);
        $data = $this->validar($request, $id);

        $servicio->update($data);

        return response()->json([
            'success'  => true,
            'servicio' => $servicio->fresh(),
            'mensaje'  => 'Servicio actualizado correctamente.',
        ]);
    }

    // ── Toggle estado ─────────────────────────────────────────────────────────

    public function toggleEstado(int $id): JsonResponse
    {
        $servicio = Servicio::findOrFail($id);
        $servicio->update(['estado' => !$servicio->estado]);

        return response()->json([
            'success' => true,
            'estado'  => $servicio->estado,
            'mensaje' => $servicio->estado ? 'Servicio activado.' : 'Servicio desactivado.',
        ]);
    }

    // ── Eliminar ──────────────────────────────────────────────────────────────

    public function destroy(int $id): JsonResponse
    {
        $servicio = Servicio::findOrFail($id);

        // Verificar si tiene citas asociadas
        if ($servicio->citas()->exists()) {
            return response()->json([
                'success' => false,
                'mensaje' => 'No se puede eliminar: el servicio tiene citas asociadas.',
            ], 422);
        }

        $servicio->delete();

        return response()->json(['success' => true, 'mensaje' => 'Servicio eliminado.']);
    }

    // ── Validación centralizada ───────────────────────────────────────────────

    private function validar(Request $request, ?int $id = null): array
    {
        return $request->validate([
            'nombre'      => 'required|string|max:100|unique:servicios,nombre' . ($id ? ",{$id}" : ''),
            'descripcion' => 'nullable|string|max:500',
            'precio'      => 'required|numeric|min:0',
            'estado'      => 'boolean',
        ]);
    }
}
