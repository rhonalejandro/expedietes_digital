@extends('layouts.admin.master')
@section('title', 'Editar — ' . $especialista->nombre_completo)

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/modules/especialistas/css/especialistas.module.css') }}?v={{ time() }}">
@endpush

@section('content')
<div class="container-fluid">

    <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
        <div>
            <h4 class="fw-semibold text-dark mb-1">Editar Especialista</h4>
            <p class="text-muted mb-0" style="font-size:.85rem;">{{ $especialista->nombre_completo }}</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('especialistas.show', $especialista->id) }}" class="btn btn-sm btn-light">
                <i class="ti ti-arrow-left me-1"></i>Volver
            </a>
        </div>
    </div>

    <form method="POST" action="{{ route('especialistas.update', $especialista->id) }}" enctype="multipart/form-data">
        @csrf @method('PUT')

        @include('modules.especialistas._partials.form-fields')

        {{-- Estado --}}
        <div class="esp-card mb-4">
            <div class="card-body p-4">
                <h6 class="fw-semibold text-dark mb-3 pb-2 border-bottom">
                    <i class="ti ti-toggle-left me-2 text-primary"></i>Estado
                </h6>
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="estado" id="estado"
                        value="1" @checked(old('estado', $especialista->estado))>
                    <label class="form-check-label" for="estado">Especialista activo</label>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2">
            <a href="{{ route('especialistas.show', $especialista->id) }}" class="btn btn-light">Cancelar</a>
            <button type="submit" class="btn btn-primary">
                <i class="ti ti-check me-1"></i>Guardar cambios
            </button>
        </div>
    </form>

</div>
@endsection

@push('scripts')
<script>
function previewFirma(input) {
    const container = document.getElementById('firma-preview-container');
    const preview   = document.getElementById('firma-preview');
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => { preview.src = e.target.result; container.style.display = 'block'; };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endpush
