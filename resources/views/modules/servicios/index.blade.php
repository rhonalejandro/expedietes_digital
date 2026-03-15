@extends('layouts.admin.master')
@section('title', 'Servicios')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/modules/servicios/css/servicios.module.css') }}?v={{ time() }}">
@endpush

@section('content')
<div class="container-fluid">

    <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
        <div>
            <h4 class="fw-semibold text-dark mb-1">Servicios</h4>
            <p class="text-muted mb-0" style="font-size:.85rem;">Catálogo de servicios clínicos ofrecidos</p>
        </div>
    </div>

    @include('modules.servicios._partials.stats')
    @include('modules.servicios._partials.toolbar')
    @include('modules.servicios._partials.table')
    @include('modules.servicios._partials.modales')

</div>

<div id="toast-container" class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index:9999;"></div>
@endsection

@push('scripts')
    <script src="{{ asset('assets/modules/servicios/js/servicios.module.js') }}?v={{ time() }}"></script>
    <script>
        ServiciosModule.init({
            csrf     : '{{ csrf_token() }}',
            baseUrl  : '{{ url('servicios') }}',
            storeUrl : '{{ route('servicios.store') }}',
        });
    </script>
@endpush
