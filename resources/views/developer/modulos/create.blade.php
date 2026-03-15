@extends('developer.layouts.master')

@section('title', 'Nuevo Módulo')

@section('content')

    <div class="d-flex align-items-center gap-2 mb-4">
        <a href="{{ route('developer.modulos.index') }}" style="color: #8b949e;" class="text-decoration-none">
            <i class="ti ti-arrow-left"></i> Módulos
        </a>
        <span style="color: #30363d;">/</span>
        <span style="color: #e6edf3;">Nuevo Módulo</span>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-7">
            <div class="dev-card p-4">
                <h5 class="fw-bold mb-4" style="color: #e6edf3;">
                    <i class="ti ti-plus me-2" style="color: #667eea;"></i>Registrar Nuevo Módulo
                </h5>

                <form method="POST" action="{{ route('developer.modulos.store') }}">
                    @csrf

                    <div class="row g-3">
                        <div class="col-sm-6">
                            <label class="form-label">Nombre <span class="text-danger">*</span></label>
                            <input type="text" name="nombre" class="form-control dev-input @error('nombre') is-invalid @enderror"
                                   value="{{ old('nombre') }}" placeholder="Ej. Pacientes" autofocus>
                            @error('nombre') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-sm-6">
                            <label class="form-label">
                                Slug <span class="text-danger">*</span>
                                <small style="color: #8b949e;">(identificador único)</small>
                            </label>
                            <input type="text" name="slug" id="slug" class="form-control dev-input @error('slug') is-invalid @enderror"
                                   value="{{ old('slug') }}" placeholder="Ej. pacientes">
                            <small style="color: #8b949e;">Solo letras, números y guiones bajos</small>
                            @error('slug') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-sm-8">
                            <label class="form-label">URL Base</label>
                            <input type="text" name="url" class="form-control dev-input @error('url') is-invalid @enderror"
                                   value="{{ old('url') }}" placeholder="Ej. /pacientes">
                            @error('url') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-sm-4">
                            <label class="form-label">Orden</label>
                            <input type="number" name="orden" class="form-control dev-input"
                                   value="{{ old('orden', 0) }}" min="0" max="999">
                        </div>

                        <div class="col-sm-8">
                            <label class="form-label">Icono <small style="color: #8b949e;">(clase Tabler)</small></label>
                            <input type="text" name="icono" id="icono" class="form-control dev-input"
                                   value="{{ old('icono', 'ti ti-box') }}" placeholder="Ej. ti ti-users">
                        </div>

                        <div class="col-sm-4 d-flex align-items-end">
                            <div class="p-3 dev-card text-center w-100">
                                <i id="icono-preview" class="{{ old('icono', 'ti ti-box') }}"
                                   style="font-size: 1.5rem; color: #667eea;"></i>
                            </div>
                        </div>

                        <div class="col-12">
                            <label class="form-label">Descripción</label>
                            <textarea name="descripcion" class="form-control dev-input" rows="2"
                                      placeholder="Descripción del módulo">{{ old('descripcion') }}</textarea>
                        </div>
                    </div>

                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn dev-btn-primary">
                            <i class="ti ti-device-floppy me-1"></i>Crear Módulo
                        </button>
                        <a href="{{ route('developer.modulos.index') }}" class="btn btn-outline-secondary"
                           style="border-color: #30363d; color: #c9d1d9;">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
<script>
// Auto-generar slug desde el nombre
document.querySelector('input[name="nombre"]').addEventListener('input', function() {
    const slug = this.value.toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, '')
        .replace(/[^a-z0-9\s_]/g, '').replace(/\s+/g, '_').replace(/_+/g, '_');
    document.getElementById('slug').value = slug;
});

// Preview del icono
document.getElementById('icono').addEventListener('input', function() {
    document.getElementById('icono-preview').className = this.value || 'ti ti-box';
});
</script>
@endpush
