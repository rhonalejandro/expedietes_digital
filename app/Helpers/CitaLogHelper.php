<?php

namespace App\Helpers;

use App\Models\LogCita;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class CitaLogHelper
{
    public static function creada(int $citaId, array $datos): void
    {
        self::guardar($citaId, 'creacion', ['datos_iniciales' => $datos]);
    }

    public static function editada(int $citaId, array $anterior, array $actual): void
    {
        $cambios = [];
        foreach ($actual as $campo => $valorActual) {
            $prev = ($anterior[$campo] ?? null) === '' ? null : ($anterior[$campo] ?? null);
            $curr = $valorActual === '' ? null : $valorActual;
            if ($prev != $curr) {
                $cambios[$campo] = ['anterior' => $prev, 'actual' => $curr];
            }
        }
        if (empty($cambios)) return;

        self::guardar($citaId, 'edicion', [
            'campos_modificados' => count($cambios),
            'cambios'            => $cambios,
        ]);
    }

    public static function eliminada(int $citaId, string $descripcion): void
    {
        self::guardar($citaId, 'eliminacion', ['descripcion' => $descripcion]);
    }

    public static function estatusCambiado(int $citaId, string $anterior, string $actual): void
    {
        self::guardar($citaId, 'cambio_estatus', [
            'cambios' => ['estatus' => ['anterior' => $anterior, 'actual' => $actual]],
        ]);
    }

    private static function guardar(int $citaId, string $tipoAccion, array $extra): void
    {
        $usuario = Auth::user();

        LogCita::create([
            'cita_id'     => $citaId,
            'usuario_id'  => $usuario?->id ?? 0,
            'tipo_accion' => $tipoAccion,
            'fecha'       => now(),
            'sucursal_id' => $usuario?->sucursales->first()?->id ?? null,
            'detalles'    => array_merge([
                'ip'      => Request::ip(),
                'fecha'   => now()->toDateString(),
                'hora'    => now()->format('H:i:s'),
                'usuario' => $usuario?->nombre ?? 'sistema',
            ], $extra),
        ]);
    }
}
