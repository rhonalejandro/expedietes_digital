@extends('layouts.admin.master')

@section('title', 'Confirmación de Citas')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/modules/citas/css/citas.module.css') }}?v={{ time() }}">
    <link rel="stylesheet" href="{{ asset('assets/components/log-actividad/log-actividad.css') }}?v={{ time() }}">
    <link rel="stylesheet" href="{{ asset('assets/modules/citas/css/confirmacion.css') }}?v={{ time() }}">
    {{-- Select2 --}}
    <link rel="stylesheet" href="{{ asset('assets/vendor/select/select2.min.css') }}">
@endpush

@section('content')

{{-- Toast container (reused from main layout convention) --}}
<div id="toast-container" class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index:1100"></div>

<div class="conf-page">

    {{-- ── Header ──────────────────────────────────────────────────────────── --}}
    <div class="conf-header">
        <div class="conf-header-left">
            <i class="ti ti-circle-check" style="font-size:1.4rem;color:#667eea"></i>
            <h1 class="conf-title">Confirmación de Citas</h1>
            <span class="conf-count-badge" id="conf-total-badge">—</span>
        </div>

        <div class="conf-search-wrap">
            <i class="ti ti-search"></i>
            <input type="text"
                   id="conf-search"
                   class="form-control"
                   placeholder="Buscar paciente, teléfono, email..."
                   autocomplete="off">
        </div>

        <a href="{{ route('citas.index') }}" class="btn btn-sm btn-light" style="border-radius:8px">
            <i class="ti ti-calendar-event me-1"></i>Ver calendario
        </a>
    </div>

    {{-- ── Filtros de estatus ────────────────────────────────────────────────── --}}
    <div class="conf-filtros">
        <button class="conf-filtro-btn active" data-filtro="pendientes">
            <i class="ti ti-clock-hour-4"></i>
            Pendientes
            <span class="conf-filtro-count" id="conf-filtro-count-pendientes">—</span>
        </button>
        <button class="conf-filtro-btn" data-filtro="confirmadas">
            <i class="ti ti-circle-check"></i>
            Confirmadas
            <span class="conf-filtro-count" id="conf-filtro-count-confirmadas">—</span>
        </button>
        <button class="conf-filtro-btn" data-filtro="rechazadas">
            <i class="ti ti-circle-x"></i>
            Canceladas / No asistió
            <span class="conf-filtro-count" id="conf-filtro-count-rechazadas">—</span>
        </button>
        <span class="conf-filtros-sub">
            <i class="ti ti-calendar-due" style="font-size:.8rem"></i>
            Solo citas vigentes (hoy en adelante)
        </span>
    </div>

    {{-- ── Cards grid ───────────────────────────────────────────────────────── --}}
    <div id="conf-cards" style="margin-top:4px"></div>

    {{-- Empty state --}}
    <div id="conf-empty">
        <i class="ti ti-calendar-off"></i>
        <p>No hay citas pendientes de confirmación.</p>
    </div>

    {{-- Sentinel for infinite scroll --}}
    <div id="conf-sentinel"></div>

</div>

{{-- ═══════════════════════════════════════════════════════════════════════════
     Modal: Trasladar cita
     ═══════════════════════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="modal-trasladar" tabindex="-1" aria-labelledby="modal-trasladar-label" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-sm">

            <div class="modal-header">
                <h6 class="modal-title" id="modal-trasladar-label">
                    <i class="ti ti-calendar-stats me-2 text-primary"></i>Trasladar Cita
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form id="form-trasladar" novalidate>
                <div class="modal-body p-4">
                    <div class="row g-3">

                        {{-- Especialista --}}
                        <div class="col-md-12">
                            <label class="cita-form-label">Especialista <span class="text-danger">*</span></label>
                            <select name="especialista_id" id="tr-especialista" class="form-select form-select-sm">
                                <option value="">Seleccionar...</option>
                                @foreach($especialistas as $esp)
                                    <option value="{{ $esp->id }}">{{ $esp->nombre_completo }}</option>
                                @endforeach
                            </select>
                            <small class="text-muted" style="font-size:.71rem">
                                Cambia el especialista para ver su disponibilidad.
                            </small>
                        </div>

                        {{-- Calendario + Slots --}}
                        <div class="col-12">
                            <div class="tr-cal-slots-wrap">

                                {{-- Mini calendar column --}}
                                <div>
                                    <label class="cita-form-label mb-2">
                                        <i class="ti ti-calendar-event me-1 text-primary"></i>
                                        Selecciona una fecha
                                    </label>
                                    <div id="tr-calendario" class="tr-cal-wrap">
                                        <div class="tr-cal-loading">Cargando especialista...</div>
                                    </div>
                                    <input type="hidden" id="tr-fecha-hidden" name="fecha" required>
                                </div>

                                {{-- Slots column --}}
                                <div id="tr-bloque-slots" style="display:none">
                                    <label class="cita-form-label mb-2">
                                        <i class="ti ti-clock me-1 text-primary"></i>
                                        Horas disponibles
                                        <span id="tr-fecha-legible" class="text-primary fw-semibold d-block" style="font-size:.78rem;margin-top:2px"></span>
                                    </label>
                                    <div class="tr-slots-col">
                                        <div class="tr-slots-grid" id="tr-slots"></div>
                                    </div>
                                    <input type="hidden" id="tr-hora-inicio-hidden" name="hora_inicio" required>
                                    <input type="hidden" id="tr-hora-fin-hidden"    name="hora_fin"    required>
                                </div>

                            </div>
                        </div>

                    </div>
                </div>

                <div class="modal-footer border-top py-3 px-4">
                    <button type="button" class="btn btn-sm btn-light" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-sm btn-primary">
                        <i class="ti ti-check me-1"></i>Guardar traslado
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════════════════════════
     Modal: Actividad de la cita
     ═══════════════════════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="modal-conf-actividad" tabindex="-1" aria-labelledby="modal-conf-actividad-label" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered conf-act-dialog">
        <div class="modal-content border-0 shadow-sm">

            <div class="modal-header" style="background:#f7f8fc;border-bottom:1px solid #e8ecf0;padding:14px 20px">
                <div>
                    <h6 class="modal-title mb-0" id="modal-conf-actividad-label" style="font-size:.95rem;font-weight:700;color:#2d3748">
                        <i class="ti ti-history me-2" style="color:#667eea"></i>Actividad de la Cita
                    </h6>
                    <span id="conf-act-paciente" style="font-size:.75rem;color:#718096;display:block;margin-top:2px"></span>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span id="la-conf-act-badge" class="conf-act-count-badge">—</span>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
            </div>

            <div class="modal-body p-0">
                <div class="conf-act-body">
                    <div class="la-feed" id="la-conf-act-feed"></div>
                    <div id="la-conf-act-empty" class="la-empty" style="display:none">
                        <i class="ti ti-clock-off"></i>
                        <p>Sin actividad registrada para esta cita.</p>
                    </div>
                    <div id="la-conf-act-sentinel" style="height:40px;display:flex;align-items:center;justify-content:center">
                        <span id="la-conf-act-spinner" class="conf-spinner" style="display:none"></span>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════════════════════════
     Modal: Editar cita (reuse modal-crear-cita partial)
     ═══════════════════════════════════════════════════════════════════════════ --}}
@include('modules.citas._partials.modal-crear')

@endsection

@push('scripts')
    <script src="{{ asset('assets/components/log-actividad/log-actividad.js') }}?v={{ time() }}"></script>
    {{-- Select2 --}}
    <script src="{{ asset('assets/vendor/select/select2.min.js') }}"></script>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        // Init Select2 on tr-especialista if available
        if (typeof $ !== 'undefined' && $.fn.select2) {
            $('#tr-especialista').select2({
                dropdownParent: $('#modal-trasladar'),
                placeholder: 'Seleccionar especialista...',
                allowClear: true,
                width: '100%',
            });
        }
    });
    </script>

    <script src="{{ asset('assets/modules/citas/js/confirmacion.js') }}?v={{ time() }}"></script>
@endpush
