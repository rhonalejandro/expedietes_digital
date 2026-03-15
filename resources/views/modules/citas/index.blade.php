@extends('layouts.admin.master')
@section('title', 'Citas')

@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.css">
    <link rel="stylesheet" href="{{ asset('assets/vendor/select/select2.min.css') }}?v={{ time() }}">
    <link rel="stylesheet" href="{{ asset('assets/modules/citas/css/citas.module.css') }}?v={{ time() }}">
    <link rel="stylesheet" href="{{ asset('assets/components/log-actividad/log-actividad.css') }}?v={{ time() }}">
@endpush

@section('content')
<div class="citas-page">

    @include('modules.citas._partials.header')

    <div class="citas-wrapper">

        {{-- Área principal --}}
        <div class="citas-main">

            {{-- FullCalendar (Día / Semana) --}}
            <div id="fc-container" class="citas-calendar-card">
                <div id="calendar"></div>
            </div>

            {{-- Vista Recursos custom (columnas por especialista) --}}
            <div id="recursos-container" style="display:none;"></div>

        </div>

        {{-- Panel lateral --}}
        @include('modules.citas._partials.sidebar')

    </div>

</div>

{{-- Modales --}}
@include('modules.citas._partials.modal-crear')
@include('modules.citas._partials.modal-ver')

{{-- Toast --}}
<div id="toast-container" class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index:9999;"></div>

@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/locales/es.global.min.js"></script>
    <script src="{{ asset('assets/vendor/select/select2.min.js') }}?v={{ time() }}"></script>
    <script src="{{ asset('assets/components/log-actividad/log-actividad.js') }}?v={{ time() }}"></script>
    <script src="{{ asset('assets/modules/citas/js/citas.module.js') }}?v={{ time() }}"></script>
    <script>
        const SUCURSALES_HORARIO = @json($sucursalHorario);

        CitasModule.init({
            csrf            : '{{ csrf_token() }}',
            eventosUrl      : '{{ route('citas.eventos') }}',
            storeUrl        : '{{ route('citas.store') }}',
            baseUrl         : '{{ url('citas') }}',
            sucursalHorario : SUCURSALES_HORARIO,
        });
        MiniCal.init();

        // Select2 en el select de especialistas
        $('#cc-especialista').select2({
            dropdownParent: $('#modal-crear-cita'),
            placeholder: 'Buscar especialista...',
            allowClear: true,
            width: '100%',
            language: { noResults: () => 'Sin resultados' },
        }).on('change', function () {
            // Disparar el evento que usa MiniCal
            this.dispatchEvent(new Event('change'));
        });
    </script>
@endpush
