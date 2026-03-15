@extends('layouts.admin.master')
@section('title', $especialista->nombre_completo . ' — Especialista')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/modules/especialistas/css/especialistas.module.css') }}?v={{ time() }}">
@endpush

@section('content')
<div class="container-fluid">

    @include('modules.especialistas._partials.alerts')

    <div class="row g-4 align-items-start">

        {{-- Columna izquierda: perfil --}}
        <div class="col-lg-4">
            <div class="card border-0 esp-detail-card">
                <div class="card-body p-0">

                    {{-- Avatar + nombre + estado --}}
                    <div class="p-4 text-center border-bottom">
                        <div class="esp-avatar-lg mx-auto mb-3">
                            {{ strtoupper(substr($especialista->persona->nombre ?? '?', 0, 1)) }}{{ strtoupper(substr($especialista->persona->apellido ?? '', 0, 1)) }}
                        </div>
                        <h5 class="fw-semibold text-dark mb-1">{{ $especialista->nombre_completo }}</h5>
                        <p class="text-muted mb-1" style="font-size:.85rem;">{{ $especialista->profesion ?? '—' }}</p>
                        @if($especialista->especialidad)
                            <p class="text-muted mb-2" style="font-size:.8rem;">{{ $especialista->especialidad }}</p>
                        @endif
                        <div class="d-flex justify-content-center gap-2 mb-3">
                            <span class="badge {{ $especialista->estado ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary' }}">
                                {{ $especialista->estado ? 'Activo' : 'Inactivo' }}
                            </span>
                            @if($especialista->tratamiento && $especialista->tratamiento !== 'N/A')
                                <span class="badge bg-primary-subtle text-primary">{{ $especialista->tratamiento }}</span>
                            @endif
                        </div>
                        <div class="d-flex justify-content-center gap-2">
                            <a href="{{ route('especialistas.edit', $especialista->id) }}" class="btn btn-sm btn-outline-primary">
                                <i class="ti ti-pencil me-1"></i>Editar
                            </a>
                            <a href="{{ route('especialistas.index') }}" class="btn btn-sm btn-light">
                                <i class="ti ti-arrow-left me-1"></i>Volver
                            </a>
                        </div>
                    </div>

                    {{-- Stats --}}
                    <div class="d-flex border-bottom text-center">
                        <div class="flex-fill py-3 border-end">
                            <h6 class="mb-0 fw-semibold">{{ $especialista->citas->count() }}</h6>
                            <small class="text-muted">Citas</small>
                        </div>
                        <div class="flex-fill py-3 border-end">
                            <h6 class="mb-0 fw-semibold">{{ $especialista->casos->count() }}</h6>
                            <small class="text-muted">Casos</small>
                        </div>
                        <div class="flex-fill py-3">
                            <h6 class="mb-0 fw-semibold">{{ $especialista->created_at->format('d/m/Y') }}</h6>
                            <small class="text-muted">Registro</small>
                        </div>
                    </div>

                    {{-- Datos en lista --}}
                    <div class="p-4">

                        <p class="esp-label fw-semibold text-uppercase mb-3" style="font-size:.7rem;letter-spacing:.06em;">
                            Datos Profesionales
                        </p>

                        @php
                            $campos = [
                                ['ti-certificate',       'N.º Colegiado',   $especialista->num_colegiado ?? 'N/A'],
                                ['ti-phone',             'Teléfono',         $especialista->telefono ?? '—'],
                                ['ti-mail',              'Correo',           $especialista->email ?? '—'],
                            ];
                        @endphp

                        @foreach($campos as [$icon, $label, $value])
                        <div class="esp-perfil-campo">
                            <i class="ti {{ $icon }} esp-perfil-campo__icon"></i>
                            <div class="esp-perfil-campo__text">
                                <span class="esp-label">{{ $label }}</span>
                                <span class="esp-value">{{ $value }}</span>
                            </div>
                        </div>
                        @endforeach

                        {{-- Sucursales --}}
                        <p class="esp-label fw-semibold text-uppercase mt-4 mb-2" style="font-size:.7rem;letter-spacing:.06em;">Sucursales</p>
                        @forelse($especialista->sucursales as $suc)
                            <span class="badge bg-light text-dark border me-1 mb-1">{{ $suc->nombre }}</span>
                        @empty
                            <p class="esp-value" style="font-size:.8rem;">Sin sucursal asignada</p>
                        @endforelse

                        {{-- Firma --}}
                        @if($especialista->firma)
                        <p class="esp-label fw-semibold text-uppercase mt-4 mb-2" style="font-size:.7rem;letter-spacing:.06em;">Firma</p>
                        <img src="{{ Storage::url($especialista->firma) }}" alt="Firma" class="esp-firma-preview">
                        @endif

                    </div>
                </div>
            </div>
        </div>

        {{-- Columna derecha: tabs --}}
        <div class="col-lg-8">
            <div class="card border-0 esp-detail-card">

                <div class="esp-tabs-nav">
                    <ul class="nav esp-nav-tabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-info" type="button">
                                <i class="ti ti-user me-1"></i>Información
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-citas" type="button">
                                <i class="ti ti-calendar me-1"></i>Citas
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-actividades" type="button">
                                <i class="ti ti-history me-1"></i>Actividades
                            </button>
                        </li>
                    </ul>
                </div>

                <div class="tab-content esp-tab-content">

                    {{-- Tab: Información --}}
                    <div class="tab-pane fade show active" id="tab-info" role="tabpanel">
                        <div class="card border-0 esp-detail-card mb-4">
                            <div class="card-body">
                                <h6 class="esp-section-title mb-3">Datos de Identificación</h6>
                                <div class="row g-3">
                                    @php
                                        $info = [
                                            ['Tratamiento',  $especialista->tratamiento ?? '—'],
                                            ['Nombre',       $especialista->persona->nombre ?? '—'],
                                            ['Apellido',     $especialista->persona->apellido ?? '—'],
                                            ['Profesión',    $especialista->profesion ?? '—'],
                                            ['Especialidad', $especialista->especialidad ?? '—'],
                                            ['N.º Colegiado', $especialista->num_colegiado ?? 'N/A'],
                                            ['Teléfono',     $especialista->telefono ?? '—'],
                                            ['Correo',       $especialista->email ?? '—'],
                                            ['Estado',       $especialista->estado ? 'Activo' : 'Inactivo'],
                                            ['Registrado',   $especialista->created_at->format('d/m/Y')],
                                        ];
                                    @endphp
                                    @foreach($info as [$label, $value])
                                    <div class="col-md-6">
                                        <p class="esp-label mb-1">{{ $label }}</p>
                                        <p class="esp-value mb-0">{{ $value }}</p>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Tab: Citas --}}
                    <div class="tab-pane fade" id="tab-citas" role="tabpanel">
                        <div class="text-center py-5 esp-empty-state">
                            <i class="ti ti-calendar-off d-block mb-2" style="font-size:2rem;color:#cbd5e0;"></i>
                            <p class="mb-0">Módulo de citas próximamente</p>
                        </div>
                    </div>

                    {{-- Tab: Actividades --}}
                    <div class="tab-pane fade" id="tab-actividades" role="tabpanel">
                        <div class="p-3">
                            <x-log-actividad modulo="especialistas" :registro-id="$especialista->id" />
                        </div>
                    </div>

                </div>
            </div>
        </div>

    </div>
</div>
@endsection
