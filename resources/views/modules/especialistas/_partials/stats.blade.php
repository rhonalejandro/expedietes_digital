<div class="row g-3 mb-4">

    <div class="col-sm-6">
        <div class="card border-0 esp-stat-card">
            <div class="card-body d-flex align-items-center gap-3 py-3">
                <div class="esp-stat-icon">
                    <i class="ti ti-users"></i>
                </div>
                <div>
                    <p class="esp-stat-label mb-0">Total Especialistas</p>
                    <h4 class="esp-stat-value mb-0">{{ $stats['total'] }}</h4>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6">
        <div class="card border-0 esp-stat-card">
            <div class="card-body d-flex align-items-center gap-3 py-3">
                <div class="esp-stat-icon esp-stat-icon--success">
                    <i class="ti ti-user-check"></i>
                </div>
                <div>
                    <p class="esp-stat-label mb-0">Activos</p>
                    <h4 class="esp-stat-value mb-0">{{ $stats['activos'] }}</h4>
                </div>
            </div>
        </div>
    </div>

</div>
