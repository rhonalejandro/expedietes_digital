
# Arquitectura Modular - Principios SOLID y Clean Code

## Estructura del Proyecto

### 1. **Vistas Modulares**
- Máximo 300 líneas por vista
- Usar partials para componentes reutilizables
- Cada partial con responsabilidad única

```markdown
/views
    /shared
        /_header.blade.php
        /_footer.blade.php
        /_sidebar.blade.php
    /components
        /_button.blade.php
        /_form-group.blade.php
        /_alert.blade.php
```

### 2. **Componentes Independientes**
- Crear componentes totalmente reutilizables
- Props/parameters claramente definidos
- Sin dependencias de contexto global

### 3. **Migraciones**
```bash
php artisan make:migration create_table_name
php artisan migrate
php artisan migrate:rollback
```

### 4. **Principios SOLID**

| Principio | Aplicación |
|-----------|-----------|
| **S**ingle Responsibility | Una clase, una responsabilidad |
| **O**pen/Closed | Abierto para extensión, cerrado para modificación |
| **L**iskov Substitution | Sustitución de subclases |
| **I**nterface Segregation | Interfaces específicas |
| **D**ependency Inversion | Inyección de dependencias |

### 5. **DRY - Don't Repeat Yourself**
- Extraer lógica común a servicios/traits
- Reutilizar componentes y partials
- Helpers y utilities compartidas

---

## Contexto del Proyecto

### ¿Qué es este sistema?
CRM médico desarrollado por **Krom-Soft** para el cliente **Global Feet Panama** (clínica de podología en Panamá). El sistema gestiona la operación completa de la clínica: pacientes, especialistas, citas, expedientes clínicos, leads y configuración empresarial.

### Desarrollador y cliente
- **Desarrollador:** Krom-Soft — cuenta `admin@krom-soft.com`
- **Cliente:** Manuel Castro / Global Feet Panama — cuenta `globalfeetpanama@gmail.com`
- Ambos con rol Super Administrador en `usuarios`

---

## Stack Técnico

| Capa | Tecnología |
|------|-----------|
| Backend | Laravel 12 + PHP |
| Base de datos | PostgreSQL |
| Frontend | Blade + Bootstrap + jQuery |
| Calendario | FullCalendar v6 |
| Autenticación | Sesión + TOTP (2FA panel dev) |
| Integración externa | Chatwoot (comunicaciones.globalfeetpanama.com) |

---

## Módulos Activos

### Citas (`/citas`)
- 3 vistas de calendario: Día, Semana, Recursos (custom grid)
- Drag & drop con snap de 10 minutos
- Modo agenda configurable: **Estricto** (valida solapamiento) / **Sobrecarga** (libre)
- Mini-calendario de disponibilidad para crear citas
- Confirmación de citas pendientes
- Controlador: `CitaController`
- Módulo JS: `public/assets/modules/citas/js/citas.module.js` + `MiniCal` (IIFE separado al final del mismo archivo)
- Config `modoAgenda` en tabla `empresas.modo_agenda` (valores: `estricto` | `sobrecarga`)

### Pacientes (`/pacientes`)
- CRUD completo con soft deletes
- Búsqueda con autocomplete (endpoint `/pacientes/buscar`)
- Historial de actividad por paciente (log_clientes)
- Controlador: `PacienteController`

### Especialistas (`/especialistas`)
- CRUD completo con soft deletes
- Horarios por día de semana con duración de cita configurable
- Asignación a sucursales
- Controlador: `EspecialistaController`

### Leads
- Integración con Chatwoot via webhook HMAC (`POST /api/v1/webhook/chatwoot`)
- Conversión de lead → paciente
- Controlador: `ApiLeadController`

### Configuración (`/settings`)
- **Empresa:** datos generales, logos
- **Sucursales:** horario apertura/cierre (afecta horas visibles en calendario)
- **Citas:** modo estricto / sobrecarga
- **Permisos:** granulares + plantillas de permisos
- Controladores: `SettingsController`, `EmpresaController`, `SucursalesController`, `PermissionsController`

### Panel de Desarrollador (`/developer`) — TOTP protegido
- CRUD de módulos del sistema
- CRUD de acciones/permisos
- Controladores: `DeveloperAuthController`, `DeveloperModulosController`

---

## Arquitectura de Base de Datos

### Entidades principales
| Tabla | Descripción |
|-------|------------|
| `personas` | Base de todos los actores (soft delete) |
| `usuarios` | Usuarios del sistema (FK → personas) |
| `pacientes` | Pacientes (FK → personas, soft delete) |
| `especialistas` | Doctores/especialistas (FK → personas) |
| `citas` | Citas médicas (nullable paciente_id para leads) |
| `leads` | Prospectos de Chatwoot antes de convertir |
| `empresas` | Organización (modo_agenda, logos) |
| `sucursales` | Sucursales/sedes (soft delete) |
| `servicios` | Servicios médicos ofrecidos |
| `horarios_especialistas` | Disponibilidad semanal por especialista |
| `casos` | Casos clínicos |
| `expedientes` | Expedientes médicos por caso |

### Permisos
```
PlantillaPermiso → PlantillaPermisoDetalle → Permiso
Rol → UsuarioRol → Usuario → UsuarioPermiso → Permiso
```
- Middleware: `CheckPermission`, `CheckModuleAccess`

### Auditoría
Tablas separadas por módulo: `log_citas`, `log_clientes`, `log_especialistas`, `log_expedientes`, `log_empresas`
- Helper centralizado: `LogSistemaHelper` en `app/Helpers/LogSistemaHelper.php`
- Guarda: usuario, IP, fecha, campos modificados (valor anterior → valor actual)

---

## Rutas

Las rutas web están organizadas modularmente en `routes/web_routes/`:
- `citas/citas_routes.php`
- `pacientes/pacientes_routes.php`
- `especialistas/especialistas_routes.php`
- `servicios/servicios_routes.php`
- `settings/settings.php`
- `auth/auth_routes.php`
- `developer/developer_routes.php`
- `log_actividad_routes.php`

La API vive en `routes/api.php` bajo `/api/v1` con autenticación Bearer (`ApiTokenMiddleware`).

---

## Assets Frontend

- Módulos JS por feature en `public/assets/modules/{modulo}/js/`
- Módulos CSS en `public/assets/modules/{modulo}/css/`
- Componentes compartidos en `public/assets/components/` (ej. `log-actividad/`)
- `CitasModule` y `MiniCal` son IIFEs en `citas.module.js` — MiniCal recibe config via `MiniCal.init({ modoAgenda })`

---

## Integración Chatwoot
- URL base: `https://comunicaciones.globalfeetpanama.com`
- Webhook: `POST /api/v1/webhook/chatwoot` (verificación HMAC con `CHATWOOT_WEBHOOK_SECRET`)
- Flujo: conversación en Chatwoot → webhook → crea Lead en BD → puede convertirse en Paciente
- Config en `config/services.php` bajo clave `chatwoot`

---

## Módulos existentes pero sin UI completa (pendientes)
- **Expedientes / Casos:** tablas y modelos listos, sin vistas de gestión completas
- **Pagos:** tabla `pagos` existe, sin controlador/vistas
- **Notificaciones:** tabla y modelo existen, flujo no completado
- **Dashboard KPIs:** `DashboardController` existe pero sin métricas robustas conectadas

---

## Panel Especialista (`/panel`)

Sistema de autenticación **separado** del panel administrativo. Los especialistas inician sesión en `/panel/login` con su correo profesional y password, accediendo a su propia agenda clínica.

### Auth Guard
- Guard: `especialista` (driver: session, provider: `especialistas`)
- Modelo: `App\Models\Especialista` extiende `Authenticatable`
- Campos auth en `especialistas`: `password` (hashed), `remember_token`
- Siempre usar `Auth::guard('especialista')->user()` — NO `Auth::user()`

### Rutas
- Definidas en `routes/web_routes/panel_especialista/panelespecialista_routes.php`
- Prefijo `/panel`, middleware `auth:especialista` para rutas protegidas

### Estructura de archivos
```
app/Http/Controllers/PanelEspecialista/   ← controladores del panel
resources/views/panel_especialista/
    layouts/master.blade.php              ← layout principal
    layouts/sidebar.blade.php
    layouts/header.blade.php
    auth/login.blade.php
    agenda/index.blade.php
    _partials/                            ← partials del panel
public/assets/panel_especialista/
    css/panel.css                         ← estilos (prefijo pnl-*)
    js/                                   ← módulos JS del panel
```

### ⚠️ IMPORTANTE — Para crear cualquier módulo o componente en el Panel Especialista:
**Leer primero `ManualDevPanelEspecialista.md`** en la raíz del proyecto antes de escribir cualquier código. Este archivo contiene la estructura de archivos, convenciones de nombres, patrones de controlador, CSS (prefijos `pnl-*`), JS (patrón IIFE `{Modulo}Panel`), estructura de modales, patrones de respuesta, cómo agregar nav items al sidebar y un checklist completo.
