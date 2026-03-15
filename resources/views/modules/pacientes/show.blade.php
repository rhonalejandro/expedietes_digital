@extends('layouts.admin.master')

@section('title', $paciente->nombre_completo . ' — Paciente')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/modules/pacientes/css/pacientes.module.css') }}?v={{ time() }}">
@endpush

@section('content')
<div class="container-fluid">

    @include('modules.pacientes._partials.alerts')

    <div class="row g-4 align-items-start">

        {{-- Columna izquierda: perfil del paciente --}}
        <div class="col-lg-4">
            @include('modules.pacientes._partials.show.perfil-lateral')
        </div>

        {{-- Columna derecha: panel con tabs --}}
        <div class="col-lg-8">
            @include('modules.pacientes._partials.show.tabs-panel')
        </div>

    </div>

</div>
@endsection
