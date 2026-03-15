@extends('layouts.admin.master')
@section('title', 'Especialistas')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/modules/especialistas/css/especialistas.module.css') }}?v={{ time() }}">
@endpush

@section('content')
<div class="container-fluid">

    @include('modules.especialistas._partials.alerts')
    @include('modules.especialistas._partials.stats')
    @include('modules.especialistas._partials.toolbar')
    @include('modules.especialistas._partials.table')
    @include('modules.especialistas._partials.modal-delete')

</div>
@endsection

@push('scripts')
    <script src="{{ asset('assets/modules/especialistas/js/especialistas.module.js') }}?v={{ time() }}"></script>
@endpush
