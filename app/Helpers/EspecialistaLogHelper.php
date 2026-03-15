<?php

namespace App\Helpers;

use App\Models\LogEspecialista;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class EspecialistaLogHelper
{
    public static function creado(int $especialistaId, array $datos): void
    {
        self::guardar($especialistaId, 'creacion', [
            'datos_iniciales' => $datos,
        ]);
    }

    public static function editado(int $especialistaId, array $anterior, array $actual): void
    {
        $cambios = [];

        foreach ($actual as $campo => $valorActual) {
            $prev = ($anterior[$campo] ?? null) === '' ? null : ($anterior[$campo] ?? null);
            $curr = $valorActual === '' ? null : $valorActual;

            if ($prev != $curr) {
                $cambios[$campo] = [
                    'anterior' => $prev,
                    'actual'   => $curr,
                ];
            }
        }

        if (empty($cambios)) {
            return;
        }

        self::guardar($especialistaId, 'edicion', [
            'campos_modificados' => count($cambios),
            'cambios'            => $cambios,
        ]);
    }

    public static function eliminado(int $especialistaId, string $nombreCompleto): void
    {
        self::guardar($especialistaId, 'eliminacion', [
            'nombre_completo' => $nombreCompleto,
        ]);
    }

    public static function estadoCambiado(int $especialistaId, bool $estadoAnterior, bool $estadoActual): void
    {
        self::guardar($especialistaId, 'cambio_estado', [
            'cambios' => [
                'estado' => [
                    'anterior' => $estadoAnterior ? 'activo' : 'inactivo',
                    'actual'   => $estadoActual   ? 'activo' : 'inactivo',
                ],
            ],
        ]);
    }

    private static function guardar(int $especialistaId, string $tipoAccion, array $detallesExtra): void
    {
        $usuario = Auth::user();

        $detalles = array_merge([
            'ip'      => Request::ip(),
            'fecha'   => now()->toDateString(),
            'hora'    => now()->format('H:i:s'),
            'usuario' => $usuario ? $usuario->nombre : 'sistema',
        ], $detallesExtra);

        LogEspecialista::create([
            'especialista_id' => $especialistaId,
            'usuario_id'      => $usuario?->id ?? 0,
            'tipo_accion'     => $tipoAccion,
            'fecha'           => now(),
            'sucursal_id'     => $usuario?->sucursales->first()?->id ?? null,
            'detalles'        => $detalles,
        ]);
    }
}
