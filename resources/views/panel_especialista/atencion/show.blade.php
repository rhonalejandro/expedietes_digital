@extends('panel_especialista.layouts.master')

@section('title', 'Atención — ' . $cita->nombre_paciente)
@section('page-title', 'Atención del Paciente')

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/panel_especialista/css/atencion.css') }}?v={{ time() }}">
@endpush

@section('content')
<div class="container-fluid py-3">

    {{-- ── Breadcrumb + botón volver ────────────────────────────────── --}}
    <div class="d-flex align-items-center gap-2 mb-3">
        <a href="{{ route('panel.agenda', ['fecha' => is_object($cita->fecha) ? $cita->fecha->toDateString() : $cita->fecha]) }}"
           class="btn btn-sm btn-outline-secondary d-flex align-items-center gap-1">
            <i class="ti ti-arrow-left"></i> Volver a la agenda
        </a>
        <span class="text-muted">›</span>
        <span class="fw-semibold">{{ $cita->nombre_paciente }}</span>
        <span class="badge bg-light text-secondary border">
            {{ substr($cita->hora_inicio, 0, 5) }} – {{ substr($cita->hora_fin, 0, 5) }}
        </span>
    </div>

    <div class="row g-3">

        {{-- ════════════════════════════════════════
             Columna izquierda: Paciente + Historial
             ════════════════════════════════════════ --}}
        <div class="col-lg-4">

            {{-- Tarjeta del paciente --}}
            @include('panel_especialista.atencion._partials.paciente-card', ['cita' => $cita])

            {{-- Historial de casos/consultas --}}
            @include('panel_especialista.atencion._partials.historial', compact('casos'))

        </div>

        {{-- ════════════════════════════════════════
             Columna derecha: Formulario de consulta
             ════════════════════════════════════════ --}}
        <div class="col-lg-8">
            @include('panel_especialista.atencion._partials.form-consulta', compact('cita', 'casos', 'casoAbierto'))
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('assets/panel_especialista/js/atencion.js') }}?v={{ time() }}"></script>
@endpush
