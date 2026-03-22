<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>CRM Global Feet</title>
<style>
/* ── Reset & Base ─────────────────────────────────────────── */
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

:root {
    --primary:   #667eea;
    --primary-d: #5a6fd6;
    --green:     #38a169;
    --green-d:   #2f855a;
    --red:       #e53e3e;
    --red-d:     #c53030;
    --orange:    #dd6b20;
    --gray-50:   #f7f8fc;
    --gray-100:  #edf2f7;
    --gray-200:  #e2e8f0;
    --gray-400:  #a0aec0;
    --gray-600:  #718096;
    --gray-800:  #2d3748;
    --white:     #ffffff;
    --radius:    8px;
    --shadow:    0 1px 3px rgba(0,0,0,.1);
}

body {
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    font-size: 13px;
    color: var(--gray-800);
    background: var(--gray-50);
    min-height: 100vh;
}

/* ── Layout ───────────────────────────────────────────────── */
.w-screen { display: flex; flex-direction: column; min-height: 100vh; }

/* ── Header del widget ────────────────────────────────────── */
.w-header {
    background: var(--primary);
    color: #fff;
    padding: 10px 14px;
    display: flex;
    align-items: center;
    gap: 8px;
    flex-shrink: 0;
    box-shadow: 0 2px 4px rgba(0,0,0,.15);
}
.w-header-logo { font-size: 16px; }
.w-header-title { font-weight: 700; font-size: 13px; flex: 1; }
.w-header-sub { font-size: 10px; opacity: .8; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 140px; }

/* ── Pantallas ────────────────────────────────────────────── */
.w-screen { display: none; flex-direction: column; flex: 1; }
.w-screen.active { display: flex; }

/* ── Pantalla: Esperando ──────────────────────────────────── */
.w-waiting {
    flex: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 12px;
    padding: 24px;
    text-align: center;
    color: var(--gray-600);
}
.w-waiting-icon { font-size: 36px; opacity: .4; }
.w-waiting p { font-size: 12px; line-height: 1.5; }

/* ── Spinner ──────────────────────────────────────────────── */
.w-spinner {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    color: var(--gray-600);
    font-size: 12px;
}
.spin {
    width: 20px; height: 20px;
    border: 2px solid var(--gray-200);
    border-top-color: var(--primary);
    border-radius: 50%;
    animation: spin .7s linear infinite;
}
@keyframes spin { to { transform: rotate(360deg); } }

/* ── Cards & contenido ────────────────────────────────────── */
.w-body { flex: 1; overflow-y: auto; padding: 12px; display: flex; flex-direction: column; gap: 10px; }

.w-card {
    background: var(--white);
    border-radius: var(--radius);
    box-shadow: var(--shadow);
    border: 1px solid var(--gray-200);
    overflow: hidden;
}

.w-card-head {
    padding: 10px 12px 8px;
    border-bottom: 1px solid var(--gray-100);
    display: flex;
    align-items: flex-start;
    gap: 8px;
}
.w-card-head-icon {
    width: 32px; height: 32px;
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: 14px;
    flex-shrink: 0;
}
.icon-paciente { background: #ebf4ff; color: var(--primary); }
.icon-lead     { background: #fffbeb; color: var(--orange); }
.icon-notfound { background: var(--gray-100); color: var(--gray-400); }

.w-card-name { font-weight: 700; font-size: 13px; color: var(--gray-800); line-height: 1.2; }
.w-card-meta { font-size: 11px; color: var(--gray-600); margin-top: 2px; }
.w-card-badge {
    margin-left: auto;
    font-size: 10px;
    font-weight: 600;
    padding: 2px 7px;
    border-radius: 20px;
    white-space: nowrap;
    flex-shrink: 0;
}
.badge-paciente { background: #ebf4ff; color: var(--primary); }
.badge-lead     { background: #fffbeb; color: var(--orange); }

/* ── Citas list ───────────────────────────────────────────── */
.w-section-title {
    padding: 8px 12px 4px;
    font-size: 10px;
    font-weight: 700;
    color: var(--gray-600);
    text-transform: uppercase;
    letter-spacing: .05em;
}

.w-cita-item {
    padding: 8px 12px;
    border-top: 1px solid var(--gray-100);
    display: flex;
    flex-direction: column;
    gap: 4px;
}
.w-cita-fecha {
    font-size: 12px;
    font-weight: 700;
    color: var(--gray-800);
}
.w-cita-info {
    font-size: 11px;
    color: var(--gray-600);
    display: flex;
    align-items: center;
    gap: 4px;
}
.w-cita-estatus {
    display: inline-block;
    font-size: 9px;
    font-weight: 700;
    padding: 1px 6px;
    border-radius: 10px;
    text-transform: uppercase;
}
.est-pendiente  { background: #edf2f7; color: var(--gray-600); }
.est-confirmada { background: #c6f6d5; color: #276749; }
.est-cancelada  { background: #fed7d7; color: #c53030; }
.est-atendida   { background: #e9d8fd; color: #553c9a; }
.est-no_asistio { background: #feebc8; color: #c05621; }

.w-cita-actions {
    display: flex;
    gap: 4px;
    margin-top: 4px;
    flex-wrap: wrap;
}

/* ── Buttons ──────────────────────────────────────────────── */
.w-btn {
    border: none;
    border-radius: 6px;
    font-size: 11px;
    font-weight: 600;
    padding: 5px 10px;
    cursor: pointer;
    transition: all .15s;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    white-space: nowrap;
}
.w-btn:disabled { opacity: .6; cursor: not-allowed; }

.w-btn-primary  { background: var(--primary); color: #fff; }
.w-btn-primary:hover:not(:disabled)  { background: var(--primary-d); }

.w-btn-green    { background: var(--green); color: #fff; }
.w-btn-green:hover:not(:disabled)    { background: var(--green-d); }

.w-btn-red      { background: var(--red); color: #fff; }
.w-btn-red:hover:not(:disabled)      { background: var(--red-d); }

.w-btn-orange   { background: var(--orange); color: #fff; }
.w-btn-orange:hover:not(:disabled)   { background: #c05621; }

.w-btn-ghost {
    background: var(--white);
    color: var(--gray-600);
    border: 1px solid var(--gray-200);
}
.w-btn-ghost:hover { background: var(--gray-100); }

.w-btn-block { width: 100%; justify-content: center; padding: 8px; }
.w-btn-sm    { padding: 4px 8px; font-size: 10px; }

/* ── Acciones de pantalla principal ──────────────────────── */
.w-main-actions {
    padding: 8px 12px 12px;
    display: flex;
    flex-direction: column;
    gap: 6px;
    border-top: 1px solid var(--gray-100);
}

.w-empty-citas {
    padding: 12px;
    text-align: center;
    color: var(--gray-400);
    font-size: 11px;
}

/* ── Formularios ──────────────────────────────────────────── */
.w-form { padding: 12px; display: flex; flex-direction: column; gap: 10px; }

.w-form-row { display: flex; flex-direction: column; gap: 3px; }
.w-label { font-size: 10px; font-weight: 700; color: var(--gray-600); text-transform: uppercase; letter-spacing: .04em; }
.w-input, .w-select {
    border: 1px solid var(--gray-200);
    border-radius: 6px;
    padding: 6px 8px;
    font-size: 12px;
    color: var(--gray-800);
    background: var(--white);
    width: 100%;
    transition: border-color .15s;
}
.w-input:focus, .w-select:focus {
    outline: none;
    border-color: var(--primary);
}
.w-form-actions { display: flex; gap: 6px; }

/* ── Mini Calendario ──────────────────────────────────────── */
.w-cal-wrap {
    border: 1px solid var(--gray-200);
    border-radius: var(--radius);
    overflow: hidden;
    background: var(--white);
}

.w-cal-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 7px 10px;
    background: var(--gray-50);
    border-bottom: 1px solid var(--gray-200);
}
.w-cal-nav {
    background: none; border: none; cursor: pointer;
    color: var(--gray-600); font-size: 14px; padding: 0 4px;
    transition: color .15s;
}
.w-cal-nav:hover { color: var(--primary); }
.w-cal-title { font-size: 12px; font-weight: 700; color: var(--gray-800); }

.w-cal-dow {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    padding: 4px 6px 0;
}
.w-cal-dow span {
    text-align: center;
    font-size: 9px;
    font-weight: 700;
    color: var(--gray-400);
    padding: 2px 0;
}

.w-cal-days {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    padding: 2px 6px 6px;
    gap: 2px;
}
.w-cal-day {
    text-align: center;
    font-size: 11px;
    padding: 4px 2px;
    border-radius: 5px;
    cursor: default;
    color: var(--gray-400);
}
.w-cal-day.disponible {
    color: var(--green);
    font-weight: 600;
    cursor: pointer;
    background: #f0fff4;
}
.w-cal-day.disponible:hover { background: #c6f6d5; }
.w-cal-day.seleccionado { background: var(--primary) !important; color: #fff !important; }
.w-cal-day.lleno { color: var(--red); text-decoration: line-through; }
.w-cal-day.hoy { font-weight: 700; }
.w-cal-day.pasado { color: var(--gray-200); }

/* ── Slots ────────────────────────────────────────────────── */
.w-slots-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 4px;
    padding: 6px;
    max-height: 140px;
    overflow-y: auto;
}
.w-slot {
    padding: 5px 4px;
    border: 1px solid var(--gray-200);
    border-radius: 5px;
    font-size: 11px;
    text-align: center;
    cursor: pointer;
    background: #f0fff4;
    color: #276749;
    font-weight: 500;
    transition: all .15s;
}
.w-slot:hover { background: #c6f6d5; border-color: #68d391; }
.w-slot.ocupado { background: #fff5f5; color: #c53030; border-color: #fed7d7; cursor: not-allowed; }
.w-slot.seleccionado { background: var(--primary); color: #fff; border-color: var(--primary); }

.w-slots-msg { text-align: center; color: var(--gray-400); font-size: 11px; padding: 12px; }

/* ── Toast ────────────────────────────────────────────────── */
.w-toast-wrap {
    position: fixed;
    bottom: 12px; left: 12px; right: 12px;
    z-index: 9999;
    display: flex;
    flex-direction: column;
    gap: 6px;
    pointer-events: none;
}
.w-toast {
    padding: 8px 12px;
    border-radius: var(--radius);
    color: #fff;
    font-size: 12px;
    font-weight: 500;
    box-shadow: 0 4px 12px rgba(0,0,0,.2);
    animation: slideUp .25s ease;
}
.w-toast.ok  { background: var(--green); }
.w-toast.err { background: var(--red); }
@keyframes slideUp { from { opacity:0; transform: translateY(10px); } to { opacity:1; transform: translateY(0); } }

/* ── Divider ──────────────────────────────────────────────── */
.w-divider { height: 1px; background: var(--gray-100); margin: 0 12px; }

/* ── No encontrado ────────────────────────────────────────── */
.w-notfound {
    padding: 14px;
    display: flex;
    flex-direction: column;
    gap: 8px;
}
.w-notfound-info {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 10px;
    background: #fffbeb;
    border: 1px solid #fbd38d;
    border-radius: var(--radius);
}
.w-notfound-icon { font-size: 20px; }
.w-notfound-text { font-size: 11px; color: var(--gray-600); line-height: 1.4; }
.w-notfound-tel { font-weight: 700; color: var(--gray-800); }

/* ── Back button ──────────────────────────────────────────── */
.w-back {
    padding: 6px 12px;
    font-size: 11px;
    color: var(--gray-600);
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    border: none;
    background: none;
    border-bottom: 1px solid var(--gray-100);
    width: 100%;
}
.w-back:hover { color: var(--primary); }

/* ── Link CRM ─────────────────────────────────────────────── */
.w-crm-link {
    display: flex;
    align-items: center;
    gap: 4px;
    font-size: 11px;
    color: var(--primary);
    text-decoration: none;
    padding: 0 12px 10px;
}
.w-crm-link:hover { text-decoration: underline; }
</style>
</head>
<body>

{{-- ── Header siempre visible ─────────────────────────────── --}}
<div class="w-header">
    <span class="w-header-logo">🦶</span>
    <div>
        <div class="w-header-title">Global Feet CRM</div>
    </div>
    <span class="w-header-sub" id="hdr-contact-name"></span>
</div>

{{-- ── Pantalla: Esperando datos de Chatwoot ───────────────── --}}
<div class="w-screen active" id="screen-waiting">
    <div class="w-waiting">
        <div class="w-waiting-icon">💬</div>
        <p>Esperando datos del contacto...<br>Abre una conversación en Chatwoot.</p>
    </div>
</div>

{{-- ── Pantalla: Cargando ───────────────────────────────────── --}}
<div class="w-screen" id="screen-loading">
    <div class="w-spinner">
        <div class="spin"></div>
        Buscando paciente...
    </div>
</div>

{{-- ── Pantalla: Paciente encontrado ───────────────────────── --}}
<div class="w-screen" id="screen-paciente">
    <div class="w-body">
        {{-- Perfil --}}
        <div class="w-card">
            <div class="w-card-head">
                <div class="w-card-head-icon icon-paciente">👤</div>
                <div>
                    <div class="w-card-name" id="pac-nombre">—</div>
                    <div class="w-card-meta" id="pac-meta">—</div>
                </div>
                <span class="w-card-badge badge-paciente">Paciente</span>
            </div>

            {{-- Citas próximas --}}
            <div class="w-section-title">Próximas citas</div>
            <div id="pac-citas-list">
                <div class="w-empty-citas">Sin citas próximas</div>
            </div>

            {{-- Acciones --}}
            <div class="w-main-actions">
                <button class="w-btn w-btn-primary w-btn-block" onclick="mostrarNuevaCita()">
                    📅 Nueva cita
                </button>
            </div>
        </div>

        <a class="w-crm-link" id="pac-crm-link" href="#" target="_blank">
            ↗ Ver expediente completo en CRM
        </a>
    </div>
</div>

{{-- ── Pantalla: Lead encontrado ───────────────────────────── --}}
<div class="w-screen" id="screen-lead">
    <div class="w-body">
        <div class="w-card">
            <div class="w-card-head">
                <div class="w-card-head-icon icon-lead">📋</div>
                <div>
                    <div class="w-card-name" id="lead-nombre">—</div>
                    <div class="w-card-meta" id="lead-meta">—</div>
                </div>
                <span class="w-card-badge badge-lead">Lead</span>
            </div>
            <div class="w-section-title">Próximas citas</div>
            <div id="lead-citas-list">
                <div class="w-empty-citas">Sin citas agendadas</div>
            </div>
            <div class="w-main-actions">
                <button class="w-btn w-btn-primary w-btn-block" onclick="mostrarNuevaCita()">
                    📅 Agendar cita
                </button>
                <button class="w-btn w-btn-ghost w-btn-block" onclick="mostrarCrearPaciente()">
                    👤 Convertir a Paciente
                </button>
            </div>
        </div>
    </div>
</div>

{{-- ── Pantalla: No encontrado ──────────────────────────────── --}}
<div class="w-screen" id="screen-notfound">
    <div class="w-body">
        <div class="w-card">
            <div class="w-notfound">
                <div class="w-notfound-info">
                    <div class="w-notfound-icon">🔍</div>
                    <div class="w-notfound-text">
                        No se encontró ningún paciente o lead con el número
                        <span class="w-notfound-tel" id="nf-tel">—</span>
                    </div>
                </div>
                <button class="w-btn w-btn-orange w-btn-block" onclick="mostrarCrearLead()">
                    ⚡ Crear lead rápido
                </button>
                <button class="w-btn w-btn-primary w-btn-block" onclick="mostrarNuevaCita()">
                    📅 Agendar cita como lead
                </button>
                <button class="w-btn w-btn-ghost w-btn-block" onclick="mostrarCrearPaciente()">
                    👤 Crear como paciente
                </button>
            </div>
        </div>
    </div>
</div>

{{-- ── Pantalla: Nueva cita ─────────────────────────────────── --}}
<div class="w-screen" id="screen-nueva-cita">
    <button class="w-back" onclick="goBack()">← Volver</button>
    <div class="w-body">
        <div class="w-card">
            <div class="w-section-title" style="padding-top:10px">Nueva Cita</div>
            <div class="w-form">
                <div class="w-form-row">
                    <label class="w-label">Especialista *</label>
                    <select class="w-select" id="nc-especialista" onchange="onEspChange()">
                        <option value="">Seleccionar...</option>
                    </select>
                </div>
                <div class="w-form-row">
                    <label class="w-label">Sucursal *</label>
                    <select class="w-select" id="nc-sucursal">
                        <option value="">Seleccionar...</option>
                    </select>
                </div>
                <div class="w-form-row">
                    <label class="w-label">Servicio</label>
                    <select class="w-select" id="nc-servicio">
                        <option value="">Seleccionar...</option>
                    </select>
                </div>
                <div class="w-form-row" id="nc-cal-wrap" style="display:none">
                    <label class="w-label">Fecha disponible *</label>
                    <div class="w-cal-wrap" id="nc-calendario"></div>
                    <input type="hidden" id="nc-fecha">
                </div>
                <div class="w-form-row" id="nc-slots-wrap" style="display:none">
                    <label class="w-label">Horario * — <span id="nc-fecha-label" style="color:var(--primary);font-size:10px"></span></label>
                    <div class="w-cal-wrap">
                        <div class="w-slots-grid" id="nc-slots"></div>
                    </div>
                    <input type="hidden" id="nc-hora-ini">
                    <input type="hidden" id="nc-hora-fin">
                </div>
                <div class="w-form-row">
                    <label class="w-label">Motivo</label>
                    <input class="w-input" type="text" id="nc-motivo" placeholder="Motivo de la consulta...">
                </div>
                <div class="w-form-actions">
                    <button class="w-btn w-btn-ghost" onclick="goBack()" style="flex:1">Cancelar</button>
                    <button class="w-btn w-btn-primary" onclick="guardarCita()" style="flex:2" id="nc-btn-guardar">
                        ✓ Guardar cita
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ── Pantalla: Crear lead rápido ──────────────────────────── --}}
<div class="w-screen" id="screen-crear-lead">
    <button class="w-back" onclick="goBack()">← Volver</button>
    <div class="w-body">
        <div class="w-card">
            <div class="w-section-title" style="padding-top:10px">Crear Lead</div>
            <div class="w-form">
                <div class="w-form-row">
                    <label class="w-label">Nombre *</label>
                    <input class="w-input" type="text" id="cl-nombre" placeholder="Nombre completo">
                </div>
                <div class="w-form-row">
                    <label class="w-label">Teléfono</label>
                    <input class="w-input" type="text" id="cl-telefono" placeholder="+507 6000-0000">
                </div>
                <div class="w-form-row">
                    <label class="w-label">Email</label>
                    <input class="w-input" type="email" id="cl-email" placeholder="correo@ejemplo.com">
                </div>
                <div class="w-form-row">
                    <label class="w-label">Notas</label>
                    <input class="w-input" type="text" id="cl-notas" placeholder="Observaciones...">
                </div>
                <div class="w-form-actions">
                    <button class="w-btn w-btn-ghost" onclick="goBack()" style="flex:1">Cancelar</button>
                    <button class="w-btn w-btn-orange" onclick="guardarLead()" style="flex:2" id="cl-btn">
                        ⚡ Crear lead
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ── Pantalla: Crear paciente (redirige al CRM) ───────────── --}}
<div class="w-screen" id="screen-crear-paciente">
    <button class="w-back" onclick="goBack()">← Volver</button>
    <div class="w-body">
        <div class="w-card">
            <div class="w-section-title" style="padding-top:10px">Crear Paciente</div>
            <div class="w-form">
                <div class="w-form-row">
                    <label class="w-label">Nombre *</label>
                    <input class="w-input" type="text" id="cp-nombre" placeholder="Nombre">
                </div>
                <div class="w-form-row">
                    <label class="w-label">Apellido *</label>
                    <input class="w-input" type="text" id="cp-apellido" placeholder="Apellido">
                </div>
                <div class="w-form-row">
                    <label class="w-label">Tipo de ID *</label>
                    <select class="w-select" id="cp-tipo-id">
                        <option value="Cédula">Cédula</option>
                        <option value="Pasaporte">Pasaporte</option>
                        <option value="RUC">RUC</option>
                        <option value="Otro">Otro</option>
                    </select>
                </div>
                <div class="w-form-row">
                    <label class="w-label">Número de ID *</label>
                    <input class="w-input" type="text" id="cp-identificacion" placeholder="8-123-456">
                </div>
                <div class="w-form-row">
                    <label class="w-label">Teléfono</label>
                    <input class="w-input" type="text" id="cp-contacto" placeholder="+507 6000-0000">
                </div>
                <div class="w-form-row">
                    <label class="w-label">Email</label>
                    <input class="w-input" type="email" id="cp-email">
                </div>
                <div class="w-form-row">
                    <label class="w-label">Género</label>
                    <select class="w-select" id="cp-genero">
                        <option value="">—</option>
                        <option value="masculino">Masculino</option>
                        <option value="femenino">Femenino</option>
                        <option value="otro">Otro</option>
                    </select>
                </div>
                <div class="w-form-actions">
                    <button class="w-btn w-btn-ghost" onclick="goBack()" style="flex:1">Cancelar</button>
                    <button class="w-btn w-btn-primary" onclick="guardarPaciente()" style="flex:2" id="cp-btn">
                        👤 Crear paciente
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ── Toast container ───────────────────────────────────────── --}}
<div class="w-toast-wrap" id="toast-wrap"></div>

<script>
/* ══════════════════════════════════════════════════════════════
   Widget CRM Global Feet — integración Chatwoot Dashboard App
   ══════════════════════════════════════════════════════════════ */

const API_BASE  = '{{ $apiUrl }}';
const API_TOKEN = '{{ $token }}';
const CRM_BASE  = '{{ $crmUrl }}';

const MESES = ['Enero','Febrero','Marzo','Abril','Mayo','Junio',
               'Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];
const DIAS  = ['Do','Lu','Ma','Mi','Ju','Vi','Sá'];

// ── Estado global ─────────────────────────────────────────────────────────────
let state = {
    contact:     null,   // { id, name, phone_number, email, chatwoot_conv_id }
    paciente:    null,   // { id, nombre, telefono, ... }
    lead:        null,   // { id, nombre, telefono, ... }
    citas:       [],
    prevScreen:  'screen-waiting',
    // catálogos
    especialistas: [],
    servicios:     [],
    sucursales:    [],
    // nueva cita — calendario
    calEsp:      null,
    calMes:      new Date(),
    calDisponib: {},
    calFecha:    null,
    calSlot:     null,
};

// ── API helper ────────────────────────────────────────────────────────────────
async function api(method, path, body) {
    const res = await fetch(API_BASE + path, {
        method,
        headers: {
            'Authorization': 'Bearer ' + API_TOKEN,
            'Content-Type':  'application/json',
            'Accept':        'application/json',
        },
        body: body ? JSON.stringify(body) : undefined,
    });
    const json = await res.json();
    if (!res.ok) throw json;
    return json;
}

// ── Pantallas ─────────────────────────────────────────────────────────────────
function showScreen(id) {
    document.querySelectorAll('.w-screen').forEach(s => s.classList.remove('active'));
    const el = document.getElementById(id);
    if (el) el.classList.add('active');
}

function goBack() {
    showScreen(state.prevScreen);
}

// ── Chatwoot postMessage ──────────────────────────────────────────────────────
window.addEventListener('message', function(e) {
    if (!e.data || e.data.event !== 'appContext') return;
    const { contact, conversation } = e.data.data || {};
    if (!contact) return;

    state.contact = {
        id:              contact.id,
        name:            contact.name,
        phone_number:    contact.phone_number,
        email:           contact.email,
        chatwoot_conv_id: conversation?.id,
    };

    document.getElementById('hdr-contact-name').textContent = contact.name || '';
    buscarContacto();
});

// ── Buscar contacto por teléfono ──────────────────────────────────────────────
async function buscarContacto() {
    const tel = state.contact?.phone_number;
    showScreen('screen-loading');

    try {
        const res = await api('GET', '/pacientes/buscar-por-telefono?tel=' + encodeURIComponent(tel || ''));

        if (res.encontrado) {
            state.paciente = res.paciente;
            state.lead     = null;
            state.citas    = res.citas || [];
            renderPaciente();
            showScreen('screen-paciente');
            state.prevScreen = 'screen-paciente';
        } else if (res.lead) {
            state.lead     = res.lead;
            state.paciente = null;
            state.citas    = [];
            renderLead();
            showScreen('screen-lead');
            state.prevScreen = 'screen-lead';
        } else {
            state.paciente = null;
            state.lead     = null;
            state.citas    = [];
            document.getElementById('nf-tel').textContent = tel || '—';
            showScreen('screen-notfound');
            state.prevScreen = 'screen-notfound';
        }
    } catch (err) {
        toast('Error al buscar el contacto.', 'err');
        showScreen('screen-notfound');
    }
}

// ── Render paciente ───────────────────────────────────────────────────────────
function renderPaciente() {
    const p = state.paciente;
    document.getElementById('pac-nombre').textContent = p.nombre;
    document.getElementById('pac-meta').textContent   = [p.telefono, p.email].filter(Boolean).join(' · ') || '—';

    const link = document.getElementById('pac-crm-link');
    link.href = CRM_BASE + 'pacientes/' + p.id;

    renderCitas('pac-citas-list', state.citas, true);
}

// ── Render lead ───────────────────────────────────────────────────────────────
function renderLead() {
    const l = state.lead;
    document.getElementById('lead-nombre').textContent = l.nombre;
    document.getElementById('lead-meta').textContent   = [l.telefono, l.email].filter(Boolean).join(' · ') || '—';
    renderCitas('lead-citas-list', state.citas, false);
}

// ── Render lista de citas ─────────────────────────────────────────────────────
function renderCitas(containerId, citas, conAcciones) {
    const container = document.getElementById(containerId);
    if (!citas.length) {
        container.innerHTML = '<div class="w-empty-citas">Sin citas próximas</div>';
        return;
    }

    container.innerHTML = citas.map(c => {
        const fechaLabel = formatFecha(c.fecha);
        const horario    = c.hora_inicio + ' – ' + c.hora_fin;
        const clsEst     = 'est-' + (c.estatus || 'pendiente');
        const acciones   = conAcciones ? `
            <div class="w-cita-actions">
                ${c.estatus === 'pendiente' ? `<button class="w-btn w-btn-green w-btn-sm" onclick="confirmarCita(${c.id})">✓ Confirmar</button>` : ''}
                ${c.estatus !== 'cancelada' ? `<button class="w-btn w-btn-red w-btn-sm" onclick="cancelarCita(${c.id})">✗ Cancelar</button>` : ''}
                <button class="w-btn w-btn-ghost w-btn-sm" onclick="enviarRecordatorio(${c.id})">📤 Recordar</button>
            </div>` : '';

        return `<div class="w-cita-item" id="cita-row-${c.id}">
            <div style="display:flex;align-items:center;gap:6px">
                <span class="w-cita-fecha">${fechaLabel}</span>
                <span class="w-cita-estatus ${clsEst}">${labelEstatus(c.estatus)}</span>
            </div>
            <div class="w-cita-info">🕐 ${horario}</div>
            <div class="w-cita-info">👨‍⚕️ ${esc(c.especialista || '—')}</div>
            ${c.servicio ? `<div class="w-cita-info">📋 ${esc(c.servicio)}</div>` : ''}
            ${acciones}
        </div>`;
    }).join('');
}

// ── Acciones sobre citas ──────────────────────────────────────────────────────
async function confirmarCita(id) {
    try {
        await api('PATCH', `/citas/${id}/estatus`, { estatus: 'confirmada' });
        toast('Cita confirmada ✓');
        actualizarFilaCita(id, 'confirmada');
    } catch {
        toast('Error al confirmar la cita.', 'err');
    }
}

async function cancelarCita(id) {
    if (!confirm('¿Cancelar esta cita?')) return;
    try {
        await api('PATCH', `/citas/${id}/estatus`, { estatus: 'cancelada' });
        toast('Cita cancelada.');
        const row = document.getElementById('cita-row-' + id);
        if (row) row.remove();
    } catch {
        toast('Error al cancelar la cita.', 'err');
    }
}

async function enviarRecordatorio(id) {
    const cita = state.citas.find(c => c.id === id);
    if (!cita) return;

    const msg = formatRecordatorio(cita);

    // Enviar mensaje via postMessage a Chatwoot para que el agente lo revise
    // (La implementación completa del envío directo es Fase 6)
    // Por ahora: copia al portapapeles y notifica
    try {
        await navigator.clipboard.writeText(msg);
        toast('Recordatorio copiado. Pégalo en el chat.');
    } catch {
        // Fallback: mostrar en prompt
        prompt('Copia este mensaje:', msg);
    }
}

function actualizarFilaCita(id, nuevoEstatus) {
    const cita = state.citas.find(c => c.id === id);
    if (cita) cita.estatus = nuevoEstatus;

    const row = document.getElementById('cita-row-' + id);
    if (!row) return;

    const badge = row.querySelector('.w-cita-estatus');
    if (badge) {
        badge.className = 'w-cita-estatus est-' + nuevoEstatus;
        badge.textContent = labelEstatus(nuevoEstatus);
    }

    // Quitar botón confirmar
    const btnConf = row.querySelector('[onclick^="confirmarCita"]');
    if (btnConf) btnConf.remove();
}

// ── Nueva cita ────────────────────────────────────────────────────────────────
async function mostrarNuevaCita() {
    state.prevScreen = obtenerScreenActivo();
    showScreen('screen-nueva-cita');
    resetNuevaCita();

    if (!state.especialistas.length) {
        await cargarCatalogos();
    }
    llenarSelects();
}

function resetNuevaCita() {
    state.calEsp      = null;
    state.calMes      = new Date();
    state.calDisponib = {};
    state.calFecha    = null;
    state.calSlot     = null;
    document.getElementById('nc-especialista').value = '';
    document.getElementById('nc-sucursal').value     = '';
    document.getElementById('nc-servicio').value     = '';
    document.getElementById('nc-motivo').value       = '';
    document.getElementById('nc-fecha').value        = '';
    document.getElementById('nc-hora-ini').value     = '';
    document.getElementById('nc-hora-fin').value     = '';
    document.getElementById('nc-cal-wrap').style.display   = 'none';
    document.getElementById('nc-slots-wrap').style.display = 'none';
    document.getElementById('nc-slots').innerHTML    = '';
    document.getElementById('nc-calendario').innerHTML = '<div class="w-slots-msg">Selecciona un especialista.</div>';
}

async function cargarCatalogos() {
    try {
        const [esps, servs, sucs] = await Promise.all([
            api('GET', '/especialistas'),
            api('GET', '/servicios'),
            api('GET', '/sucursales'),
        ]);
        state.especialistas = esps;
        state.servicios     = servs;
        state.sucursales    = sucs;
    } catch {
        toast('Error al cargar catálogos.', 'err');
    }
}

function llenarSelects() {
    const espSel  = document.getElementById('nc-especialista');
    const servSel = document.getElementById('nc-servicio');
    const sucSel  = document.getElementById('nc-sucursal');

    espSel.innerHTML = '<option value="">Seleccionar...</option>' +
        state.especialistas.map(e => `<option value="${e.id}">${esc(e.nombre)}</option>`).join('');

    servSel.innerHTML = '<option value="">— Ninguno —</option>' +
        state.servicios.map(s => `<option value="${s.id}">${esc(s.nombre)}</option>`).join('');

    sucSel.innerHTML = '<option value="">Seleccionar...</option>' +
        state.sucursales.map(s => `<option value="${s.id}">${esc(s.nombre)}</option>`).join('');
}

async function onEspChange() {
    const espId = document.getElementById('nc-especialista').value;
    if (!espId) {
        document.getElementById('nc-cal-wrap').style.display   = 'none';
        document.getElementById('nc-slots-wrap').style.display = 'none';
        return;
    }

    state.calEsp   = parseInt(espId);
    state.calFecha = null;
    state.calSlot  = null;
    state.calMes   = new Date();
    state.calMes.setDate(1);
    document.getElementById('nc-cal-wrap').style.display   = '';
    document.getElementById('nc-slots-wrap').style.display = 'none';
    document.getElementById('nc-hora-ini').value = '';
    document.getElementById('nc-hora-fin').value = '';

    await cargarCalMes();
}

async function cargarCalMes() {
    const calEl = document.getElementById('nc-calendario');
    calEl.innerHTML = '<div class="w-slots-msg">Cargando...</div>';

    const mes = state.calMes.getFullYear() + '-' +
                String(state.calMes.getMonth() + 1).padStart(2, '0');
    try {
        state.calDisponib = await api('GET', `/especialistas/${state.calEsp}/disponibilidad?mes=${mes}`);
        renderCalendario();
    } catch {
        calEl.innerHTML = '<div class="w-slots-msg" style="color:var(--red)">Error al cargar.</div>';
    }
}

function renderCalendario() {
    const calEl = document.getElementById('nc-calendario');
    const year  = state.calMes.getFullYear();
    const month = state.calMes.getMonth();
    const titulo = MESES[month] + ' ' + year;
    const hoy    = new Date();
    const hoyStr = `${hoy.getFullYear()}-${String(hoy.getMonth()+1).padStart(2,'0')}-${String(hoy.getDate()).padStart(2,'0')}`;
    const primerDia = new Date(year, month, 1);
    const startCol  = primerDia.getDay(); // 0=Dom
    const diasEnMes = new Date(year, month + 1, 0).getDate();

    let html = `
        <div class="w-cal-header">
            <button class="w-cal-nav" id="cal-prev" type="button">‹</button>
            <span class="w-cal-title">${titulo}</span>
            <button class="w-cal-nav" id="cal-next" type="button">›</button>
        </div>
        <div class="w-cal-dow">
            ${DIAS.map(d => `<span>${d}</span>`).join('')}
        </div>
        <div class="w-cal-days">`;

    for (let i = 0; i < startCol; i++) html += `<div class="w-cal-day"></div>`;

    for (let d = 1; d <= diasEnMes; d++) {
        const fechaStr = `${year}-${String(month+1).padStart(2,'0')}-${String(d).padStart(2,'0')}`;
        const estado   = state.calDisponib[fechaStr] || 'sin_horario';
        const esHoy    = fechaStr === hoyStr;
        const esSel    = fechaStr === state.calFecha;
        const esPasado = new Date(year, month, d) < new Date(hoy.getFullYear(), hoy.getMonth(), hoy.getDate());

        let cls = 'w-cal-day';
        if (esPasado || estado === 'sin_horario') cls += ' pasado';
        else if (estado === 'lleno')              cls += ' lleno';
        else                                      cls += ' disponible';
        if (esHoy) cls += ' hoy';
        if (esSel) cls += ' seleccionado';

        html += `<div class="${cls}" data-fecha="${fechaStr}">${d}</div>`;
    }

    html += '</div>';
    calEl.innerHTML = html;

    document.getElementById('cal-prev')?.addEventListener('click', () => {
        state.calMes.setMonth(state.calMes.getMonth() - 1);
        cargarCalMes();
    });
    document.getElementById('cal-next')?.addEventListener('click', () => {
        state.calMes.setMonth(state.calMes.getMonth() + 1);
        cargarCalMes();
    });

    calEl.querySelectorAll('.w-cal-day.disponible').forEach(el => {
        el.addEventListener('click', () => seleccionarFecha(el.dataset.fecha));
    });
}

async function seleccionarFecha(fecha) {
    state.calFecha = fecha;
    state.calSlot  = null;
    document.getElementById('nc-fecha').value    = fecha;
    document.getElementById('nc-hora-ini').value = '';
    document.getElementById('nc-hora-fin').value = '';

    // Resaltar día
    document.querySelectorAll('#nc-calendario .w-cal-day').forEach(el => {
        el.classList.remove('seleccionado');
        if (el.dataset.fecha === fecha) el.classList.add('seleccionado');
    });

    // Mostrar slots
    document.getElementById('nc-slots-wrap').style.display = '';
    const [y, m, d] = fecha.split('-');
    const dt = new Date(parseInt(y), parseInt(m)-1, parseInt(d));
    document.getElementById('nc-fecha-label').textContent =
        DIAS[dt.getDay()] + ' ' + parseInt(d) + ' ' + MESES[dt.getMonth()] + ' ' + y;

    const slotsEl = document.getElementById('nc-slots');
    slotsEl.innerHTML = '<div class="w-slots-msg">Cargando horarios...</div>';

    try {
        const slots = await api('GET', `/especialistas/${state.calEsp}/horas-disponibles?fecha=${fecha}`);
        renderSlots(slots);
    } catch {
        slotsEl.innerHTML = '<div class="w-slots-msg" style="color:var(--red)">Error.</div>';
    }
}

function renderSlots(slots) {
    const slotsEl = document.getElementById('nc-slots');
    if (!slots.length) {
        slotsEl.innerHTML = '<div class="w-slots-msg">Sin horarios disponibles.</div>';
        return;
    }
    slotsEl.innerHTML = slots.map(s =>
        `<button type="button" class="w-slot${s.disponible ? '' : ' ocupado'}" data-hi="${s.hora_inicio}" data-hf="${s.hora_fin}">
            ${s.hora_inicio} – ${s.hora_fin}
        </button>`
    ).join('');

    slotsEl.querySelectorAll('.w-slot:not(.ocupado)').forEach(btn => {
        btn.addEventListener('click', () => {
            slotsEl.querySelectorAll('.w-slot').forEach(b => b.classList.remove('seleccionado'));
            btn.classList.add('seleccionado');
            state.calSlot = { hi: btn.dataset.hi, hf: btn.dataset.hf };
            document.getElementById('nc-hora-ini').value = btn.dataset.hi;
            document.getElementById('nc-hora-fin').value = btn.dataset.hf;
        });
    });
}

async function guardarCita() {
    const espId = document.getElementById('nc-especialista').value;
    const sucId = document.getElementById('nc-sucursal').value;
    const fecha = document.getElementById('nc-fecha').value;
    const hi    = document.getElementById('nc-hora-ini').value;
    const hf    = document.getElementById('nc-hora-fin').value;

    if (!espId || !sucId)    { toast('Selecciona especialista y sucursal.', 'err'); return; }
    if (!fecha || !hi || !hf){ toast('Selecciona una fecha y horario.', 'err'); return; }

    const payload = {
        especialista_id: parseInt(espId),
        sucursal_id:     parseInt(sucId),
        servicio_id:     parseInt(document.getElementById('nc-servicio').value) || null,
        fecha, hora_inicio: hi, hora_fin: hf,
        motivo: document.getElementById('nc-motivo').value || null,
        origen: 'chatwoot',
    };

    if (state.paciente) {
        payload.paciente_id = state.paciente.id;
    } else {
        payload.nombre_lead   = (state.lead?.nombre || state.contact?.name || 'Lead Chatwoot');
        payload.telefono_lead = (state.lead?.telefono || state.contact?.phone_number || null);
    }

    const btn = document.getElementById('nc-btn-guardar');
    btn.disabled = true;
    btn.textContent = 'Guardando...';

    try {
        const res = await api('POST', '/citas', payload);
        toast('Cita agendada correctamente ✓');
        // Agregar a lista y volver
        state.citas.unshift(res.cita);
        setTimeout(() => {
            goBack();
            if (state.paciente) renderPaciente();
            else if (state.lead) renderLead();
        }, 600);
    } catch (err) {
        toast((err.message || err.error || 'Error al guardar la cita.'), 'err');
    } finally {
        btn.disabled = false;
        btn.textContent = '✓ Guardar cita';
    }
}

// ── Crear lead ────────────────────────────────────────────────────────────────
function mostrarCrearLead() {
    state.prevScreen = obtenerScreenActivo();
    document.getElementById('cl-nombre').value    = state.contact?.name || '';
    document.getElementById('cl-telefono').value  = state.contact?.phone_number || '';
    document.getElementById('cl-email').value     = state.contact?.email || '';
    document.getElementById('cl-notas').value     = '';
    showScreen('screen-crear-lead');
}

async function guardarLead() {
    const nombre = document.getElementById('cl-nombre').value.trim();
    if (!nombre) { toast('El nombre es requerido.', 'err'); return; }

    const btn = document.getElementById('cl-btn');
    btn.disabled = true;
    btn.textContent = 'Guardando...';

    try {
        const res = await api('POST', '/leads', {
            nombre,
            telefono:            document.getElementById('cl-telefono').value.trim() || null,
            email:               document.getElementById('cl-email').value.trim()    || null,
            notas:               document.getElementById('cl-notas').value.trim()    || null,
            origen:              'chatwoot',
            chatwoot_contact_id: state.contact?.id    || null,
            chatwoot_conv_id:    state.contact?.chatwoot_conv_id || null,
        });

        toast('Lead creado correctamente ✓');
        state.lead = res.lead;
        state.citas = [];
        setTimeout(() => {
            renderLead();
            showScreen('screen-lead');
            state.prevScreen = 'screen-lead';
        }, 500);
    } catch {
        toast('Error al crear el lead.', 'err');
    } finally {
        btn.disabled = false;
        btn.textContent = '⚡ Crear lead';
    }
}

// ── Crear paciente ────────────────────────────────────────────────────────────
function mostrarCrearPaciente() {
    state.prevScreen = obtenerScreenActivo();
    // Pre-llenar con datos del contacto
    const n = (state.contact?.name || '').split(' ');
    document.getElementById('cp-nombre').value    = n[0] || '';
    document.getElementById('cp-apellido').value  = n.slice(1).join(' ') || '';
    document.getElementById('cp-contacto').value  = state.contact?.phone_number || '';
    document.getElementById('cp-email').value     = state.contact?.email || '';
    document.getElementById('cp-identificacion').value = '';
    showScreen('screen-crear-paciente');
}

async function guardarPaciente() {
    const nombre = document.getElementById('cp-nombre').value.trim();
    const apell  = document.getElementById('cp-apellido').value.trim();
    const id_val = document.getElementById('cp-identificacion').value.trim();
    const tipoId = document.getElementById('cp-tipo-id').value;

    if (!nombre || !apell)  { toast('Nombre y apellido son requeridos.', 'err'); return; }
    if (!id_val)             { toast('El número de identificación es requerido.', 'err'); return; }

    const btn = document.getElementById('cp-btn');
    btn.disabled = true;
    btn.textContent = 'Guardando...';

    try {
        const res = await api('POST', '/pacientes', {
            nombre, apellido: apell,
            tipo_identificacion: tipoId,
            identificacion:      id_val,
            contacto:            document.getElementById('cp-contacto').value.trim() || null,
            email:               document.getElementById('cp-email').value.trim()    || null,
            genero:              document.getElementById('cp-genero').value           || null,
        });

        toast('Paciente creado correctamente ✓');
        state.paciente = res.paciente;
        state.lead     = null;
        state.citas    = [];

        setTimeout(() => {
            renderPaciente();
            showScreen('screen-paciente');
            state.prevScreen = 'screen-paciente';
        }, 500);
    } catch (err) {
        const msg = err?.errors?.identificacion?.[0] || err?.message || 'Error al crear paciente.';
        toast(msg, 'err');
    } finally {
        btn.disabled = false;
        btn.textContent = '👤 Crear paciente';
    }
}

// ── Formato de recordatorio ────────────────────────────────────────────────────
function formatRecordatorio(cita) {
    const nombre = state.paciente?.nombre || state.lead?.nombre || state.contact?.name || 'Estimado/a';
    const fecha  = formatFecha(cita.fecha);
    return `Hola ${nombre.split(' ')[0]} 👋\n\nTe recordamos tu cita en Global Feet Panama:\n\n📅 ${fecha}\n⏰ ${cita.hora_inicio} – ${cita.hora_fin}\n👨‍⚕️ ${cita.especialista || ''}\n${cita.servicio ? '📋 ' + cita.servicio + '\n' : ''}${cita.sucursal ? '📍 ' + cita.sucursal + '\n' : ''}\n¿Necesitas confirmar o reagendar? Escríbenos aquí mismo. 😊`;
}

// ── Helpers ───────────────────────────────────────────────────────────────────
function obtenerScreenActivo() {
    const el = document.querySelector('.w-screen.active');
    return el ? el.id : 'screen-waiting';
}

function formatFecha(fechaStr) {
    if (!fechaStr) return '—';
    const [y, m, d] = fechaStr.split('-');
    const dt = new Date(parseInt(y), parseInt(m)-1, parseInt(d));
    return DIAS[dt.getDay()] + ' ' + parseInt(d) + ' ' + MESES[dt.getMonth()] + ' ' + y;
}

function labelEstatus(e) {
    return { pendiente:'Pendiente', confirmada:'Confirmada', atendida:'Atendida',
             cancelada:'Cancelada', no_asistio:'No asistió' }[e] || e;
}

function esc(s) {
    return String(s ?? '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
}

function toast(msg, tipo = 'ok') {
    const wrap = document.getElementById('toast-wrap');
    const el   = document.createElement('div');
    el.className = 'w-toast ' + tipo;
    el.textContent = msg;
    wrap.appendChild(el);
    setTimeout(() => el.remove(), 3500);
}

// ── Init: detectar si Chatwoot ya envió datos ─────────────────────────────────
// (Para testing directo con parámetro en URL)
const urlParams = new URLSearchParams(location.search);
const testTel   = urlParams.get('tel');
if (testTel) {
    state.contact = { id: 0, name: 'Test', phone_number: testTel, email: '' };
    document.getElementById('hdr-contact-name').textContent = 'Test: ' + testTel;
    buscarContacto();
}
</script>
</body>
</html>
