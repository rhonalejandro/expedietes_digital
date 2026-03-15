@props(['clienteId'])

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/components/pacientes/actividades-recientes/css/actividades-recientes.component.css') }}?v={{ time() }}">
@endpush

<div class="act-recientes" id="act-recientes-{{ $clienteId }}">

    <div class="act-recientes__header">
        <i class="ti ti-history"></i>
        <span>Actividades Recientes</span>
    </div>

    <div class="act-recientes__feed" id="act-feed-{{ $clienteId }}">
        {{-- Los items se cargan vía JS --}}
    </div>

    <div class="act-recientes__loader d-none" id="act-loader-{{ $clienteId }}">
        <div class="act-recientes__spinner"></div>
    </div>

    <div class="act-recientes__empty d-none" id="act-empty-{{ $clienteId }}">
        <i class="ti ti-inbox"></i>
        <p>Sin actividades registradas</p>
    </div>

    {{-- Sentinel para Intersection Observer --}}
    <div id="act-sentinel-{{ $clienteId }}"></div>
</div>

@push('scripts')
<script src="{{ asset('assets/components/pacientes/actividades-recientes/js/actividades-recientes.component.js') }}?v={{ time() }}"></script>
<script>
    ActividadesRecientes.init({
        clienteId : {{ $clienteId }},
        endpoint  : '{{ route('pacientes.actividades', $clienteId) }}',
        csrfToken : '{{ csrf_token() }}',
    });
</script>
@endpush
