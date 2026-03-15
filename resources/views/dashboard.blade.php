@extends('layouts.admin.master')

@section('title', 'Dashboard')

@push('styles')
    <!-- ApexCharts CSS -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/apexcharts/apexcharts.css') }}">
    
    <!-- Custom Dashboard Styles -->
    <style>
        /* Dashboard Cards con gradientes */
        .dashboard-card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
            overflow: hidden;
        }
        
        .dashboard-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 30px rgba(0,0,0,0.12);
        }
        
        .card-icon-box {
            width: 60px;
            height: 60px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
        }
        
        .bg-primary-solid { background-color: #667eea; }
        .bg-success-solid { background-color: #11998e; }
        .bg-warning-solid { background-color: #f5576c; }
        .bg-info-solid { background-color: #4facfe; }
        
        .stat-number {
            font-size: 2rem;
            font-weight: 700;
        }
        
        .chart-container {
            position: relative;
            min-height: 300px;
        }
        
        .recent-activity-item {
            padding: 12px 0;
            border-bottom: 1px solid #f0f0f0;
            transition: background 0.2s;
        }
        
        .recent-activity-item:hover {
            background: #f8f9fa;
            border-radius: 8px;
        }
        
        .activity-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        
        .welcome-banner {
            background-color: #667eea;
            border-radius: 20px;
            padding: 30px;
            color: white;
            margin-bottom: 30px;
            position: relative;
            overflow: hidden;
        }
        
        .welcome-banner::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -10%;
            width: 300px;
            height: 300px;
            background: rgba(255,255,255,0.1);
            border-radius: 50%;
        }
        
        .quick-action-btn {
            border-radius: 12px;
            padding: 15px 20px;
            transition: all 0.3s;
            border: 2px solid transparent;
        }
        
        .quick-action-btn:hover {
            transform: scale(1.05);
            border-color: #667eea;
        }
    </style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Welcome Banner -->
    <div class="welcome-banner">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h2 class="mb-2">👋 ¡Bienvenido, {{ auth()->user()->nombre ?? 'Administrador' }}!</h2>
                <p class="mb-0 opacity-75">Panel de control - Expediente Digital</p>
            </div>
            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                <p class="mb-1 opacity-75">Fecha de hoy</p>
                <h5 class="mb-0">{{ now()->format('l, d \d\e F \d\e\l Y') }}</h5>
                <a href="{{ route('settings.index') }}" class="btn btn-light btn-sm mt-2">
                    <i class="ti ti-settings me-1"></i> Configuración
                </a>
            </div>
        </div>
    </div>

    <!-- Estadísticas Principales -->
    <div class="row g-4 mb-4">
        <!-- Pacientes -->
        <div class="col-md-6 col-xl-3">
            <div class="card dashboard-card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="flex-grow-1">
                            <p class="text-muted mb-1">Total Pacientes</p>
                            <h3 class="stat-number text-primary mb-0" id="totalPacientes">0</h3>
                            <p class="text-success mb-0 mt-2">
                                <i class="ti ti-trending-up"></i>
                                <span class="ms-1">+12% este mes</span>
                            </p>
                        </div>
                        <div class="card-icon-box bg-primary-solid text-white shadow">
                            <i class="ti ti-users"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Doctores -->
        <div class="col-md-6 col-xl-3">
            <div class="card dashboard-card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="flex-grow-1">
                            <p class="text-muted mb-1">Doctores Activos</p>
                            <h3 class="stat-number text-success mb-0" id="totalDoctores">0</h3>
                            <p class="text-success mb-0 mt-2">
                                <span class="ms-1">Todos operativos</span>
                            </p>
                        </div>
                        <div class="card-icon-box bg-success-solid text-white shadow">
                            <i class="ti ti-user-md"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Citas Hoy -->
        <div class="col-md-6 col-xl-3">
            <div class="card dashboard-card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="flex-grow-1">
                            <p class="text-muted mb-1">Citas Hoy</p>
                            <h3 class="stat-number text-warning mb-0" id="citasHoy">0</h3>
                            <p class="text-warning mb-0 mt-2">
                                <i class="ti ti-clock"></i>
                                <span class="ms-1">3 pendientes</span>
                            </p>
                        </div>
                        <div class="card-icon-box bg-warning-solid text-white shadow">
                            <i class="ti ti-calendar"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Casos Activos -->
        <div class="col-md-6 col-xl-3">
            <div class="card dashboard-card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="flex-grow-1">
                            <p class="text-muted mb-1">Casos Activos</p>
                            <h3 class="stat-number text-info mb-0" id="casosActivos">0</h3>
                            <p class="text-info mb-0 mt-2">
                                <i class="ti ti-folder-open"></i>
                                <span class="ms-1">5 nuevos esta semana</span>
                            </p>
                        </div>
                        <div class="card-icon-box bg-info-solid text-white shadow">
                            <i class="ti ti-folder"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráficos y Actividad Reciente -->
    <div class="row g-4 mb-4">
        <!-- Gráfico de Citas -->
        <div class="col-lg-8">
            <div class="card dashboard-card">
                <div class="card-header bg-transparent border-0 pb-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="ti ti-chart-bar me-2"></i>
                            Citas por Semana
                        </h5>
                        <select class="form-select form-select-sm" style="width: auto;">
                            <option>Esta semana</option>
                            <option>Semana pasada</option>
                            <option>Este mes</option>
                        </select>
                    </div>
                </div>
                <div class="card-body">
                    <div id="citasChart" class="chart-container"></div>
                </div>
            </div>
        </div>

        <!-- Actividad Reciente -->
        <div class="col-lg-4">
            <div class="card dashboard-card h-100">
                <div class="card-header bg-transparent border-0 pb-0">
                    <h5 class="mb-0">
                        <i class="ti ti-activity me-2"></i>
                        Actividad Reciente
                    </h5>
                </div>
                <div class="card-body">
                    <div class="recent-activity-item d-flex align-items-center gap-3">
                        <div class="activity-icon bg-primary bg-opacity-10 text-primary">
                            <i class="ti ti-user-plus"></i>
                        </div>
                        <div class="flex-grow-1">
                            <p class="mb-0 fw-medium">Nuevo paciente registrado</p>
                            <small class="text-muted">Hace 5 minutos</small>
                        </div>
                    </div>
                    
                    <div class="recent-activity-item d-flex align-items-center gap-3">
                        <div class="activity-icon bg-success bg-opacity-10 text-success">
                            <i class="ti ti-calendar-check"></i>
                        </div>
                        <div class="flex-grow-1">
                            <p class="mb-0 fw-medium">Cita completada</p>
                            <small class="text-muted">Hace 15 minutos</small>
                        </div>
                    </div>
                    
                    <div class="recent-activity-item d-flex align-items-center gap-3">
                        <div class="activity-icon bg-warning bg-opacity-10 text-warning">
                            <i class="ti ti-file-text"></i>
                        </div>
                        <div class="flex-grow-1">
                            <p class="mb-0 fw-medium">Expediente actualizado</p>
                            <small class="text-muted">Hace 1 hora</small>
                        </div>
                    </div>
                    
                    <div class="recent-activity-item d-flex align-items-center gap-3">
                        <div class="activity-icon bg-info bg-opacity-10 text-info">
                            <i class="ti ti-message"></i>
                        </div>
                        <div class="flex-grow-1">
                            <p class="mb-0 fw-medium">Nueva consulta creada</p>
                            <small class="text-muted">Hace 2 horas</small>
                        </div>
                    </div>
                    
                    <div class="recent-activity-item d-flex align-items-center gap-3">
                        <div class="activity-icon bg-danger bg-opacity-10 text-danger">
                            <i class="ti ti-clock-exclamation"></i>
                        </div>
                        <div class="flex-grow-1">
                            <p class="mb-0 fw-medium">Cita programada</p>
                            <small class="text-muted">Hace 3 horas</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Acciones Rápidas -->
    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="card dashboard-card">
                <div class="card-header bg-transparent border-0 pb-0">
                    <h5 class="mb-0">
                        <i class="ti ti-lightning me-2"></i>
                        Acciones Rápidas
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-6 col-md-3">
                            <a href="#" class="quick-action-btn btn btn-light-primary w-100 text-start d-flex align-items-center gap-3">
                                <i class="ti ti-user-plus f-s-24"></i>
                                <div>
                                    <span class="d-block fw-medium">Nuevo Paciente</span>
                                    <small class="text-muted">Registrar</small>
                                </div>
                            </a>
                        </div>
                        <div class="col-6 col-md-3">
                            <a href="#" class="quick-action-btn btn btn-light-success w-100 text-start d-flex align-items-center gap-3">
                                <i class="ti ti-calendar-plus f-s-24"></i>
                                <div>
                                    <span class="d-block fw-medium">Nueva Cita</span>
                                    <small class="text-muted">Agendar</small>
                                </div>
                            </a>
                        </div>
                        <div class="col-6 col-md-3">
                            <a href="#" class="quick-action-btn btn btn-light-warning w-100 text-start d-flex align-items-center gap-3">
                                <i class="ti ti-file-plus f-s-24"></i>
                                <div>
                                    <span class="d-block fw-medium">Nuevo Caso</span>
                                    <small class="text-muted">Crear</small>
                                </div>
                            </a>
                        </div>
                        <div class="col-6 col-md-3">
                            <a href="#" class="quick-action-btn btn btn-light-info w-100 text-start d-flex align-items-center gap-3">
                                <i class="ti ti-file-text f-s-24"></i>
                                <div>
                                    <span class="d-block fw-medium">Expediente</span>
                                    <small class="text-muted">Consultar</small>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Doctores y Pacientes -->
    <div class="row g-4">
        <!-- Top Doctores -->
        <div class="col-lg-6">
            <div class="card dashboard-card h-100">
                <div class="card-header bg-transparent border-0 pb-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="ti ti-star me-2"></i>
                            Doctores Destacados
                        </h5>
                        <a href="#" class="btn btn-sm btn-primary">Ver todos</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="recent-activity-item d-flex align-items-center gap-3">
                        <img src="{{ asset('assets/images/avatar/1.png') }}" alt="Doctor" class="rounded-circle" width="50">
                        <div class="flex-grow-1">
                            <p class="mb-0 fw-medium">Dr. María González</p>
                            <small class="text-muted">Cardiología • 25 citas esta semana</small>
                        </div>
                        <span class="badge bg-success">Activo</span>
                    </div>
                    
                    <div class="recent-activity-item d-flex align-items-center gap-3">
                        <img src="{{ asset('assets/images/avatar/2.png') }}" alt="Doctor" class="rounded-circle" width="50">
                        <div class="flex-grow-1">
                            <p class="mb-0 fw-medium">Dr. Carlos Rodríguez</p>
                            <small class="text-muted">Pediatría • 18 citas esta semana</small>
                        </div>
                        <span class="badge bg-success">Activo</span>
                    </div>
                    
                    <div class="recent-activity-item d-flex align-items-center gap-3">
                        <img src="{{ asset('assets/images/avatar/3.png') }}" alt="Doctor" class="rounded-circle" width="50">
                        <div class="flex-grow-1">
                            <p class="mb-0 fw-medium">Dra. Ana Martínez</p>
                            <small class="text-muted">Dermatología • 15 citas esta semana</small>
                        </div>
                        <span class="badge bg-warning">Ocupado</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Próximas Citas -->
        <div class="col-lg-6">
            <div class="card dashboard-card h-100">
                <div class="card-header bg-transparent border-0 pb-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="ti ti-clock me-2"></i>
                            Próximas Citas
                        </h5>
                        <a href="#" class="btn btn-sm btn-primary">Ver calendario</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="recent-activity-item d-flex align-items-center gap-3">
                        <div class="activity-icon bg-primary bg-opacity-10 text-primary">
                            <i class="ti ti-clock-hour-3"></i>
                        </div>
                        <div class="flex-grow-1">
                            <p class="mb-0 fw-medium">Juan Pérez - Consulta General</p>
                            <small class="text-muted">Hoy, 10:00 AM • Dr. González</small>
                        </div>
                        <span class="badge bg-primary">Confirmada</span>
                    </div>
                    
                    <div class="recent-activity-item d-flex align-items-center gap-3">
                        <div class="activity-icon bg-success bg-opacity-10 text-success">
                            <i class="ti ti-clock-hour-4"></i>
                        </div>
                        <div class="flex-grow-1">
                            <p class="mb-0 fw-medium">María López - Seguimiento</p>
                            <small class="text-muted">Hoy, 11:30 AM • Dr. Rodríguez</small>
                        </div>
                        <span class="badge bg-success">En espera</span>
                    </div>
                    
                    <div class="recent-activity-item d-flex align-items-center gap-3">
                        <div class="activity-icon bg-warning bg-opacity-10 text-warning">
                            <i class="ti ti-clock-hour-5"></i>
                        </div>
                        <div class="flex-grow-1">
                            <p class="mb-0 fw-medium">Pedro Sánchez - Primera vez</p>
                            <small class="text-muted">Hoy, 02:00 PM • Dra. Martínez</small>
                        </div>
                        <span class="badge bg-warning">Pendiente</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <!-- ApexCharts -->
    <script src="{{ asset('assets/vendor/apexcharts/apexcharts.min.js') }}"></script>
    
    <script>
        // Gráfico de Citas por Semana
        var citasChartOptions = {
            series: [{
                name: 'Citas',
                data: [12, 18, 15, 22, 28, 35, 40]
            }],
            chart: {
                height: 300,
                type: 'area',
                toolbar: { show: false },
                fontFamily: 'Rubik, sans-serif'
            },
            colors: ['#667eea'],
            dataLabels: { enabled: false },
            stroke: { curve: 'smooth', width: 3 },
            fill: {
                type: 'solid',
                opacity: 0.15
            },
            xaxis: {
                categories: ['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'],
                axisBorder: { show: false },
                axisTicks: { show: false }
            },
            yaxis: { show: true },
            grid: {
                borderColor: '#f0f0f0',
                strokeDashArray: 4,
            },
            tooltip: { theme: 'light' },
            markers: {
                size: 6,
                colors: ['#667eea'],
                strokeColors: '#fff',
                strokeWidth: 2,
            }
        };

        var citasChart = new ApexCharts(document.querySelector("#citasChart"), citasChartOptions);
        citasChart.render();

        // Animación de números (simulados - luego se cargarán de BD)
        function animateValue(element, start, end, duration) {
            let startTimestamp = null;
            const step = (timestamp) => {
                if (!startTimestamp) startTimestamp = timestamp;
                const progress = Math.min((timestamp - startTimestamp) / duration, 1);
                element.innerHTML = Math.floor(progress * (end - start) + start);
                if (progress < 1) {
                    window.requestAnimationFrame(step);
                }
            };
            window.requestAnimationFrame(step);
        }

        // Animar estadísticas (valores de ejemplo)
        document.addEventListener('DOMContentLoaded', function() {
            animateValue(document.getElementById("totalPacientes"), 0, 1250, 2000);
            animateValue(document.getElementById("totalDoctores"), 0, 24, 2000);
            animateValue(document.getElementById("citasHoy"), 0, 18, 2000);
            animateValue(document.getElementById("casosActivos"), 0, 86, 2000);
        });
    </script>
@endpush
