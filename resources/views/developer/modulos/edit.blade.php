@extends('developer.layouts.master')

@section('title', 'Editar — ' . $modulo->nombre)

@section('content')

    <div class="d-flex align-items-center gap-2 mb-4">
        <a href="{{ route('developer.modulos.index') }}" style="color: #8b949e;" class="text-decoration-none">Módulos</a>
        <span style="color: #30363d;">/</span>
        <a href="{{ route('developer.modulos.show', $modulo->id) }}" style="color: #8b949e;" class="text-decoration-none">{{ $modulo->nombre }}</a>
        <span style="color: #30363d;">/</span>
        <span style="color: #e6edf3;">Editar</span>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-7">
            <div class="dev-card p-4">
                <h5 class="fw-bold mb-4" style="color: #e6edf3;">
                    <i class="ti ti-pencil me-2" style="color: #667eea;"></i>Editar Módulo
                </h5>

                <form method="POST" action="{{ route('developer.modulos.update', $modulo->id) }}">
                    @csrf @method('PUT')

                    <div class="row g-3">
                        <div class="col-sm-6">
                            <label class="form-label">Nombre <span class="text-danger">*</span></label>
                            <input type="text" name="nombre" class="form-control dev-input @error('nombre') is-invalid @enderror"
                                   value="{{ old('nombre', $modulo->nombre) }}">
                            @error('nombre') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-sm-6">
                            <label class="form-label">Slug <span class="text-danger">*</span></label>
                            <input type="text" name="slug" id="slug" class="form-control dev-input @error('slug') is-invalid @enderror"
                                   value="{{ old('slug', $modulo->slug) }}">
                            <small style="color: #e3b341;"><i class="ti ti-alert-triangle"></i> Cambiar el slug actualiza todos los permisos existentes.</small>
                            @error('slug') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-sm-8">
                            <label class="form-label">URL Base</label>
                            <input type="text" name="url" class="form-control dev-input"
                                   value="{{ old('url', $modulo->url) }}" placeholder="/modulo">
                        </div>

                        <div class="col-sm-4">
                            <label class="form-label">Orden</label>
                            <input type="number" name="orden" class="form-control dev-input"
                                   value="{{ old('orden', $modulo->orden) }}" min="0" max="999">
                        </div>

                        <div class="col-sm-8">
                            <label class="form-label">Icono</label>
                            <input type="text" name="icono" id="icono" class="form-control dev-input"
                                   value="{{ old('icono', $modulo->icono) }}">
                        </div>

                        <div class="col-sm-4 d-flex align-items-end">
                            <div class="p-3 dev-card text-center w-100">
                                <i id="icono-preview" class="{{ old('icono', $modulo->icono) }}"
                                   style="font-size: 1.5rem; color: #667eea;"></i>
                            </div>
                        </div>

                        <div class="col-sm-8">
                            <label class="form-label">Descripción</label>
                            <textarea name="descripcion" class="form-control dev-input" rows="2">{{ old('descripcion', $modulo->descripcion) }}</textarea>
                        </div>

                        <div class="col-sm-4 d-flex align-items-center">
                            <div class="form-check form-switch mt-3">
                                <input class="form-check-input" type="checkbox" name="activo" value="1" id="activo"
                                       {{ old('activo', $modulo->activo) ? 'checked' : '' }}>
                                <label class="form-check-label" for="activo" style="color: #c9d1d9;">Activo</label>
                            </div>
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

@push('scripts')
<script>
document.getElementById('icono').addEventListener('input', function() {
    document.getElementById('icono-preview').className = this.value || 'ti ti-box';
});
</script>
@endpush
