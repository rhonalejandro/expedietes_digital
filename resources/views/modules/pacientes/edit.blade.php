@extends('layouts.admin.master')

@section('title', 'Editar — ' . $paciente->nombre_completo)

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/modules/pacientes/css/pacientes.module.css') }}?v={{ time() }}">
@endpush

@section('content')
<div class="container-fluid">

    <div class="page-header mb-4">
        <h4 class="mb-1 fw-semibold text-dark">Editar Paciente</h4>
    </div>

    <form method="POST" action="{{ route('pacientes.update', $paciente->id) }}">
        @csrf
        @method('PUT')
        <div class="row g-4">
            <div class="col-lg-8">
                @include('modules.pacientes._partials.form.section-personal')
                @include('modules.pacientes._partials.form.section-contacto')
                @include('modules.pacientes._partials.form.section-clinico')
            </div>
            <div class="col-lg-4">
                @include('modules.pacientes._partials.form.actions', [
                    'cancelRoute' => route('pacientes.show', $paciente->id),
                    'submitLabel' => 'Guardar Cambios',
                ])
            </div>
        </div>
    </form>

</div>
@endsection
