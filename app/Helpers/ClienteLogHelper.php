<?php

namespace App\Helpers;

use App\Models\LogCliente;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class ClienteLogHelper
{
    /**
     * Registra la creación de un paciente/cliente.
     */
    public static function creado(int $clienteId, array $datos): void
    {
        self::guardar($clienteId, 'creacion', [
            'datos_iniciales' => $datos,
        ]);
    }

    /**
     * Registra la edición de un paciente/cliente.
     * Detecta automáticamente qué campos cambiaron, valor anterior y actual.
     *
     * @param int   $clienteId
     * @param array $anterior  Valores antes de editar
     * @param array $actual    Valores después de editar
     */
    public static function editado(int $clienteId, array $anterior, array $actual): void
    {
        $cambios = [];

        foreach ($actual as $campo => $valorActual) {
            $valorAnterior = $anterior[$campo] ?? null;

            // Normalizar para comparación justa
            $prev = $valorAnterior === '' ? null : $valorAnterior;
            $curr = $valorActual  === '' ? null : $valorActual;

            if ($prev != $curr) {
                $cambios[$campo] = [
                    'anterior' => $prev,
                    'actual'   => $curr,
                ];
            }
        }

        if (empty($cambios)) {
            return; // No hubo cambios reales, no registrar
        }

        self::guardar($clienteId, 'edicion', [
            'campos_modificados' => count($cambios),
            'cambios'            => $cambios,
        ]);
    }

    /**
     * Registra la eliminación de un paciente/cliente.
     */
    public static function eliminado(int $clienteId, string $nombreCompleto): void
    {
        self::guardar($clienteId, 'eliminacion', [
            'nombre_completo' => $nombreCompleto,
        ]);
    }

    /**
     * Registra el cambio de estado (activar/desactivar).
     */
    public static function estadoCambiado(int $clienteId, bool $estadoAnterior, bool $estadoActual): void
    {
        self::guardar($clienteId, 'cambio_estado', [
            'cambios' => [
                'estado' => [
                    'anterior' => $estadoAnterior ? 'activo' : 'inactivo',
                    'actual'   => $estadoActual   ? 'activo' : 'inactivo',
                ],
            ],
        ]);
    }

    /**
     * Método central que persiste el log en la BD.
     */
    private static function guardar(int $clienteId, string $tipoAccion, array $detallesExtra): void
    {
        $usuario = Auth::user();

        $detalles = array_merge([
            'ip'       => Request::ip(),
            'fecha'    => now()->toDateString(),
            'hora'     => now()->format('H:i:s'),
            'usuario'  => $usuario ? $usuario->nombre : 'sistema',
        ], $detallesExtra);

        LogCliente::create([
            'cliente_id'  => $clienteId,
            'usuario_id'  => $usuario?->id ?? 0,
            'tipo_accion' => $tipoAccion,
            'fecha'       => now(),
            'sucursal_id' => $usuario?->sucursales->first()?->id ?? null,
            'detalles'    => $detalles,
        ]);
    }
}
