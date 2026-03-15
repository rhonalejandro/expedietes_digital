{{-- 
Desarrollo - Permisos de Usuarios - UI - 2026-02-18
Vista: index.blade.php (Main - < 50 líneas)
Propósito: Vista principal de gestión de permisos
--}}

@extends('layouts.admin.master')

@section('title', 'Permisos')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/modules/settings/permissions/css/permissions.module.css') }}?v={{ time() }}">
@endpush

@section('content')
<div class="container-fluid">
    
    @include('modules.settings.permissions._partials.alerts')
    
    <div class="row">
        @include('modules.settings.permissions._partials.sidebar')
        
        <div class="col-lg-9 col-xl-10">
            @include('modules.settings.permissions._partials.stats')
            @include('modules.settings.permissions._partials.filters')
            @include('modules.settings.permissions._partials.permissions-table')
        </div>
    </div>
</div>

@include('modules.settings.permissions._partials.permission-modal')
@endsection

@push('scripts')
    <script src="{{ asset('assets/modules/settings/permissions/js/permissions.module.js') }}?v={{ time() }}"></script>
@endpush
