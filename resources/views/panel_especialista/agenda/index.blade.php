@extends('panel_especialista.layouts.master')

@section('title', 'Mi Agenda')
@section('page-title', 'Mi Agenda')

@section('content')
<div class="container-fluid py-3">
<div class="pnl-agenda-wrap">

    {{-- ── Navegador de fechas ──────────────────────────────────────── --}}
    <div class="pnl-date-nav">
        <a href="{{ route('panel.agenda', ['fecha' => $fechaAnterior]) }}" class="pnl-date-nav-btn" title="Día anterior">
            <i class="ti ti-chevron-left"></i>
        </a>

        <div class="pnl-date-nav-center">
            <div class="pnl-date-nav-label">
                @if($esHoy)
                    <span class="pnl-date-nav-hoy-pill">Hoy</span>
                @endif
                <span class="pnl-date-nav-dia">
                    {{ $fechaCarbon->isoFormat('dddd') }}
                </span>
                <span class="pnl-date-nav-fecha">
                    {{ $fechaCarbon->isoFormat('D [de] MMMM [de] YYYY') }}
                </span>
            </div>

            <div class="pnl-date-nav-actions">
                {{-- Picker de fecha --}}
                <input type="date"
                       id="pnl-date-picker"
                       class="pnl-date-picker-input"
                       value="{{ $fecha }}"
                       title="Ir a una fecha">

                @if(!$esHoy)
                    <a href="{{ route('panel.agenda') }}" class="pnl-btn-hoy">
                        <i class="ti ti-calendar-today"></i> Hoy
                    </a>
                @endif
            </div>
        </div>

        <a href="{{ route('panel.agenda', ['fecha' => $fechaSiguiente]) }}" class="pnl-date-nav-btn" title="Día siguiente">
            <i class="ti ti-chevron-right"></i>
        </a>
    </div>

    {{-- ── Resumen del día ─────────────────────────────────────────── --}}
    <div class="pnl-agenda-stats">
        <div class="pnl-stat-card">
            <div class="pnl-stat-icon" style="background:rgba(102,126,234,.12);color:#667eea">
                <i class="ti ti-calendar-event"></i>
            </div>
            <div>
                <div class="pnl-stat-num">{{ $citas->count() }}</div>
                <div class="pnl-stat-label">{{ $esHoy ? 'Citas hoy' : 'Citas del día' }}</div>
            </div>
        </div>
        <div class="pnl-stat-card">
            <div class="pnl-stat-icon" style="background:rgba(56,161,105,.12);color:#38a169">
                <i class="ti ti-circle-check"></i>
            </div>
            <div>
                <div class="pnl-stat-num">{{ $citas->where('estatus','atendida')->count() }}</div>
                <div class="pnl-stat-label">Atendidas</div>
            </div>
        </div>
        <div class="pnl-stat-card">
            <div class="pnl-stat-icon" style="background:rgba(237,137,54,.12);color:#dd6b20">
                <i class="ti ti-clock"></i>
            </div>
            <div>
                <div class="pnl-stat-num">{{ $citas->whereIn('estatus',['pendiente','confirmada'])->count() }}</div>
                <div class="pnl-stat-label">Pendientes</div>
            </div>
        </div>
    </div>

    {{-- ── Lista de citas ───────────────────────────────────────────── --}}
    @if($citas->isEmpty())
        <div class="pnl-empty">
            <i class="ti ti-calendar-off"></i>
            <p>No tienes citas programadas para este día.</p>
            @if(!$esHoy)
                <a href="{{ route('panel.agenda') }}" class="pnl-btn-hoy mt-2">
                    <i class="ti ti-calendar-today"></i> Ver agenda de hoy
                </a>
            @endif
        </div>
    @else
        <div class="pnl-citas-list">
            @foreach($citas as $cita)
                @include('panel_especialista._partials.cita-row', ['cita' => $cita])
            @endforeach
        </div>
    @endif

</div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('pnl-date-picker').addEventListener('change', function () {
    if (this.value) {
        window.location.href = '{{ route('panel.agenda') }}?fecha=' + this.value;
    }
});
</script>
@endpush
