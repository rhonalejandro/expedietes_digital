@php
$colores = [
    'pendiente'  => ['bg' => '#fef3c7', 'text' => '#92400e', 'dot' => '#f59e0b'],
    'confirmada' => ['bg' => '#d1fae5', 'text' => '#065f46', 'dot' => '#10b981'],
    'atendida'   => ['bg' => '#e0e7ff', 'text' => '#3730a3', 'dot' => '#6366f1'],
    'no_asistio' => ['bg' => '#fee2e2', 'text' => '#991b1b', 'dot' => '#ef4444'],
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
            {{ $nombre }}
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
        @elseif($cita->estatus === 'atendida')
            <span class="pnl-atendida-label">
                <i class="ti ti-circle-check"></i> Atendida
            </span>
        @endif
    </div>

</div>
