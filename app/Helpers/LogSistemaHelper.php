<?php

namespace App\Helpers;

use App\Models\LogCita;
use App\Models\LogCliente;
use App\Models\LogEmpresa;
use App\Models\LogEspecialista;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

/**
 * LogSistemaHelper — Auditoría centralizada del sistema.
 *
 * Registra toda acción relevante sobre Citas, Pacientes, Especialistas y Empresa.
 * Cada campo modificado se guarda con su etiqueta legible, valor anterior y valor actual.
 *
 * ─────────────────────────────────────────────────────────────────
 * USO:
 *
 *   // Crear
 *   LogSistemaHelper::logPacientes('creado', $id, actual: $datos);
 *
 *   // Editar
 *   LogSistemaHelper::logPacientes('editado', $id, $anterior, $actual);
 *
 *   // Eliminar
 *   LogSistemaHelper::logPacientes('eliminado', $id, extra: 'Nombre Completo');
 *
 *   // Cambio de estado/estatus
 *   LogSistemaHelper::logPacientes('estado_cambiado', $id,
 *       ['estado' => $estadoAnterior], ['estado' => $estadoActual]);
 *
 * ─────────────────────────────────────────────────────────────────
 * ESTRUCTURA JSON `detalles` para 'editado':
 *   {
 *     "tipo": "edicion",
 *     "ip": "...", "fecha": "...", "hora": "...", "usuario": "...",
 *     "campos_modificados": 2,
 *     "cambios": [
 *       {
 *         "campo":          "identificacion",
 *         "etiqueta":       "Cédula / Identificación",
 *         "valor_anterior": "8-123-456",
 *         "valor_actual":   "8-123-4567"
 *       },
 *       ...
 *     ]
 *   }
 * ─────────────────────────────────────────────────────────────────
 */
class LogSistemaHelper
{
    // ── Mapas de etiquetas ────────────────────────────────────────────────────

    private const ETIQUETAS_PACIENTE = [
        'nombre'              => 'Nombre',
        'apellido'            => 'Apellido',
        'tipo_identificacion' => 'Tipo de Identificación',
        'identificacion'      => 'Cédula / Identificación',
        'fecha_nacimiento'    => 'Fecha de Nacimiento',
        'contacto'            => 'Teléfono / Contacto',
        'email'               => 'Correo Electrónico',
        'direccion'           => 'Dirección',
        'genero'              => 'Género',
        'estado'              => 'Estado',
        'ocupacion'           => 'Ocupación',
        'nacionalidad'        => 'Nacionalidad',
        'seguro_medico'       => 'Seguro Médico',
        'contacto_emergencia' => 'Contacto de Emergencia',
    ];

    private const ETIQUETAS_ESPECIALISTA = [
        'nombre'           => 'Nombre',
        'apellido'         => 'Apellido',
        'tratamiento'      => 'Tratamiento / Título',
        'profesion'        => 'Profesión',
        'especialidad'     => 'Especialidad',
        'num_colegiado'    => 'N° de Colegiado',
        'telefono'         => 'Teléfono del Especialista',
        'email'            => 'Correo Electrónico (Especialista)',
        'email_persona'    => 'Correo Electrónico Personal',
        'contacto_persona' => 'Teléfono Personal',
        'estado'           => 'Estado',
        'firma'            => 'Firma Digital',
    ];

    private const ETIQUETAS_CITA = [
        'especialista_id' => 'Especialista',
        'paciente_id'     => 'Paciente',
        'sucursal_id'     => 'Sucursal',
        'caso_id'         => 'Caso Clínico',
        'servicio_id'     => 'Servicio',
        'nombre_lead'     => 'Nombre del Lead',
        'telefono_lead'   => 'Teléfono del Lead',
        'fecha'           => 'Fecha de la Cita',
        'hora_inicio'     => 'Hora de Inicio',
        'hora_fin'        => 'Hora de Fin',
        'estatus'         => 'Estatus',
        'motivo'          => 'Motivo de Consulta',
        'observaciones'   => 'Observaciones',
        'origen'          => 'Origen de la Cita',
    ];

    private const ETIQUETAS_EMPRESA = [
        'nombre'              => 'Nombre de la Empresa',
        'tipo_identificacion' => 'Tipo de Identificación',
        'identificacion'      => 'RUC / Identificación',
        'telefono'            => 'Teléfono',
        'email'               => 'Correo Electrónico',
        'pagina_web'          => 'Página Web',
        'redes_sociales'      => 'Redes Sociales',
        'estado'              => 'Estado',
        'logo'                => 'Logo Circular',
        'logo_rectangular'    => 'Logo Rectangular',
    ];

    // ── Valores legibles por campo ────────────────────────────────────────────

    private const VALORES_ESTATUS_CITA = [
        'pendiente'  => 'Pendiente',
        'confirmada' => 'Confirmada',
        'atendida'   => 'Atendida',
        'cancelada'  => 'Cancelada',
        'no_asistio' => 'No asistió',
    ];

    private const VALORES_GENERO = [
        'masculino' => 'Masculino',
        'femenino'  => 'Femenino',
        'otro'      => 'Otro',
    ];

    private const VALORES_ORIGEN_CITA = [
        'web'      => 'Web',
        'telefono' => 'Teléfono',
        'chatwoot' => 'Chatwoot',
        'mobile'   => 'Mobile',
    ];

    // ── Métodos públicos ──────────────────────────────────────────────────────

    /**
     * Log de acciones sobre Pacientes.
     *
     * @param string $accion   creado | editado | eliminado | estado_cambiado
     * @param int    $id       ID del paciente
     * @param array  $anterior Valores anteriores (requerido para 'editado' y 'estado_cambiado')
     * @param array  $actual   Valores actuales  (requerido para 'creado', 'editado', 'estado_cambiado')
     * @param string $extra    Texto adicional   (requerido para 'eliminado': nombre completo)
     */
    public static function logPacientes(
        string $accion,
        int    $id,
        array  $anterior = [],
        array  $actual   = [],
        string $extra    = ''
    ): void {
        $detalles = self::_construirDetalles(
            $accion, $anterior, $actual, $extra, self::ETIQUETAS_PACIENTE
        );

        if ($detalles === null) return;

        self::_persistir('cliente', $id, $accion, $detalles);
    }

    /**
     * Log de acciones sobre Especialistas.
     *
     * @param string $accion   creado | editado | eliminado | estado_cambiado
     */
    public static function logEspecialistas(
        string $accion,
        int    $id,
        array  $anterior = [],
        array  $actual   = [],
        string $extra    = ''
    ): void {
        $detalles = self::_construirDetalles(
            $accion, $anterior, $actual, $extra, self::ETIQUETAS_ESPECIALISTA
        );

        if ($detalles === null) return;

        self::_persistir('especialista', $id, $accion, $detalles);
    }

    /**
     * Log de acciones sobre Citas.
     *
     * @param string $accion   creada | editada | eliminada | estatus_cambiado
     */
    public static function logCitas(
        string $accion,
        int    $id,
        array  $anterior = [],
        array  $actual   = [],
        string $extra    = ''
    ): void {
        $detalles = self::_construirDetalles(
            $accion, $anterior, $actual, $extra, self::ETIQUETAS_CITA
        );

        if ($detalles === null) return;

        self::_persistir('cita', $id, $accion, $detalles);
    }

    /**
     * Log de acciones sobre la Empresa (configuración general).
     *
     * @param string $accion   editada | logo_actualizado
     */
    public static function logEmpresa(
        string $accion,
        int    $id,
        array  $anterior = [],
        array  $actual   = [],
        string $extra    = ''
    ): void {
        $detalles = self::_construirDetalles(
            $accion, $anterior, $actual, $extra, self::ETIQUETAS_EMPRESA
        );

        if ($detalles === null) return;

        self::_persistir('empresa', $id, $accion, $detalles);
    }

    // ── Construcción de detalles ──────────────────────────────────────────────

    /**
     * Retorna null si no hay cambios reales (para 'editado'/'editada').
     * Retorna array con la estructura de detalles para los demás casos.
     */
    private static function _construirDetalles(
        string $accion,
        array  $anterior,
        array  $actual,
        string $extra,
        array  $etiquetas
    ): ?array {
        return match (true) {
            in_array($accion, ['creado', 'creada'])              => self::_detallesCreacion($actual, $etiquetas),
            in_array($accion, ['editado', 'editada'])            => self::_detallesEdicion($anterior, $actual, $etiquetas),
            in_array($accion, ['eliminado', 'eliminada'])        => ['tipo' => 'eliminacion', 'nombre_completo' => $extra],
            in_array($accion, ['estado_cambiado'])               => self::_detallesCambioSimple('estado', 'Estado', $anterior['estado'] ?? null, $actual['estado'] ?? null),
            in_array($accion, ['estatus_cambiado'])              => self::_detallesCambioSimple('estatus', 'Estatus', $anterior['estatus'] ?? null, $actual['estatus'] ?? null),
            default                                              => ['tipo' => $accion, 'descripcion' => $extra],
        };
    }

    /** Registra todos los campos iniciales al crear un registro. */
    private static function _detallesCreacion(array $datos, array $etiquetas): array
    {
        $campos = [];
        foreach ($datos as $campo => $valor) {
            $campos[] = [
                'campo'        => $campo,
                'etiqueta'     => $etiquetas[$campo] ?? $campo,
                'valor_actual' => self::_formatearValor($campo, $valor),
            ];
        }

        return [
            'tipo'               => 'creacion',
            'campos_registrados' => count($campos),
            'campos'             => $campos,
        ];
    }

    /**
     * Compara anterior vs actual campo por campo.
     * Retorna null si no hubo diferencias (evita log vacío).
     */
    private static function _detallesEdicion(array $anterior, array $actual, array $etiquetas): ?array
    {
        $cambios = [];

        foreach ($actual as $campo => $valorActual) {
            $prev = ($anterior[$campo] ?? null) === '' ? null : ($anterior[$campo] ?? null);
            $curr = $valorActual              === '' ? null : $valorActual;

            // Comparación loose para evitar falsos positivos (ej. null vs "")
            if ($prev != $curr) {
                $cambios[] = [
                    'campo'          => $campo,
                    'etiqueta'       => $etiquetas[$campo] ?? $campo,
                    'valor_anterior' => self::_formatearValor($campo, $prev),
                    'valor_actual'   => self::_formatearValor($campo, $curr),
                ];
            }
        }

        if (empty($cambios)) return null;

        return [
            'tipo'               => 'edicion',
            'campos_modificados' => count($cambios),
            'cambios'            => $cambios,
        ];
    }

    /** Log de un único campo con valor anterior y actual (estado, estatus). */
    private static function _detallesCambioSimple(
        string $campo,
        string $etiqueta,
        mixed  $anterior,
        mixed  $actual
    ): array {
        return [
            'tipo'               => 'cambio_' . $campo,
            'campos_modificados' => 1,
            'cambios'            => [[
                'campo'          => $campo,
                'etiqueta'       => $etiqueta,
                'valor_anterior' => self::_formatearValor($campo, $anterior),
                'valor_actual'   => self::_formatearValor($campo, $actual),
            ]],
        ];
    }

    // ── Formateador de valores ────────────────────────────────────────────────

    /**
     * Convierte un valor crudo en una representación legible para el log.
     */
    private static function _formatearValor(string $campo, mixed $valor): string
    {
        if (is_null($valor)) return '—';

        // Booleanos → Activo / Inactivo
        if (is_bool($valor)) return $valor ? 'Activo' : 'Inactivo';

        // Enteros 0/1 que representan boolean (ej. estado)
        if ($campo === 'estado' && in_array($valor, [0, 1, '0', '1'], true)) {
            return $valor ? 'Activo' : 'Inactivo';
        }

        // Estatus de cita
        if ($campo === 'estatus') {
            return self::VALORES_ESTATUS_CITA[$valor] ?? (string) $valor;
        }

        // Género
        if ($campo === 'genero') {
            return self::VALORES_GENERO[$valor] ?? (string) $valor;
        }

        // Origen de la cita
        if ($campo === 'origen') {
            return self::VALORES_ORIGEN_CITA[$valor] ?? (string) $valor;
        }

        // Arrays / JSON (ej. redes_sociales)
        if (is_array($valor)) {
            return json_encode($valor, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }

        return (string) $valor;
    }

    // ── Persistencia ─────────────────────────────────────────────────────────

    /** Meta-datos comunes a todos los logs (IP, usuario, timestamp). */
    private static function _meta(): array
    {
        $usuario = Auth::user();
        return [
            'ip'      => Request::ip(),
            'fecha'   => now()->toDateString(),
            'hora'    => now()->format('H:i:s'),
            'usuario' => $usuario?->nombre ?? 'sistema',
        ];
    }

    /**
     * Persiste el log en la tabla correspondiente según el módulo.
     *
     * @param string $modulo  cita | cliente | especialista | empresa
     */
    private static function _persistir(string $modulo, int $id, string $accion, array $detalles): void
    {
        $usuario    = Auth::user();
        $sucursalId = $usuario?->sucursales->first()?->id ?? null;
        $detalles   = array_merge(self::_meta(), $detalles);

        match ($modulo) {
            'cita' => LogCita::create([
                'cita_id'     => $id,
                'usuario_id'  => $usuario?->id ?? 0,
                'tipo_accion' => $accion,
                'fecha'       => now(),
                'sucursal_id' => $sucursalId,
                'detalles'    => $detalles,
            ]),

            'cliente' => LogCliente::create([
                'cliente_id'  => $id,
                'usuario_id'  => $usuario?->id ?? 0,
                'tipo_accion' => $accion,
                'fecha'       => now(),
                'sucursal_id' => $sucursalId,
                'detalles'    => $detalles,
            ]),

            'especialista' => LogEspecialista::create([
                'especialista_id' => $id,
                'usuario_id'      => $usuario?->id ?? 0,
                'tipo_accion'     => $accion,
                'fecha'           => now(),
                'sucursal_id'     => $sucursalId,
                'detalles'        => $detalles,
            ]),

            'empresa' => LogEmpresa::create([
                'empresa_id'  => $id,
                'usuario_id'  => $usuario?->id ?? 0,
                'tipo_accion' => $accion,
                'fecha'       => now(),
                'sucursal_id' => $sucursalId,
                'detalles'    => $detalles,
            ]),
        };
    }
}
