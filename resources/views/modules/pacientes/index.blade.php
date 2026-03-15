@extends('layouts.admin.master')

@section('title', 'Pacientes')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/modules/pacientes/css/pacientes.module.css') }}?v={{ time() }}">
@endpush

@section('content')
<div class="container-fluid">


    @include('modules.pacientes._partials.alerts')

    @include('modules.pacientes._partials.stats')

    @include('modules.pacientes._partials.toolbar')

    @include('modules.pacientes._partials.table')

    @include('modules.pacientes._partials.modal-delete')

</div>
@endsection

@push('scripts')
    <script src="{{ asset('assets/modules/pacientes/js/pacientes.module.js') }}?v={{ time() }}"></script>
@endpush
