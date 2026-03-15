@extends('layouts.admin.master')

@section('title', 'Configuración')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/modules/settings/css/settings.module.css') }}?v={{ time() }}">
@endpush

@section('content')
<div class="container-fluid">

    <!-- Alertas -->
    @if(session('success'))
        <x-settings.alert-message type="success" :message="session('success')" />
    @endif

    @if(session('error'))
        <x-settings.alert-message type="error" :message="session('error')" />
    @endif

    <!-- Main Content -->
    <div class="row">
        <!-- Sidebar -->
        <div class="col-lg-3 col-xl-2 mb-4">
            <x-settings.navigation
                :tabs="[
                    ['id' => 'empresa', 'label' => 'Empresa', 'icon' => 'ti ti-building'],
                    ['id' => 'sucursales', 'label' => 'Sucursales', 'icon' => 'ti ti-map-pin', 'badge' => $stats['total_sucursales']],
                    ['id' => 'permisos', 'label' => 'Permisos', 'icon' => 'ti ti-shield-lock'],
                ]"
                active-tab="empresa"
            />
        </div>

        <!-- Content -->
        <div class="col-lg-9 col-xl-10">
            <div class="tab-content" id="settings-tab-content">
                @include('modules.settings.empresa')
                @include('modules.settings.sucursales')
                @include('modules.settings.permissions')
            </div>
        </div>
    </div>
</div>
@endsection

{{-- Scripts del módulo se manejan inline en cada vista --}}
