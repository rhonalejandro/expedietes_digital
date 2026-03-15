{{-- 
Desarrollo - Permisos de Usuarios - UI - 2026-02-18
Vista: users/index.blade.php
--}}

@extends('layouts.admin.master')

@section('title', 'Asignar Permisos')

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
                            <i class="ti ti-users me-2"></i>
                            Asignar Permisos a Usuarios
                        </h5>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Filtro de búsqueda -->
                    <form method="GET" action="{{ route('settings.permissions.users.index') }}" class="mb-4">
                        <div class="row g-3">
                            <div class="col-md-8">
                                <input type="text" name="search" class="form-control" 
                                       placeholder="Buscar por nombre o email..." 
                                       value="{{ $search }}">
                            </div>
                            <div class="col-md-4">
                                <button type="submit" class="btn btn-primary me-2">
                                    <i class="ti ti-search me-1"></i> Buscar
                                </button>
                                <a href="{{ route('settings.permissions.users.index') }}" class="btn btn-outline-secondary">
                                    <i class="ti ti-x"></i>
                                </a>
                            </div>
                        </div>
                    </form>

                    <!-- Tabla de usuarios -->
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Usuario</th>
                                    <th>Email</th>
                                    <th>Roles</th>
                                    <th>Permisos</th>
                                    <th>Estado</th>
                                    <th class="text-end">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($usuarios as $usuario)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="bg-primary bg-opacity-10 h-40 w-40 d-flex-center b-r-50 me-3">
                                                    <i class="ti ti-user text-primary"></i>
                                                </div>
                                                <div>
                                                    <div class="fw-semibold">{{ $usuario->persona->nombre ?? $usuario->nombre }}</div>
                                                    <small class="text-muted">{{ $usuario->persona->apellido ?? '' }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>{{ $usuario->email }}</td>
                                        <td>
                                            @if($usuario->roles->isEmpty())
                                                <span class="text-muted small">Sin roles</span>
                                            @else
                                                @foreach($usuario->roles->take(2) as $rol)
                                                    <span class="badge bg-primary bg-opacity-10 text-primary">
                                                        {{ $rol->nombre }}
                                                    </span>
                                                @endforeach
                                                @if($usuario->roles->count() > 2)
                                                    <span class="badge bg-secondary">+{{ $usuario->roles->count() - 2 }}</span>
                                                @endif
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-info">{{ $usuario->permisos->count() }} permisos</span>
                                        </td>
                                        <td>
                                            <span class="badge {{ $usuario->estado ? 'bg-success' : 'bg-secondary' }}">
                                                {{ $usuario->estado ? 'Activo' : 'Inactivo' }}
                                            </span>
                                        </td>
                                        <td class="text-end">
                                            <div class="btn-group btn-group-sm" role="group">
                                                <a href="{{ route('settings.permissions.users.show', $usuario->id) }}" 
                                                   class="btn btn-outline-info" 
                                                   title="Ver permisos">
                                                    <i class="ti ti-eye"></i>
                                                </a>
                                                <a href="{{ route('settings.permissions.users.edit', $usuario->id) }}" 
                                                   class="btn btn-outline-primary" 
                                                   title="Asignar permisos">
                                                    <i class="ti ti-shield"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-5">
                                            <div class="text-muted">
                                                <i class="ti ti-users f-s-40 mb-3 d-block"></i>
                                                No se encontraron usuarios
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Paginación -->
                    @if($usuarios->hasPages())
                        <div class="mt-4">
                            {{ $usuarios->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
