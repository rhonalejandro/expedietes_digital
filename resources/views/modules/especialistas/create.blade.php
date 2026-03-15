@extends('layouts.admin.master')
@section('title', 'Nuevo Especialista')

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/modules/especialistas/css/especialistas.module.css') }}?v={{ time() }}">
@endpush

@section('content')
<div class="container-fluid">

    <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
        <div>
            <h4 class="fw-semibold text-dark mb-1">Nuevo Especialista</h4>
            <p class="text-muted mb-0" style="font-size:.85rem;">Registra un nuevo miembro del equipo clínico</p>
        </div>
        <a href="{{ route('especialistas.index') }}" class="btn btn-sm btn-light">
            <i class="ti ti-arrow-left me-1"></i>Volver
        </a>
    </div>

    <form method="POST" action="{{ route('especialistas.store') }}" enctype="multipart/form-data">
        @csrf

        @include('modules.especialistas._partials.form-fields')

        <div class="d-flex justify-content-end gap-2">
            <a href="{{ route('especialistas.index') }}" class="btn btn-light">Cancelar</a>
            <button type="submit" class="btn btn-primary">
                <i class="ti ti-check me-1"></i>Registrar Especialista
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
