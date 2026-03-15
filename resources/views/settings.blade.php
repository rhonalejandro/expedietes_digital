@extends('layouts.admin.master')

@section('title', 'Configuración de Empresa')

@push('styles')
    <!-- Custom Settings Styles -->
    <style>
        /* Cards */
        .settings-card {
            border: none !important;
            border-radius: 12px !important;
            box-shadow: 0 2px 12px rgba(0,0,0,0.05) !important;
            background: white !important;
            position: relative !important;
            overflow: visible !important;
        }
        
        .settings-card .card-header {
            background: white !important;
            border-bottom: 1px solid #f0f0f0 !important;
            padding: 20px !important;
        }
        
        .settings-card .card-body {
            padding: 24px !important;
        }
        
        /* Navigation Sidebar */
        .settings-nav {
            border-radius: 10px !important;
            background: #f8f9fa !important;
            padding: 6px !important;
            margin: 0 !important;
            list-style: none !important;
        }
        
        .settings-nav .nav-item {
            margin-bottom: 4px;
        }
        
        .settings-nav .nav-link {
            border-radius: 8px !important;
            padding: 10px 12px !important;
            transition: all 0.2s !important;
            border: none !important;
            color: #555 !important;
            font-weight: 500 !important;
            font-size: 0.85rem !important;
            text-align: left !important;
            background: transparent !important;
            display: flex !important;
            align-items: center !important;
        }
        
        .settings-nav .nav-link:hover {
            background: #e9ecef !important;
        }
        
        .settings-nav .nav-link.active {
            background-color: #667eea !important;
            color: white !important;
        }
        
        .settings-nav .nav-link i {
            margin-right: 8px !important;
        }
        
        .settings-nav .badge {
            font-size: 0.7rem !important;
            padding: 2px 8px !important;
        }
        
        /* Logo */
        .company-logo-preview {
            width: 120px;
            height: 120px;
            border-radius: 12px;
            border: 2px solid #e0e0e0;
            background: #fafafa;
            padding: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto;
        }

        .company-logo-preview img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }

        .company-logo-preview i {
            font-size: 40px;
        }
        
        .logo-upload-btn {
            background: #667eea;
            color: white;
            padding: 8px 14px;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s;
            display: inline-block;
            margin-top: 10px;
            font-size: 0.9rem;
        }
        
        .logo-upload-btn:hover {
            background: #5568d3;
            color: white;
        }
        
        /* Section Titles */
        .setting-section-title {
            font-size: 1rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 16px;
            padding-bottom: 8px;
            border-bottom: 1px solid #f0f0f0;
        }
        
        /* Toggle Switch */
        .toggle-switch {
            position: relative;
            width: 46px;
            height: 24px;
        }
        
        .toggle-switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }
        
        .toggle-slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: .2s;
            border-radius: 24px;
        }
        
        .toggle-slider:before {
            position: absolute;
            content: "";
            height: 18px;
            width: 18px;
            left: 3px;
            bottom: 3px;
            background-color: white;
            transition: .2s;
            border-radius: 50%;
        }
        
        input:checked + .toggle-slider {
            background-color: #667eea;
        }
        
        input:checked + .toggle-slider:before {
            transform: translateX(22px);
        }
        
        /* Forms */
        .form-control, .form-select {
            border-radius: 8px;
            border: 1px solid #e0e0e0;
            padding: 8px 12px;
            font-size: 0.9rem;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.15);
        }
        
        .form-label {
            font-weight: 500;
            font-size: 0.85rem;
            color: #555;
            margin-bottom: 4px;
        }
        
        /* Buttons */
        .btn-primary {
            background-color: #667eea;
            border: none;
            border-radius: 8px;
            padding: 8px 20px;
            font-weight: 500;
            font-size: 0.9rem;
        }
        
        .btn-primary:hover {
            background-color: #5568d3;
        }
        
        .btn-outline-primary {
            border: 1px solid #667eea;
            color: #667eea;
        }
        
        .btn-outline-primary:hover {
            background-color: #667eea;
            color: white;
        }
        
        /* Alerts */
        .alert-custom {
            border-radius: 10px;
            border: none;
            padding: 12px 16px;
            font-size: 0.9rem;
        }
        
        /* Sucursales Cards */
        .sucursal-card {
            border: 1px solid #e9ecef;
            border-radius: 12px;
            padding: 20px;
            transition: all 0.2s;
            background: white;
        }
        
        .sucursal-card:hover {
            box-shadow: 0 4px 16px rgba(0,0,0,0.08);
            transform: translateY(-2px);
            border-color: #667eea;
        }
        
        .sucursal-card.active {
            background: #fafbff;
        }
        
        .sucursal-card.inactive {
            opacity: 0.75;
        }
        
        .sucursal-badge {
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 0.7rem;
            font-weight: 600;
            text-transform: uppercase;
        }
        
        .badge-active {
            background: #d4edda;
            color: #155724;
        }
        
        .badge-inactive {
            background: #f8d7da;
            color: #721c24;
        }
        
        /* Modal */
        .modal-header {
            background: #667eea;
            color: white;
            border-radius: 12px 12px 0 0;
            padding: 16px 20px;
        }
        
        .modal-content {
            border-radius: 12px;
            border: none;
        }
        
        .btn-close {
            filter: brightness(0) invert(1);
        }
        
        .modal-body {
            padding: 20px;
        }
        
        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 40px 20px;
        }
        
        .empty-state-icon {
            width: 80px;
            height: 80px;
            margin: 0 auto 16px;
            background: #f0f0ff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        /* Main content area */
        .container-fluid {
            position: relative !important;
            z-index: 1 !important;
        }
        
        .tab-content {
            position: relative !important;
            z-index: 1 !important;
        }
        
        .tab-pane {
            position: relative !important;
            z-index: 2 !important;
        }
    </style>
@endpush

@section('content')
<div class="container-fluid" style="position: relative;">
    <!-- Breadcrumb -->
    <div class="row mb-3">
        <div class="col-12">
            <div style="margin-bottom: 16px;">
                <h4 class="mb-2"><i class="ti ti-building me-2"></i>Configuración de Empresa</h4>
            </div>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-custom mb-3" role="alert">
        {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-custom mb-3" role="alert">
        <i class="ti ti-x me-2"></i>{{ session('error') }}
    </div>
    @endif

    <!-- Main Content -->
    <div class="row" style="position: relative; z-index: 1;">
        <!-- Sidebar -->
        <div class="col-lg-3 col-xl-2 mb-4">
            <div class="card settings-card" style="position: relative; z-index: 2;">
                <div class="card-body p-3">
                    <div class="text-center mb-3">
                        <div class="bg-primary bg-opacity-10 h-70 w-70 d-flex-center b-r-10 m-auto mb-2">
                            <i class="ti ti-building f-s-32 text-primary"></i>
                        </div>
                        <h6 class="mb-1 text-truncate">{{ $empresa->nombre ?? 'Mi Empresa' }}</h6>
                        <p class="text-muted small text-truncate mb-0">{{ $empresa->email ?? '' }}</p>
                    </div>

                    <ul class="nav flex-column settings-nav" id="settings-tab" role="tablist" style="position: relative; z-index: 3;">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active w-100" id="empresa-tab" data-bs-toggle="pill" data-bs-target="#empresa" type="button" role="tab">
                                <i class="ti ti-building"></i> Configuración de empresa
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link w-100" id="sucursales-tab" data-bs-toggle="pill" data-bs-target="#sucursales" type="button" role="tab">
                                <i class="ti ti-map-pin"></i> Sucursales
                                <span class="badge bg-primary ms-2">{{ $sucursales->count() }}</span>
                            </button>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="col-lg-9 col-xl-10">
            <div class="tab-content" id="settings-tab-content" style="position: relative; z-index: 1;">

                <!-- Empresa Tab -->
                <div class="tab-pane fade show active" id="empresa" role="tabpanel" style="position: relative;">
                    <div class="card settings-card" style="position: relative; z-index: 1;">
                        <div class="card-header bg-transparent border-0">
                            <h5 class="mb-0"><i class="ti ti-building me-2"></i>Información de la Empresa</h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('settings.empresa.update') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')

                                <h6 class="setting-section-title mb-3">Logos</h6>
                                <div class="row g-4 mb-4">
                                    <!-- Logo Redondo (Login) -->
                                    <div class="col-md-6">
                                        <div class="card h-100">
                                            <div class="card-body text-center">
                                                <h6 class="mb-3">Logo Redondo (Login)</h6>
                                                <div class="mb-3">
                                                    @if($empresa && $empresa->logo)
                                                        <img src="{{ asset('storage/' . $empresa->logo) }}" alt="Logo Redondo" class="company-logo-preview" id="logoPreview">
                                                    @else
                                                        <div class="company-logo-preview d-flex-center bg-light" id="logoPreview">
                                                            <i class="ti ti-camera f-s-40 text-muted"></i>
                                                        </div>
                                                    @endif
                                                </div>
                                                <label class="logo-upload-btn">
                                                    <i class="ti ti-camera me-2"></i>Cambiar Logo
                                                    <input type="file" name="logo" id="logoInput" accept="image/*" style="display: none;" onchange="previewLogo(event, 'logoPreview')">
                                                </label>
                                                <p class="text-muted small mt-2">PNG, JPG hasta 2MB - Se muestra en el login</p>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Logo Rectangular (Menú) -->
                                    <div class="col-md-6">
                                        <div class="card h-100">
                                            <div class="card-body text-center">
                                                <h6 class="mb-3">Logo Rectangular (Menú)</h6>
                                                <div class="mb-3">
                                                    @if($empresa && $empresa->logo_rectangular)
                                                        <img src="{{ asset('storage/' . $empresa->logo_rectangular) }}" alt="Logo Rectangular" class="company-logo-preview" id="logoRectangularPreview">
                                                    @else
                                                        <div class="company-logo-preview d-flex-center bg-light" id="logoRectangularPreview">
                                                            <i class="ti ti-camera f-s-40 text-muted"></i>
                                                        </div>
                                                    @endif
                                                </div>
                                                <label class="logo-upload-btn">
                                                    <i class="ti ti-camera me-2"></i>Cambiar Logo
                                                    <input type="file" name="logo_rectangular" id="logoRectangularInput" accept="image/*" style="display: none;" onchange="previewLogo(event, 'logoRectangularPreview')">
                                                </label>
                                                <p class="text-muted small mt-2">PNG, JPG hasta 2MB - Se muestra en el sidebar</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <h6 class="setting-section-title">Datos Principales</h6>

                                <div class="row g-3">
                                    <div class="col-12">
                                        <label class="form-label">Nombre de la Empresa *</label>
                                        <input type="text" name="nombre" class="form-control"
                                               value="{{ old('nombre', $empresa->nombre ?? '') }}" required>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">Identificación / RUC / NIT *</label>
                                        <input type="text" name="identificacion" class="form-control"
                                               value="{{ old('identificacion', $empresa->identificacion ?? '') }}" required>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">Teléfono</label>
                                        <input type="text" name="telefono" class="form-control"
                                               value="{{ old('telefono', $empresa->telefono ?? '') }}" placeholder="+506 0000 0000">
                                    </div>

                                    <div class="col-12">
                                        <label class="form-label">Correo Electrónico *</label>
                                        <input type="email" name="email" class="form-control"
                                               value="{{ old('email', $empresa->email ?? '') }}" required>
                                    </div>

                                    <div class="col-12">
                                        <label class="form-label">Dirección</label>
                                        <textarea name="direccion" class="form-control" rows="3"
                                                  placeholder="Dirección completa de la empresa">{{ old('direccion', $empresa->direccion ?? '') }}</textarea>
                                    </div>
                                </div>

                                <div class="text-end mt-4">
                                    <button type="submit" class="btn btn-primary">
                                         Guardar Cambios
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Sucursales -->
                <div class="tab-pane fade" id="sucursales" role="tabpanel">
                    <div class="card settings-card">
                        <div class="card-header bg-transparent border-0">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="mb-0"><i class="ti ti-map-pin me-2"></i>Sucursales</h5>
                                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addSucursalModal">
                                    <i class="ti ti-plus me-1"></i> Agregar Sucursal
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            @if($sucursales->count() > 0)
                                <div class="row g-4">
                                    @foreach($sucursales as $sucursal)
                                    <div class="col-md-6 col-xl-4">
                                        <div class="sucursal-card {{ $sucursal->estado ? 'active' : 'inactive' }}">
                                            <div class="d-flex justify-content-between align-items-start mb-3">
                                                <div class="bg-primary bg-opacity-10 h-50 w-50 d-flex-center b-r-10 flex-shrink-0">
                                                    <i class="ti ti-building f-s-24 text-primary"></i>
                                                </div>
                                                <span class="sucursal-badge {{ $sucursal->estado ? 'badge-active' : 'badge-inactive' }}">
                                                    {{ $sucursal->estado ? 'Activa' : 'Inactiva' }}
                                                </span>
                                            </div>
                                            
                                            <h6 class="mb-2">{{ $sucursal->nombre }}</h6>
                                            
                                            <div class="mb-2">
                                                <small class="text-muted">
                                                    <i class="ti ti-map-pin me-1"></i>
                                                    {{ $sucursal->direccion }}
                                                </small>
                                            </div>
                                            
                                            @if($sucursal->telefono ?? $sucursal->contacto)
                                            <div class="mb-2">
                                                <small class="text-muted">
                                                    <i class="ti ti-phone me-1"></i>
                                                    {{ $sucursal->telefono ?? $sucursal->contacto }}
                                                </small>
                                            </div>
                                            @endif
                                            
                                            @if($sucursal->email)
                                            <div class="mb-2">
                                                <small class="text-muted">
                                                    <i class="ti ti-mail me-1"></i>
                                                    {{ $sucursal->email }}
                                                </small>
                                            </div>
                                            @endif
                                            
                                            @if($sucursal->encargado)
                                            <div class="mb-3">
                                                <small class="text-muted">
                                                    <i class="ti ti-user me-1"></i>
                                                    Encargado: {{ $sucursal->encargado }}
                                                </small>
                                            </div>
                                            @endif
                                            
                                            <div class="d-flex gap-2 mt-3">
                                                <button class="btn btn-sm btn-outline-primary flex-grow-1" 
                                                        onclick="editSucursal({{ json_encode($sucursal) }})">
                                                    <i class="ti ti-edit"></i>
                                                </button>
                                                <form action="{{ route('settings.sucursal.destroy', $sucursal->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" 
                                                            onclick="return confirm('¿Eliminar esta sucursal?')">
                                                        <i class="ti ti-trash"></i>
                                                    </button>
                                                </form>
                                                <button class="btn btn-sm {{ $sucursal->estado ? 'btn-outline-warning' : 'btn-outline-success' }}" 
                                                        onclick="toggleSucursal({{ $sucursal->id }})" title="{{ $sucursal->estado ? 'Desactivar' : 'Activar' }}">
                                                    <i class="ti ti-{{ $sucursal->estado ? 'eye-off' : 'eye' }}"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-5">
                                    <div class="bg-light-primary h-100 w-100 d-flex-center b-r-50 m-auto mb-3">
                                        <i class="ti ti-building f-s-40 text-primary"></i>
                                    </div>
                                    <h5 class="text-muted">No hay sucursales registradas</h5>
                                    <p class="text-muted mb-3">Comienza agregando tu primera sucursal</p>
                                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addSucursalModal">
                                        <i class="ti ti-plus me-1"></i> Agregar Sucursal
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<!-- Modal Agregar Sucursal -->
<div class="modal fade" id="addSucursalModal" tabindex="-1" aria-labelledby="addSucursalModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('settings.sucursal.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="addSucursalModalLabel">
                        <i class="ti ti-building me-2"></i>Agregar Nueva Sucursal
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Nombre de la Sucursal *</label>
                            <input type="text" name="nombre" class="form-control" required placeholder="Ej: Sucursal Centro">
                        </div>
                        
                        <div class="col-12">
                            <label class="form-label">Dirección *</label>
                            <textarea name="direccion" class="form-control" rows="2" required placeholder="Dirección completa"></textarea>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">Teléfono / Contacto</label>
                            <input type="text" name="telefono" class="form-control" placeholder="+506 0000 0000">
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">Correo Electrónico</label>
                            <input type="email" name="email" class="form-control" placeholder="sucursal@empresa.com">
                        </div>
                        
                        <div class="col-12">
                            <label class="form-label">Encargado</label>
                            <input type="text" name="encargado" class="form-control" placeholder="Nombre del encargado">
                        </div>
                        
                        <div class="col-12">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="estado" id="estado" checked>
                                <label class="form-check-label" for="estado">Sucursal Activa</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                         Guardar Sucursal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Editar Sucursal -->
<div class="modal fade" id="editSucursalModal" tabindex="-1" aria-labelledby="editSucursalModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="editSucursalForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title" id="editSucursalModalLabel">
                        <i class="ti ti-edit me-2"></i>Editar Sucursal
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <input type="hidden" name="sucursal_id" id="edit_sucursal_id">
                        
                        <div class="col-12">
                            <label class="form-label">Nombre de la Sucursal *</label>
                            <input type="text" name="nombre" id="edit_nombre" class="form-control" required>
                        </div>
                        
                        <div class="col-12">
                            <label class="form-label">Dirección *</label>
                            <textarea name="direccion" id="edit_direccion" class="form-control" rows="2" required></textarea>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">Teléfono / Contacto</label>
                            <input type="text" name="telefono" id="edit_telefono" class="form-control">
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">Correo Electrónico</label>
                            <input type="email" name="email" id="edit_email" class="form-control">
                        </div>
                        
                        <div class="col-12">
                            <label class="form-label">Encargado</label>
                            <input type="text" name="encargado" id="edit_encargado" class="form-control">
                        </div>
                        
                        <div class="col-12">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="estado" id="edit_estado" checked>
                                <label class="form-check-label" for="edit_estado">Sucursal Activa</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        Actualizar Sucursal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script>
        // Preview de logo - funciona para ambos logos (redondo y rectangular)
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

        // Editar sucursal
        function editSucursal(sucursal) {
            document.getElementById('edit_sucursal_id').value = sucursal.id;
            document.getElementById('edit_nombre').value = sucursal.nombre;
            document.getElementById('edit_direccion').value = sucursal.direccion;
            document.getElementById('edit_telefono').value = sucursal.telefono || sucursal.contacto || '';
            document.getElementById('edit_email').value = sucursal.email || '';
            document.getElementById('edit_encargado').value = sucursal.encargado || '';
            document.getElementById('edit_estado').checked = sucursal.estado;
            
            document.getElementById('editSucursalForm').action = '/settings/sucursal/' + sucursal.id;
            
            const modal = new bootstrap.Modal(document.getElementById('editSucursalModal'));
            modal.show();
        }

        // Toggle estado sucursal
        function toggleSucursal(id) {
            if (confirm('¿Cambiar estado de esta sucursal?')) {
                window.location.href = '/settings/sucursal/' + id + '/toggle';
            }
        }
    </script>
@endpush
