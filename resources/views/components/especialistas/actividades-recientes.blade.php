@props(['especialistaId'])

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/components/especialistas/actividades-recientes/css/actividades-recientes.component.css') }}?v={{ time() }}">
@endpush

<div class="act-recientes" id="esp-act-recientes-{{ $especialistaId }}">

    <div class="act-recientes__header">
        <i class="ti ti-history"></i>
        <span>Actividades Recientes</span>
    </div>

    <div class="act-recientes__feed" id="esp-act-feed-{{ $especialistaId }}">
        {{-- Items cargados vía JS --}}
    </div>

    <div class="act-recientes__loader d-none" id="esp-act-loader-{{ $especialistaId }}">
        <div class="act-recientes__spinner"></div>
    </div>

    <div class="act-recientes__empty d-none" id="esp-act-empty-{{ $especialistaId }}">
        <i class="ti ti-inbox"></i>
        <p>Sin actividades registradas</p>
    </div>

    <div id="esp-act-sentinel-{{ $especialistaId }}"></div>
</div>

@push('scripts')
<script src="{{ asset('assets/components/especialistas/actividades-recientes/js/actividades-recientes.component.js') }}?v={{ time() }}"></script>
<script>
    EspActividadesRecientes.init({
        especialistaId : {{ $especialistaId }},
        endpoint       : '{{ route('especialistas.actividades', $especialistaId) }}',
        csrfToken      : '{{ csrf_token() }}',
    });
</script>
@endpush
