<div class="row g-3 mb-4">

    {{-- Total pacientes --}}
    <div class="col-sm-6 col-lg-4">
        <div class="card border-0 pac-stat-card">
            <div class="card-body d-flex align-items-center gap-3 py-3">
                <div class="pac-stat-icon">
                    <i class="ti ti-users"></i>
                </div>
                <div>
                    <p class="pac-stat-label mb-0">Total Pacientes</p>
                    <h4 class="pac-stat-value mb-0">{{ $stats['total'] }}</h4>
                </div>
            </div>
        </div>
    </div>

    {{-- Activos --}}
    <div class="col-sm-6 col-lg-4">
        <div class="card border-0 pac-stat-card">
            <div class="card-body d-flex align-items-center gap-3 py-3">
                <div class="pac-stat-icon pac-stat-icon--success">
                    <i class="ti ti-user-check"></i>
                </div>
                <div>
                    <p class="pac-stat-label mb-0">Activos</p>
                    <h4 class="pac-stat-value mb-0">{{ $stats['activos'] }}</h4>
                </div>
            </div>
        </div>
    </div>

    {{-- Nuevos este mes --}}
    <div class="col-sm-6 col-lg-4">
        <div class="card border-0 pac-stat-card">
            <div class="card-body d-flex align-items-center gap-3 py-3">
                <div class="pac-stat-icon pac-stat-icon--info">
                    <i class="ti ti-user-plus"></i>
                </div>
                <div>
                    <p class="pac-stat-label mb-0">Nuevos este mes</p>
                    <h4 class="pac-stat-value mb-0">{{ $stats['nuevos'] }}</h4>
                </div>
            </div>
        </div>
    </div>

</div>
