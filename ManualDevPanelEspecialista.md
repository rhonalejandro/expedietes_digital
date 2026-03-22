# Manual de Desarrollo — Panel Especialista
> Guía de referencia para programar cualquier módulo dentro del Panel Clínico del Especialista.
> Seguir esta guía garantiza consistencia, mantenibilidad y separación total del panel admin.

---

## 1. Principio fundamental

El Panel Especialista es **completamente independiente** del panel admin:

| Aspecto | Panel Admin | Panel Especialista |
|---------|------------|-------------------|
| Guard | `web` (tabla `usuarios`) | `especialista` (tabla `especialistas`) |
| Layout | `layouts.admin.master` | `panel_especialista.layouts.master` |
| Rutas prefix | `/` | `/panel` |
| Route names | `pacientes.*`, `citas.*`… | `panel.*` |
| Controladores | `app/Http/Controllers/` | `app/Http/Controllers/PanelEspecialista/` |
| Vistas | `resources/views/modules/` | `resources/views/panel_especialista/` |
| Assets CSS/JS | `public/assets/modules/` | `public/assets/panel_especialista/` |

**Nunca mezclar** vistas, assets o controladores entre ambos paneles.

---

## 2. Estructura de archivos de un módulo

Ejemplo: módulo `expedientes`

```
── Controlador
   app/Http/Controllers/PanelEspecialista/
   └── ExpedienteController.php

── Rutas
   routes/web_routes/panel_especialista/
   └── panelespecialista_routes.php     ← todas las rutas del panel van aquí

── Vistas
   resources/views/panel_especialista/
   └── expediente/
       ├── index.blade.php              ← listado / vista principal
       ├── show.blade.php               ← detalle
       └── _partials/
           ├── card.blade.php           ← tarjeta individual
           ├── toolbar.blade.php        ← barra de acciones / filtros
           └── modal-crear.blade.php    ← modal si aplica

── Assets
   public/assets/panel_especialista/
   ├── css/
   │   └── panel.css                   ← CSS global del panel (ya existe)
   │   └── expediente.css              ← CSS específico del módulo (si necesita)
   └── js/
       └── expediente.js               ← JS específico del módulo (si necesita)
```

---

## 3. Cómo agregar la ruta de un módulo nuevo

**Archivo:** `routes/web_routes/panel_especialista/panelespecialista_routes.php`

```php
Route::middleware(['auth:especialista'])
    ->prefix('panel')
    ->name('panel.')
    ->group(function () {

        // Agenda (ya existe)
        Route::get('/agenda', [AgendaController::class, 'index'])->name('agenda');

        // ── Nuevo módulo: Expedientes ──────────────────────────────
        Route::get('/expedientes',               [ExpedienteController::class, 'index'])->name('expedientes.index');
        Route::get('/expedientes/{id}',          [ExpedienteController::class, 'show'])->name('expedientes.show');
        Route::post('/expedientes',              [ExpedienteController::class, 'store'])->name('expedientes.store');

        // ── Nuevo módulo: Casos ────────────────────────────────────
        Route::get('/casos/{id}',                [CasoController::class, 'show'])->name('casos.show');
        Route::post('/casos',                    [CasoController::class, 'store'])->name('casos.store');

    });
```

**Reglas:**
- Siempre dentro del grupo `auth:especialista`
- Siempre prefix `panel`, name `panel.`
- Verbos REST estándar: GET index/show, POST store, PUT update, DELETE destroy

---

## 4. Cómo crear un Controlador

**Archivo:** `app/Http/Controllers/PanelEspecialista/ExpedienteController.php`

```php
<?php

namespace App\Http\Controllers\PanelEspecialista;

use App\Http\Controllers\Controller;
use App\Models\Expediente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExpedienteController extends Controller
{
    // Siempre obtener el especialista autenticado así:
    private function especialista()
    {
        return Auth::guard('especialista')->user();
    }

    public function index()
    {
        $especialista = $this->especialista();

        // Lógica del módulo...
        $expedientes = Expediente::where('especialista_id', $especialista->id)
            ->with(['paciente.persona'])
            ->latest()
            ->get();

        return view('panel_especialista.expediente.index', compact('especialista', 'expedientes'));
    }

    public function show(int $id)
    {
        $especialista = $this->especialista();

        $expediente = Expediente::with(['paciente.persona', 'casos.consultas'])
            ->findOrFail($id);

        return view('panel_especialista.expediente.show', compact('especialista', 'expediente'));
    }

    public function store(Request $request)
    {
        $especialista = $this->especialista();

        $data = $request->validate([
            'paciente_id'  => 'required|exists:pacientes,id',
            'antecedentes' => 'nullable|string|max:2000',
            'alergias'     => 'nullable|string|max:500',
        ]);

        $expediente = Expediente::create([
            ...$data,
            'especialista_id' => $especialista->id,
        ]);

        // Respuesta JSON si es petición AJAX
        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'expediente' => $expediente]);
        }

        return redirect()->route('panel.expedientes.show', $expediente->id)
            ->with('success', 'Expediente creado correctamente.');
    }
}
```

**Reglas del controlador:**
- Namespace siempre `App\Http\Controllers\PanelEspecialista`
- Obtener el doctor con `Auth::guard('especialista')->user()` — NUNCA `Auth::user()`
- Métodos pequeños, máximo 30 líneas
- Validar siempre con `$request->validate()`
- Soportar JSON (`expectsJson()`) para peticiones AJAX del módulo

---

## 5. Cómo crear una Vista

### 5.1 Vista principal — extiende el layout del panel

```blade
{{-- resources/views/panel_especialista/expediente/index.blade.php --}}

@extends('panel_especialista.layouts.master')

@section('title', 'Expedientes')
@section('page-title', 'Expedientes Clínicos')

@push('styles')
    {{-- CSS específico del módulo (si existe) --}}
    <link rel="stylesheet" href="{{ asset('assets/panel_especialista/css/expediente.css') }}?v={{ time() }}">
@endpush

@section('content')
<div class="pnl-modulo-wrap">

    @include('panel_especialista.expediente._partials.toolbar')

    <div class="pnl-modulo-body">
        @forelse($expedientes as $exp)
            @include('panel_especialista.expediente._partials.card', ['exp' => $exp])
        @empty
            @include('panel_especialista._partials.empty', ['mensaje' => 'Sin expedientes registrados.'])
        @endforelse
    </div>

</div>
@endsection

@push('scripts')
    {{-- JS específico del módulo (si existe) --}}
    <script src="{{ asset('assets/panel_especialista/js/expediente.js') }}?v={{ time() }}"></script>
@endpush
```

### 5.2 Partial — toolbar

```blade
{{-- resources/views/panel_especialista/expediente/_partials/toolbar.blade.php --}}

<div class="pnl-toolbar">
    <div class="pnl-toolbar-left">
        <h5 class="pnl-toolbar-title">
            <i class="ti ti-folder-open"></i>
            Mis expedientes
        </h5>
    </div>
    <div class="pnl-toolbar-right">
        <button class="pnl-btn pnl-btn--primary" data-bs-toggle="modal" data-bs-target="#modal-crear-exp">
            <i class="ti ti-plus"></i> Nuevo expediente
        </button>
    </div>
</div>
```

### 5.3 Partial reutilizable — empty state

```blade
{{-- resources/views/panel_especialista/_partials/empty.blade.php --}}
<div class="pnl-empty">
    <i class="ti ti-inbox"></i>
    <p>{{ $mensaje ?? 'Sin datos registrados.' }}</p>
</div>
```

---

## 6. Agregar el ítem al Sidebar

**Archivo:** `resources/views/panel_especialista/layouts/sidebar.blade.php`

```blade
<nav class="pnl-nav">

    <a href="{{ route('panel.agenda') }}"
       class="pnl-nav-item {{ request()->routeIs('panel.agenda') ? 'active' : '' }}">
        <i class="ti ti-calendar-event"></i>
        <span>Mi Agenda</span>
    </a>

    {{-- ── Nuevo módulo ──────────────────────── --}}
    <a href="{{ route('panel.expedientes.index') }}"
       class="pnl-nav-item {{ request()->routeIs('panel.expedientes.*') ? 'active' : '' }}">
        <i class="ti ti-folder-open"></i>
        <span>Expedientes</span>
    </a>

</nav>
```

**Regla:** usar `routeIs('panel.modulo.*')` para que todos los sub-niveles del módulo marquen el ítem como activo.

---

## 7. CSS — cómo y dónde escribirlo

### 7.1 CSS global del panel
**Archivo:** `public/assets/panel_especialista/css/panel.css`

Contiene: layout (sidebar, header, main), clases base (`pnl-*`), estados, responsive.

**Clases base disponibles (ya definidas):**

| Clase | Uso |
|-------|-----|
| `pnl-btn pnl-btn--primary` | Botón primario del panel |
| `pnl-badge` | Badge de estado con color |
| `pnl-empty` | Estado vacío (icono + mensaje) |
| `pnl-alert--success / --danger` | Alertas de sesión |
| `pnl-stat-card` | Tarjeta de estadística |
| `pnl-toolbar` | Barra de acciones (título + botones) |

### 7.2 CSS específico de un módulo

Crear **solo si el módulo tiene estilos que no aplican globalmente**.

**Archivo:** `public/assets/panel_especialista/css/{modulo}.css`

```css
/* expediente.css — solo estilos exclusivos de expedientes */

.exp-card { ... }
.exp-caso-timeline { ... }
.exp-foto-grid { ... }
```

Cargar en la vista con `@push('styles')`:
```blade
@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/panel_especialista/css/expediente.css') }}?v={{ time() }}">
@endpush
```

**Reglas CSS:**
- Prefijo de clase = abreviatura del módulo: `exp-`, `cas-`, `con-` (consulta), `rec-` (receta)
- No usar estilos inline en las vistas, todo va al CSS
- Nunca usar `!important` salvo que sea estrictamente necesario para override de Bootstrap
- Mobile-first: diseñar para tablet/móvil (el doctor puede usar el panel desde el teléfono)

---

## 8. JavaScript — cómo y dónde escribirlo

### 8.1 JS simple (sin módulo IIFE)

Para lógica pequeña (toggle, modal, submit AJAX simple):

```blade
{{-- Al final de la vista, dentro de @push('scripts') --}}
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    // Lógica específica de esta vista
    document.getElementById('btn-crear').addEventListener('click', function () {
        // ...
    });

});
</script>
@endpush
```

### 8.2 JS de módulo (IIFE — para lógica compleja)

Si el módulo tiene lógica compleja (formularios dinámicos, AJAX múltiple, estado interno):

**Archivo:** `public/assets/panel_especialista/js/{modulo}.js`

```js
/**
 * ExpedientePanel — Módulo JS para gestión de expedientes
 * Panel Especialista
 */
const ExpedientePanel = (function () {

    'use strict';

    // ── Estado interno ────────────────────────────────────────
    let _csrf    = '';
    let _baseUrl = '';

    // ── Init ──────────────────────────────────────────────────
    function init(cfg) {
        _csrf    = cfg.csrf;
        _baseUrl = cfg.baseUrl;

        _initModales();
        _initForms();
    }

    // ── Privados ──────────────────────────────────────────────
    function _initModales() {
        // ...
    }

    function _initForms() {
        const form = document.getElementById('form-crear-exp');
        if (!form) return;

        form.addEventListener('submit', function (e) {
            e.preventDefault();
            _guardar(new FormData(form));
        });
    }

    function _guardar(formData) {
        fetch(_baseUrl + '/store', {
            method:  'POST',
            headers: { 'X-CSRF-TOKEN': _csrf, 'Accept': 'application/json' },
            body:    formData,
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) _toast('Guardado correctamente.', 'success');
        })
        .catch(() => _toast('Error al guardar.', 'danger'));
    }

    function _toast(msg, tipo) {
        // Reutilizar el sistema de toasts del panel si existe
        // o crear uno temporal
        console.log(`[${tipo}] ${msg}`);
    }

    // ── API pública ───────────────────────────────────────────
    return { init };

})();
```

Inicializar en la vista:

```blade
@push('scripts')
    <script src="{{ asset('assets/panel_especialista/js/expediente.js') }}?v={{ time() }}"></script>
    <script>
        ExpedientePanel.init({
            csrf:    '{{ csrf_token() }}',
            baseUrl: '{{ url("panel/expedientes") }}',
        });
    </script>
@endpush
```

**Reglas JS:**
- Un IIFE por módulo complejo, nombre = `{Modulo}Panel` (ej: `ExpedientePanel`, `ConsultaPanel`)
- Estado interno con variables prefijadas `_` (privadas)
- Siempre `'use strict'`
- `init(cfg)` recibe toda la config desde Blade (csrf, urls, ids, modo)
- Nunca hardcodear URLs — siempre vienen del config
- Peticiones AJAX siempre con `Accept: application/json` y `X-CSRF-TOKEN`

---

## 9. Modales

Usar Bootstrap 5 modal estándar. Estructura siempre igual:

```blade
{{-- resources/views/panel_especialista/expediente/_partials/modal-crear.blade.php --}}

<div class="modal fade" id="modal-crear-exp" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="ti ti-folder-plus me-2 text-primary"></i>
                    Nuevo Expediente
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form id="form-crear-exp" method="POST" action="{{ route('panel.expedientes.store') }}">
                @csrf
                <div class="modal-body">
                    {{-- campos del formulario --}}
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="ti ti-device-floppy me-1"></i> Guardar
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>
```

Incluir en la vista principal al final del `@section('content')`:

```blade
@include('panel_especialista.expediente._partials.modal-crear')
```

---

## 10. Respuestas del controlador

### Vista normal (GET)
```php
return view('panel_especialista.modulo.index', compact('especialista', 'datos'));
```

### Redirect con mensaje
```php
return redirect()->route('panel.expedientes.index')
    ->with('success', 'Operación completada.');
// o
    ->with('error', 'Algo salió mal.');
```

### AJAX — éxito
```php
return response()->json([
    'success' => true,
    'mensaje' => 'Guardado correctamente.',
    'data'    => $modelo,
]);
```

### AJAX — error de validación
```php
// Laravel lo devuelve automáticamente como 422 cuando el request
// tiene Accept: application/json y falla $request->validate()
```

### AJAX — error controlado
```php
return response()->json([
    'success' => false,
    'mensaje' => 'No se puede realizar esta acción.',
], 422);
```

---

## 11. Variables siempre disponibles en las vistas

Gracias al layout `panel_especialista.layouts.master`, cualquier vista del panel puede acceder a:

```blade
{{-- El especialista autenticado --}}
@php $esp = auth('especialista')->user(); @endphp

{{-- Nombre completo --}}
{{ $esp->nombre_completo }}

{{-- Especialidad --}}
{{ $esp->especialidad ?? $esp->profesion }}

{{-- Su ID (para filtros) --}}
{{ $esp->id }}
```

---

## 12. Checklist para crear un módulo nuevo

```
[ ] 1. Crear el controlador en app/Http/Controllers/PanelEspecialista/
[ ] 2. Agregar las rutas en panelespecialista_routes.php
[ ] 3. Agregar el ítem al sidebar (layouts/sidebar.blade.php)
[ ] 4. Crear carpeta de vistas en resources/views/panel_especialista/{modulo}/
[ ] 5. Crear index.blade.php extendiendo panel_especialista.layouts.master
[ ] 6. Crear _partials/ con toolbar, cards, modales según necesite
[ ] 7. Si tiene CSS propio: public/assets/panel_especialista/css/{modulo}.css
[ ] 8. Si tiene JS propio: public/assets/panel_especialista/js/{modulo}.js
[ ] 9. Cargar CSS/JS en la vista con @push('styles') / @push('scripts')
[ ] 10. Probar en móvil/tablet (el panel es responsive)
```

---

## 13. Convenciones de nombres

| Elemento | Convención | Ejemplo |
|----------|-----------|---------|
| Controlador | `{Modulo}Controller.php` | `ConsultaController.php` |
| Ruta name | `panel.{modulo}.{accion}` | `panel.consultas.store` |
| Vista carpeta | `{modulo}/` en minúscula | `consulta/` |
| Partial | `_{nombre}.blade.php` | `_modal-crear.blade.php` |
| CSS class | `{prefijo}-{elemento}` | `con-card`, `con-foto-grid` |
| JS módulo | `{Modulo}Panel` IIFE | `ConsultaPanel` |
| JS archivo | `{modulo}.js` | `consulta.js` |
| CSS archivo | `{modulo}.css` | `consulta.css` |

---

## 14. Módulos del Panel — Estado actual

| Módulo | Ruta | Controlador | Estado |
|--------|------|-------------|--------|
| Auth (login/logout) | `/panel/login` | `PanelAuthController` | ✅ Listo |
| Agenda del día | `/panel/agenda` | `AgendaController` | ✅ Listo |
| Expediente clínico | `/panel/expedientes` | `ExpedienteController` | 🔨 En desarrollo |
| Caso podológico | `/panel/casos` | `CasoController` | 🔨 En desarrollo |
| Consulta / Atención | `/panel/consultas` | `ConsultaController` | 🔨 En desarrollo |
| Receta (PDF) | `/panel/recetas` | `RecetaController` | ⏳ Pendiente |
| Mi perfil / password | `/panel/perfil` | `PerfilController` | ⏳ Pendiente |
