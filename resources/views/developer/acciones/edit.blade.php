@extends('developer.layouts.master')

@section('title', 'Editar Acción — ' . $permiso->nombre)

@section('content')

    <div class="d-flex align-items-center gap-2 mb-4">
        <a href="{{ route('developer.modulos.index') }}" style="color: #8b949e;" class="text-decoration-none">Módulos</a>
        <span style="color: #30363d;">/</span>
        <a href="{{ route('developer.modulos.show', $modulo->id) }}" style="color: #8b949e;" class="text-decoration-none">{{ $modulo->nombre }}</a>
        <span style="color: #30363d;">/</span>
        <span style="color: #e6edf3;">Editar Acción</span>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-7">
            <div class="dev-card p-4">
                <h5 class="fw-bold mb-1" style="color: #e6edf3;">
                    <i class="ti ti-pencil me-2" style="color: #667eea;"></i>Editar Acción
                </h5>
                <p class="mb-4" style="color: #8b949e; font-size: 13px;">
                    Módulo: <code class="dev-code">{{ $modulo->slug }}</code> ·
                    Código: <code class="dev-code">{{ $permiso->codigo }}</code>
                    <small style="color: #e3b341;">(el código no se puede cambiar)</small>
                </p>

                <form method="POST" action="{{ route('developer.acciones.update', [$modulo->id, $permiso->id]) }}">
                    @csrf @method('PUT')

                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Nombre de la Acción <span class="text-danger">*</span></label>
                            <input type="text" name="nombre" class="form-control dev-input @error('nombre') is-invalid @enderror"
                                   value="{{ old('nombre', $permiso->nombre) }}" autofocus>
                            @error('nombre') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-sm-6">
                            <label class="form-label">Tipo <span class="text-danger">*</span></label>
                            <select name="tipo" class="form-select dev-select">
                                <option value="general"  {{ old('tipo', $permiso->tipo) === 'general'  ? 'selected' : '' }}>General</option>
                                <option value="granular" {{ old('tipo', $permiso->tipo) === 'granular' ? 'selected' : '' }}>Granular</option>
                            </select>
                        </div>

                        <div class="col-sm-6 d-flex align-items-center">
                            <div class="form-check form-switch mt-3">
                                <input class="form-check-input" type="checkbox" name="estado" value="1" id="estado"
                                       {{ old('estado', $permiso->estado) ? 'checked' : '' }}>
                                <label class="form-check-label" for="estado" style="color: #c9d1d9;">Acción activa</label>
                            </div>
                        </div>

                        <div class="col-12">
                            <label class="form-label">Descripción</label>
                            <textarea name="descripcion" class="form-control dev-input" rows="2">{{ old('descripcion', $permiso->descripcion) }}</textarea>
                        </div>
                    </div>

                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn dev-btn-primary">
                            <i class="ti ti-device-floppy me-1"></i>Guardar Cambios
                        </button>
                        <a href="{{ route('developer.modulos.show', $modulo->id) }}" class="btn btn-outline-secondary"
                           style="border-color: #30363d; color: #c9d1d9;">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection
