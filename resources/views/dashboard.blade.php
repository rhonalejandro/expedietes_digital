@extends('layouts.admin.master')
@section('title', 'Dashboard')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/vendor/apexcharts/apexcharts.css') }}">
    <style>
        .dash-card {
            border: none;
            border-radius: 14px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.06);
            transition: transform .2s, box-shadow .2s;
        }
        .dash-card:hover { transform: translateY(-3px); box-shadow: 0 6px 20px rgba(0,0,0,0.1); }

        .kpi-icon {
            width: 52px; height: 52px;
            border-radius: 14px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.5rem; flex-shrink: 0;
        }
        .kpi-number { font-size: 1.9rem; font-weight: 700; line-height: 1; }
        .kpi-label  { font-size: .78rem; color: #8a94a6; font-weight: 500; text-transform: uppercase; letter-spacing: .04em; }
        .kpi-sub    { font-size: .76rem; margin-top: 4px; }

        .welcome-bar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 16px;
            color: #fff;
            padding: 24px 28px;
            position: relative;
            overflow: hidden;
        }
        .welcome-bar::after {
            content: '';
            position: absolute;
            right: -40px; top: -40px;
            width: 200px; height: 200px;
            background: rgba(255,255,255,.08);
            border-radius: 50%;
            pointer-events: none;
        }

        .esp-initials {
            width: 38px; height: 38px;
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-weight: 700; font-size: .78rem;
            background: rgba(102,126,234,.12);
            color: #667eea;
            flex-shrink: 0;
        }
        .cita-row { padding: 9px 0; border-bottom: 1px solid #f1f3f5; }
        .cita-row:last-child { border-bottom: none; }

        .badge-estatus-confirmada { background: #d4edda; color: #276749; }
        .badge-estatus-pendiente  { background: #fff3cd; color: #856404; }
        .badge-estatus-cancelada  { background: #f8d7da; color: #721c24; }

        .section-title {
            font-size: .82rem;
            font-weight: 700;
            color: #8a94a6;
            text-transform: uppercase;
            letter-spacing: .06em;
            margin-bottom: 14px;
        }
    </style>
@endpush

@section('content')
<div class="container-fluid py-3 px-4">

    {{-- ── Bienvenida ──────────────────────────────────────────────────────── --}}
    <div class="welcome-bar mb-4">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div>
                <h4 class="mb-1 fw-bold" style="color:#fff;">¡Bienvenido, {{ auth()->user()->nombre ?? 'Administrador' }}!</h4>
                <p class="mb-0" style="font-size:.88rem;color:rgba(255,255,255,.8);">
                    {{ now()->locale('es')->isoFormat('dddd, D [de] MMMM [de] YYYY') }}
                    &nbsp;·&nbsp; Global Feet Panama
                </p>
            </div>
            <a href="{{ route('citas.index') }}" class="btn btn-light btn-sm fw-semibold">
                <i class="ti ti-calendar me-1"></i>Ver calendario
            </a>
        </div>
    </div>

    {{-- ── KPIs ─────────────────────────────────────────────────────────────── --}}
    <div class="row g-3 mb-4">

        {{-- Pacientes --}}
        <div class="col-6 col-xl-3">
            <div class="card dash-card p-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="kpi-icon" style="background:rgba(102,126,234,.12);color:#667eea;">
                        <i class="ti ti-users"></i>
                    </div>
                    <div>
                        <div class="kpi-label">Pacientes</div>
                        <div class="kpi-number text-primary">{{ number_format($totalPacientes) }}</div>
                        <div class="kpi-sub text-success">
                            <i class="ti ti-user-plus" style="font-size:.7rem"></i>
                            {{ $nuevosPacientesMes }} nuevos este mes
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Especialistas --}}
        <div class="col-6 col-xl-3">
            <div class="card dash-card p-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="kpi-icon" style="background:rgba(17,153,142,.12);color:#11998e;">
                        <i class="ti ti-stethoscope"></i>
                    </div>
                    <div>
                        <div class="kpi-label">Especialistas</div>
                        <div class="kpi-number text-success">{{ $totalEspecialistas }}</div>
                        <div class="kpi-sub text-muted">Todos operativos</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Citas hoy --}}
        <div class="col-6 col-xl-3">
            <div class="card dash-card p-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="kpi-icon" style="background:rgba(245,87,108,.12);color:#f5576c;">
                        <i class="ti ti-calendar-event"></i>
                    </div>
                    <div>
                        <div class="kpi-label">Citas hoy</div>
                        <div class="kpi-number" style="color:#f5576c;">{{ $citasHoy }}</div>
                        <div class="kpi-sub text-muted">
                            <span class="text-success">{{ $citasHoyConfirmadas }} confirmadas</span>
                            &nbsp;·&nbsp;
                            <span class="text-warning">{{ $citasHoyPendientes }} pendientes</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Citas del mes --}}
        <div class="col-6 col-xl-3">
            <div class="card dash-card p-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="kpi-icon" style="background:rgba(79,172,254,.12);color:#4facfe;">
                        <i class="ti ti-calendar-stats"></i>
                    </div>
                    <div>
                        <div class="kpi-label">Citas este mes</div>
                        <div class="kpi-number" style="color:#4facfe;">{{ number_format($citasMes) }}</div>
                        <div class="kpi-sub text-muted">
                            {{ $casosActivos }} casos activos
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- ── Gráficos ──────────────────────────────────────────────────────────── --}}
    <div class="row g-3 mb-4">

        {{-- Citas por día esta semana --}}
        <div class="col-lg-8">
            <div class="card dash-card p-3 h-100">
                <div class="section-title">Citas esta semana <span class="text-primary fw-normal normal-case" style="text-transform:none;letter-spacing:0">({{ array_sum($citasSemana) }} en total)</span></div>
                <div id="chart-semana"></div>
            </div>
        </div>

        {{-- Distribución por estatus --}}
        <div class="col-lg-4">
            <div class="card dash-card p-3 h-100">
                <div class="section-title">Estatus — este mes</div>
                <div id="chart-estatus"></div>
                <div class="mt-3 d-flex flex-column gap-2">
                    @php
                        $colors = ['confirmada'=>'#38a169','pendiente'=>'#94a3b8','cancelada'=>'#e53e3e','atendida'=>'#4a5568','no_asistio'=>'#dd6b20'];
                        $labels = ['confirmada'=>'Confirmada','pendiente'=>'Pendiente','cancelada'=>'Cancelada','atendida'=>'Atendida','no_asistio'=>'No asistió'];
                    @endphp
                    @foreach($distribucionEstatus as $estatus => $total)
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-2">
                            <span style="width:10px;height:10px;border-radius:3px;background:{{ $colors[$estatus] ?? '#aaa' }};display:inline-block;"></span>
                            <span style="font-size:.78rem;color:#4a5568;">{{ $labels[$estatus] ?? $estatus }}</span>
                        </div>
                        <span style="font-size:.78rem;font-weight:600;color:#2d3748;">{{ number_format($total) }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

    </div>

    {{-- ── Próximas citas + Top especialistas ───────────────────────────────── --}}
    <div class="row g-3">

        {{-- Próximas citas --}}
        <div class="col-lg-7">
            <div class="card dash-card p-3">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="section-title mb-0">Próximas citas</div>
                    <a href="{{ route('citas.index') }}" class="btn btn-sm btn-outline-primary" style="font-size:.75rem;padding:3px 10px;">
                        Ver todas <i class="ti ti-arrow-right ms-1"></i>
                    </a>
                </div>
                @forelse($proximasCitas as $cita)
                @php
                    $nombrePac = $cita->paciente
                        ? $cita->paciente->persona->nombre . ' ' . $cita->paciente->persona->apellido
                        : ($cita->nombre_lead ?? 'Lead');
                    $esFecha = $cita->fecha->isToday() ? 'Hoy' : $cita->fecha->format('d/m');
                @endphp
                <div class="cita-row d-flex align-items-center gap-3">
                    <div style="min-width:42px;text-align:center;">
                        <div style="font-size:.7rem;color:#8a94a6;font-weight:600;">{{ $esFecha }}</div>
                        <div style="font-size:.88rem;font-weight:700;color:#2d3748;">{{ substr($cita->hora_inicio,0,5) }}</div>
                    </div>
                    <div class="flex-grow-1" style="min-width:0;">
                        <div style="font-size:.82rem;font-weight:600;color:#2d3748;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $nombrePac }}</div>
                        <div style="font-size:.72rem;color:#8a94a6;">{{ $cita->especialista->nombre_completo }}</div>
                    </div>
                    <span class="badge badge-estatus-{{ $cita->estatus }}" style="font-size:.68rem;border-radius:6px;padding:3px 8px;">
                        {{ ucfirst($cita->estatus) }}
                    </span>
                </div>
                @empty
                <p class="text-muted text-center py-3 mb-0" style="font-size:.82rem;">No hay citas próximas programadas.</p>
                @endforelse
            </div>
        </div>

        {{-- Top especialistas esta semana --}}
        <div class="col-lg-5">
            <div class="card dash-card p-3">
                <div class="section-title mb-3">Top especialistas — esta semana</div>
                @foreach($topEspecialistas as $item)
                @php
                    $nombre = $item->especialista->nombre_completo;
                    $iniciales = collect(explode(' ', $nombre))->map(fn($w) => strtoupper($w[0] ?? ''))->take(2)->implode('');
                    $maxCitas = $topEspecialistas->first()->total;
                    $pct = $maxCitas > 0 ? round(($item->total / $maxCitas) * 100) : 0;
                @endphp
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="esp-initials">{{ $iniciales }}</div>
                    <div class="flex-grow-1" style="min-width:0;">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span style="font-size:.8rem;font-weight:600;color:#2d3748;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:170px;">{{ $nombre }}</span>
                            <span style="font-size:.75rem;font-weight:700;color:#667eea;">{{ $item->total }}</span>
                        </div>
                        <div style="background:#f1f3f5;border-radius:4px;height:5px;">
                            <div style="background:#667eea;border-radius:4px;height:5px;width:{{ $pct }}%;"></div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('assets/vendor/apexcharts/apexcharts.min.js') }}"></script>
<script>
// ── Gráfico citas por día de la semana ──────────────────────────────────
new ApexCharts(document.querySelector('#chart-semana'), {
    series: [{ name: 'Citas', data: @json($citasSemana) }],
    chart: { type: 'bar', height: 220, toolbar: { show: false }, fontFamily: 'Rubik, sans-serif' },
    colors: ['#667eea'],
    plotOptions: { bar: { borderRadius: 6, columnWidth: '55%' } },
    dataLabels: { enabled: false },
    xaxis: {
        categories: ['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'],
        axisBorder: { show: false }, axisTicks: { show: false },
        labels: { style: { fontSize: '12px', colors: '#8a94a6' } }
    },
    yaxis: { labels: { style: { colors: '#8a94a6', fontSize: '11px' } } },
    grid: { borderColor: '#f1f3f5', strokeDashArray: 4 },
    tooltip: { theme: 'light' },
}).render();

// ── Gráfico distribución de estatus ─────────────────────────────────────
@php
    $estatusLabels = $distribucionEstatus->keys()->map(fn($k) => ['confirmada'=>'Confirmada','pendiente'=>'Pendiente','cancelada'=>'Cancelada','atendida'=>'Atendida','no_asistio'=>'No asistió'][$k] ?? $k)->values();
    $estatusColors = $distribucionEstatus->keys()->map(fn($k) => ['confirmada'=>'#38a169','pendiente'=>'#94a3b8','cancelada'=>'#e53e3e','atendida'=>'#4a5568','no_asistio'=>'#dd6b20'][$k] ?? '#aaa')->values();
@endphp
new ApexCharts(document.querySelector('#chart-estatus'), {
    series: @json($distribucionEstatus->values()),
    chart: { type: 'donut', height: 180, fontFamily: 'Rubik, sans-serif' },
    labels: @json($estatusLabels),
    colors: @json($estatusColors),
    legend: { show: false },
    dataLabels: { enabled: false },
    plotOptions: { pie: { donut: { size: '65%' } } },
    tooltip: { theme: 'light' },
}).render();
</script>
@endpush
