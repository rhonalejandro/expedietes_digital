@php
    $p       = $cita->paciente?->persona;
    $nombre  = $cita->nombre_paciente;
    $iniciales = collect(explode(' ', $nombre))->map(fn($w) => strtoupper(substr($w,0,1)))->take(2)->implode('');
    $edad    = $p?->fecha_nacimiento ? \Carbon\Carbon::parse($p->fecha_nacimiento)->age : null;
@endphp

<div class="atc-card mb-3">
    <div class="atc-card-header">
        <i class="ti ti-user-circle me-2"></i> Paciente
    </div>
    <div class="atc-card-body">

        <div class="atc-paciente-top">
            <div class="atc-avatar">{{ $iniciales }}</div>
            <div>
                <div class="atc-paciente-nombre">
                    @if($cita->paciente_id)
                        <a href="{{ route('panel.paciente.show', $cita->paciente_id) }}"
                           style="color:inherit;text-decoration:none;border-bottom:1px dashed currentColor;">
                            {{ $nombre }}
                        </a>
                    @else
                        {{ $nombre }}
                    @endif
                </div>
                @if($p?->identificacion && $p->identificacion !== 'N/A')
                    <div class="atc-paciente-meta"><i class="ti ti-id-badge"></i> {{ $p->identificacion }}</div>
                @endif
                @if($edad)
                    <div class="atc-paciente-meta"><i class="ti ti-calendar-user"></i> {{ $edad }} años</div>
                @endif
            </div>
        </div>

        @if($cita->motivo)
        <div class="atc-motivo-box mt-3">
            <div class="atc-motivo-label"><i class="ti ti-notes me-1"></i> Motivo de la cita</div>
            <div class="atc-motivo-text">{{ $cita->motivo }}</div>
        </div>
        @endif

        @if($cita->servicio)
        <div class="atc-servicio-box mt-2">
            <i class="ti ti-stethoscope me-1"></i>
            <span>{{ $cita->servicio->nombre }}</span>
        </div>
        @endif

    </div>
</div>
