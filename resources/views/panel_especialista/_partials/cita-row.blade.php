@php
$colores = [
    'pendiente'   => ['bg' => '#fef3c7', 'text' => '#92400e', 'dot' => '#f59e0b'],
    'confirmada'  => ['bg' => '#d1fae5', 'text' => '#065f46', 'dot' => '#10b981'],
    'en_consulta' => ['bg' => '#dbeafe', 'text' => '#1e40af', 'dot' => '#2563eb'],
    'atendida'    => ['bg' => '#e0e7ff', 'text' => '#3730a3', 'dot' => '#6366f1'],
    'no_asistio'  => ['bg' => '#fee2e2', 'text' => '#991b1b', 'dot' => '#ef4444'],
];
$c      = $colores[$cita->estatus] ?? $colores['pendiente'];
$nombre = $cita->paciente?->persona->nombre . ' ' . $cita->paciente?->persona->apellido
        ?: ($cita->nombre_lead ?: 'Paciente sin nombre');
@endphp

<div class="pnl-cita-row" data-estatus="{{ $cita->estatus }}">

    {{-- Hora --}}
    <div class="pnl-cita-hora">
        <span class="pnl-cita-hi">{{ substr($cita->hora_inicio, 0, 5) }}</span>
        <span class="pnl-cita-sep">–</span>
        <span class="pnl-cita-hf">{{ substr($cita->hora_fin, 0, 5) }}</span>
    </div>

    {{-- Info --}}
    <div class="pnl-cita-info">
        <div class="pnl-cita-nombre">
            <i class="ti ti-user-circle"></i>
            @if($cita->paciente_id)
                <a href="{{ route('panel.paciente.show', $cita->paciente_id) }}"
                   style="color:inherit;text-decoration:none;border-bottom:1px dashed currentColor;">{{ $nombre }}</a>
            @else
                {{ $nombre }}
            @endif
        </div>
        @if($cita->servicio)
            <div class="pnl-cita-servicio">
                <i class="ti ti-stethoscope"></i>
                {{ $cita->servicio->nombre }}
            </div>
        @endif
        @if($cita->motivo)
            <div class="pnl-cita-motivo">
                <i class="ti ti-notes"></i>
                {{ \Illuminate\Support\Str::limit($cita->motivo, 80) }}
            </div>
        @endif
    </div>

    {{-- Estatus --}}
    <div class="pnl-cita-estatus">
        <span class="pnl-badge" style="background:{{ $c['bg'] }};color:{{ $c['text'] }}">
            <span class="pnl-badge-dot" style="background:{{ $c['dot'] }}"></span>
            {{ ucfirst(str_replace('_',' ',$cita->estatus)) }}
        </span>
    </div>

    {{-- Acción --}}
    <div class="pnl-cita-accion">
        @if(in_array($cita->estatus, ['pendiente','confirmada']))
            <a href="{{ route('panel.atencion.show', $cita->id) }}" class="pnl-btn-atender">
                <i class="ti ti-stethoscope"></i>
                Atender
            </a>
        @elseif($cita->estatus === 'en_consulta')
            <a href="{{ route('panel.atencion.show', $cita->id) }}" class="pnl-btn-atender" style="background:#2563eb">
                <i class="ti ti-activity-heartbeat"></i>
                En Consulta
            </a>
        @elseif($cita->estatus === 'atendida')
            @if($cita->consulta && $cita->paciente_id)
                <div style="display:flex;gap:.35rem">
                    <a href="{{ route('panel.paciente.consulta.pdf', ['pacienteId' => $cita->paciente_id, 'consultaId' => $cita->consulta->id]) }}"
                       target="_blank"
                       style="font-size:.7rem;padding:.2rem .6rem;display:inline-flex;align-items:center;gap:.25rem;border:1px solid #198754;background:#198754;color:#fff;border-radius:5px;text-decoration:none;">
                        <i class="ti ti-file-type-pdf"></i> Ficha
                    </a>
                    @if($cita->consulta->receta || $cita->consulta->indicaciones)
                    <a href="{{ route('panel.paciente.consulta.receta', ['pacienteId' => $cita->paciente_id, 'consultaId' => $cita->consulta->id]) }}"
                       target="_blank"
                       style="font-size:.7rem;padding:.2rem .6rem;display:inline-flex;align-items:center;gap:.25rem;border:1px solid #ffc107;background:#ffc107;color:#000;border-radius:5px;text-decoration:none;">
                        <i class="ti ti-prescription"></i> Receta
                    </a>
                    @endif
                </div>
            @else
                <span class="pnl-atendida-label">
                    <i class="ti ti-circle-check"></i> Atendida
                </span>
            @endif
        @endif
    </div>

</div>
