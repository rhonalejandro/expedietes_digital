{{-- 
Desarrollo - Permisos de Usuarios - UI - 2026-02-18
Vista: templates/index.blade.php
--}}

@extends('layouts.admin.master')

@section('title', 'Plantillas de Permisos')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/modules/settings/permissions/css/permissions.module.css') }}?v={{ time() }}">
@endpush

@section('content')
<div class="container-fluid">
    @include('modules.settings.permissions._partials.alerts')

    <div class="row">
        @include('modules.settings.permissions._partials.sidebar')

        <div class="col-lg-9 col-xl-10">
            <div class="card settings-card">
                <div class="card-header bg-transparent border-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="ti ti-copy me-2"></i>
                            Plantillas de Permisos
                        </h5>
                        <a href="{{ route('settings.permissions.templates.create') }}" class="btn btn-primary">
                            <i class="ti ti-plus me-1"></i> Nueva Plantilla
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if($plantillas->isEmpty())
                        <div class="text-center py-5">
                            <div class="bg-primary bg-opacity-10 h-100 w-100 d-flex-center b-r-50 m-auto mb-3">
                                <i class="ti ti-copy f-s-40 text-primary"></i>
                            </div>
                            <h5 class="text-muted">No hay plantillas registradas</h5>
                            <p class="text-muted mb-3">Comienza creando la primera plantilla</p>
                            <a href="{{ route('settings.permissions.templates.create') }}" class="btn btn-primary">
                                <i class="ti ti-plus me-1"></i> Crear Plantilla
                            </a>
                        </div>
                    @else
                        <div class="row g-4">
                            @foreach($plantillas as $plantilla)
                                @include('modules.settings.permissions.templates._components.template-card', [
                                    'plantilla' => $plantilla
                                ])
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
