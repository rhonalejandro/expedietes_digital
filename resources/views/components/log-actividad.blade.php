@props([
    'modulo',      // pacientes | especialistas | citas | empresa
    'registroId',  // ID del registro a consultar
    'titulo' => 'Historial de Actividad',
])

@php
    $uid = 'la-' . $modulo . '-' . $registroId;
@endphp

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/components/log-actividad/log-actividad.css') }}?v={{ time() }}">
@endpush

<div class="la-wrap" id="{{ $uid }}">

    <div class="la-header">
        <i class="ti ti-history la-header-icon"></i>
        <span class="la-header-title">{{ $titulo }}</span>
        <span class="la-total-badge" id="{{ $uid }}-badge">—</span>
    </div>

    {{-- Timeline feed --}}
    <div class="la-feed" id="{{ $uid }}-feed"></div>

    {{-- Sentinel para infinite scroll --}}
    <div class="la-sentinel" id="{{ $uid }}-sentinel">
        <span class="la-spinner" id="{{ $uid }}-spinner" style="display:none"></span>
    </div>

    {{-- Estado vacío --}}
    <div class="la-empty" id="{{ $uid }}-empty" style="display:none">
        <i class="ti ti-clipboard-off"></i>
        <p>Sin actividades registradas aún.</p>
    </div>

</div>

@push('scripts')
<script src="{{ asset('assets/components/log-actividad/log-actividad.js') }}?v={{ time() }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    LogActividad.init({
        uid       : '{{ $uid }}',
        modulo    : '{{ $modulo }}',
        registroId: {{ $registroId }},
        endpoint  : '{{ route('log.actividad', [$modulo, $registroId]) }}',
        csrf      : '{{ csrf_token() }}',
    });
});
</script>
@endpush
