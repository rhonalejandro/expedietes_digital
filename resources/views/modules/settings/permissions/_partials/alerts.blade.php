{{-- 
Desarrollo - Permisos de Usuarios - UI - 2026-02-18
Partial: alerts.blade.php (< 30 líneas)
--}}

@if(session('success'))
    <div class="alert alert-success alert-custom mb-3" role="alert">
        <i class="ti ti-check me-2"></i>{{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-custom mb-3" role="alert">
        <i class="ti ti-x me-2"></i>{{ session('error') }}
    </div>
@endif

@if($errors->any())
    <div class="alert alert-warning alert-custom mb-3">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li><i class="ti ti-alert-triangle me-2"></i>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
