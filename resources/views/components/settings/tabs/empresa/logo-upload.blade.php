@props([
    'empresa' => null,
    'name' => 'logo',
])

@php
    $logoField = $name === 'logo_rectangular' ? 'logo_rectangular' : 'logo';
    $hasLogo = $empresa && $empresa->$logoField && \Illuminate\Support\Facades\Storage::disk('public')->exists($empresa->$logoField);
@endphp

<div class="text-center">
    <div class="mb-3">
        @if($hasLogo)
            <img
                src="{{ asset('storage/' . $empresa->$logoField) }}"
                alt="Logo {{ $empresa->nombre }}"
                class="company-logo-preview"
                id="{{ $name }}Preview"
                style="object-fit: contain;"
            >
        @else
            <div
                class="company-logo-preview d-flex-center bg-light"
                id="{{ $name }}Preview"
            >
                <i class="ti ti-camera f-s-40 text-muted"></i>
            </div>
        @endif
    </div>

    <label class="logo-upload-btn" for="{{ $name }}Input">
        <i class="ti ti-camera me-2"></i>Cambiar Logo
    </label>
    <input
        type="file"
        name="{{ $name }}"
        id="{{ $name }}Input"
        accept="image/*"
        style="display: none;"
        onchange="previewLogo(event, '{{ $name }}Preview')"
    >

    <p class="text-muted small mt-2">PNG, JPG hasta 2MB</p>
</div>

@push('scripts')
<script>
function previewLogo(event, previewId) {
    const file = event.target.files[0];
    
    // Validar archivo
    if (!file) return;
    
    // Validar tipo
    const validTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];
    if (!validTypes.includes(file.type)) {
        alert('Solo se permiten imágenes JPG, PNG o GIF');
        event.target.value = '';
        return;
    }
    
    // Validar tamaño (2MB max)
    if (file.size > 2 * 1024 * 1024) {
        alert('La imagen no debe superar los 2MB');
        event.target.value = '';
        return;
    }
    
    // Mostrar preview
    const reader = new FileReader();
    reader.onload = function(e) {
        const imgElement = document.getElementById(previewId);
        imgElement.src = e.target.result;
        imgElement.classList.remove('bg-light');
        imgElement.style.objectFit = 'contain';
    };
    reader.readAsDataURL(file);
}
</script>
@endpush
