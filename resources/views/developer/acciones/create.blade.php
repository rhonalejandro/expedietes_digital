@extends('developer.layouts.master')

@section('title', 'Nueva Acción — ' . $modulo->nombre)

@section('content')

    <div class="d-flex align-items-center gap-2 mb-4">
        <a href="{{ route('developer.modulos.index') }}" style="color: #8b949e;" class="text-decoration-none">Módulos</a>
        <span style="color: #30363d;">/</span>
        <a href="{{ route('developer.modulos.show', $modulo->id) }}" style="color: #8b949e;" class="text-decoration-none">{{ $modulo->nombre }}</a>
        <span style="color: #30363d;">/</span>
        <span style="color: #e6edf3;">Nueva Acción</span>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-7">
            <div class="dev-card p-4">
                <h5 class="fw-bold mb-1" style="color: #e6edf3;">
                    <i class="ti ti-key me-2" style="color: #667eea;"></i>Nueva Acción
                </h5>
                <p class="mb-4" style="color: #8b949e; font-size: 13px;">
                    Módulo: <code class="dev-code">{{ $modulo->slug }}</code>
                </p>

                <form method="POST" action="{{ route('developer.acciones.store', $modulo->id) }}">
                    @csrf

                    <div class="row g-3">
                        <div class="col-sm-7">
                            <label class="form-label">Nombre de la Acción <span class="text-danger">*</span></label>
                            <input type="text" name="nombre" class="form-control dev-input @error('nombre') is-invalid @enderror"
                                   value="{{ old('nombre') }}" placeholder="Ej. Ver Pacientes" autofocus>
                            @error('nombre') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-sm-5">
                            <label class="form-label">
                                Código <span class="text-danger">*</span>
                                <small style="color: #8b949e;">(access_code)</small>
                            </label>
                            <input type="text" name="codigo" id="codigo" class="form-control dev-input @error('codigo') is-invalid @enderror"
                                   value="{{ old('codigo') }}" placeholder="Ej. ver">
                            <small style="color: #8b949e;">Solo letras, números y _</small>
                            @error('codigo') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-sm-6">
                            <label class="form-label">Tipo <span class="text-danger">*</span></label>
                            <select name="tipo" class="form-select dev-select @error('tipo') is-invalid @enderror">
                                <option value="general"  {{ old('tipo', 'general') === 'general'  ? 'selected' : '' }}>
                                    General (CRUD básico: ver, crear, editar, eliminar)
                                </option>
                                <option value="granular" {{ old('tipo') === 'granular' ? 'selected' : '' }}>
                                    Granular (acción especial)
                                </option>
                            </select>
                            @error('tipo') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label">Descripción</label>
                            <textarea name="descripcion" class="form-control dev-input" rows="2"
                                      placeholder="¿Qué permite hacer esta acción?">{{ old('descripcion') }}</textarea>
                        </div>

                        {{-- Preview del helper --}}
                        <div class="col-12">
                            <div class="dev-card p-3" style="border-color: rgba(102,126,234,.2);">
                                <p class="dev-label mb-1">Llamada en código</p>
                                <code class="dev-code" id="helper-preview">
                                    canDo('{{ $modulo->slug }}', '<span id="codigo-preview">codigo</span>')
                                </code>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn dev-btn-primary">
                            <i class="ti ti-device-floppy me-1"></i>Registrar Acción
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
// Auto-generar código desde el nombre
document.querySelector('input[name="nombre"]').addEventListener('input', function() {
    const codigo = this.value.toLowerCase().normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '').replace(/[^a-z0-9\s_]/g, '')
        .replace(/\s+/g, '_').replace(/_+/g, '_').replace(/^_|_$/g, '');
    document.getElementById('codigo').value = codigo;
    document.getElementById('codigo-preview').textContent = codigo || 'codigo';
});

document.getElementById('codigo').addEventListener('input', function() {
    document.getElementById('codigo-preview').textContent = this.value || 'codigo';
});
</script>
@endpush
