<div class="row g-3 mb-4">
    <div class="col-sm-4">
        <div class="card border-0 srv-stat-card">
            <div class="card-body d-flex align-items-center gap-3 py-3">
                <div class="srv-stat-icon"><i class="ti ti-medical-cross"></i></div>
                <div>
                    <p class="srv-stat-label mb-0">Total Servicios</p>
                    <h4 class="srv-stat-value mb-0">{{ $stats['total'] }}</h4>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="card border-0 srv-stat-card">
            <div class="card-body d-flex align-items-center gap-3 py-3">
                <div class="srv-stat-icon srv-stat-icon--success"><i class="ti ti-check"></i></div>
                <div>
                    <p class="srv-stat-label mb-0">Activos</p>
                    <h4 class="srv-stat-value mb-0">{{ $stats['activos'] }}</h4>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="card border-0 srv-stat-card">
            <div class="card-body d-flex align-items-center gap-3 py-3">
                <div class="srv-stat-icon srv-stat-icon--warning"><i class="ti ti-coin"></i></div>
                <div>
                    <p class="srv-stat-label mb-0">Precio Promedio</p>
                    <h4 class="srv-stat-value mb-0">${{ number_format($stats['precio_promedio'], 2) }}</h4>
                </div>
            </div>
        </div>
    </div>
</div>
