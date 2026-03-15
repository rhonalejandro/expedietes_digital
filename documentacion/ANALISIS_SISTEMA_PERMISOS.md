# Sistema de Permisos Granulares - Expediente Digital

## 📋 Análisis Detallado y Propuesta de Implementación

**Fecha:** 2026-02-17  
**Versión:** 1.0  
**Estado:** Documento de planificación

---

## 1. ANÁLISIS DE REQUERIMIENTOS

### 1.1 Problema a Resolver

El sistema necesita un esquema de permisos que:
- ✅ Permita acceso general a módulos completos
- ✅ Permita permisos granulares dentro de cada módulo (ver, crear, editar, eliminar)
- ✅ Permita permisos especiales específicos de ciertos módulos (ej: impuestos en productos)
- ✅ NO limite por roles rígidamente - un usuario puede tener múltiples permisos de diferentes roles
- ✅ Use plantillas de permisos para facilitar la asignación inicial
- ✅ Sea fácil de mantener y extender
- ✅ Tenga helpers tipo: `hasPermission('clients')`, `canEdit('clients')`, `canEdit('products', 'taxes')`

### 1.2 Análisis de la Estructura Actual

#### Tablas Existentes:
```
personas ──< usuarios >── usuario_rol ──> roles
           └── doctores
           └── pacientes
```

**Observación importante:** La tabla `usuarios` ya tiene un campo `persona_id` que la relaciona con `personas`. Esto significa que:
- Un usuario del sistema ES una persona
- Los doctores también son personas (y pueden ser usuarios)
- Los pacientes también son personas (pero no necesariamente son usuarios)

---

## 2. PROPUESTA DE ARQUITECTURA DE PERMISOS

### 2.1 Nuevo Esquema de Base de Datos

#### Tabla: `permisos`
Define todos los permisos disponibles en el sistema.

| Campo         | Tipo       | Comentario                              |
|---------------|------------|-----------------------------------------|
| id            | BIGSERIAL  | PK                                      |
| modulo        | VARCHAR(50)| Módulo al que pertenece (clients, products, etc.) |
| codigo        | VARCHAR(50)| Código único del permiso (view, create, edit, delete, taxes, accounting, etc.) |
| nombre        | VARCHAR(100)| Nombre legible del permiso              |
| descripcion   | TEXT       | Descripción detallada                   |
| tipo          | VARCHAR(20)| 'general' o 'granular'                  |
| estado        | BOOLEAN    | Activo/Inactivo                         |
| created_at    | TIMESTAMP  |                                         |
| updated_at    | TIMESTAMP  |                                         |

**Ejemplos de registros:**
```
| modulo      | codigo     | nombre                    | tipo     |
|-------------|------------|---------------------------|----------|
| clients     | view       | Ver clientes              | general  |
| clients     | create     | Crear clientes            | general  |
| clients     | edit       | Editar clientes           | general  |
| clients     | delete     | Eliminar clientes         | general  |
| products    | view       | Ver productos             | general  |
| products    | create     | Crear productos           | general  |
| products    | edit       | Editar productos          | general  |
| products    | delete     | Eliminar productos        | general  |
| products    | taxes      | Gestionar impuestos       | granular |
| products    | accounting | Gestionar cuentas contables | granular |
| products    | pricing    | Cambiar precios/costos    | granular |
| appointments| view       | Ver citas                 | general  |
| appointments| create     | Agendar citas             | general  |
| appointments| edit       | Editar citas              | general  |
| appointments| delete     | Cancelar/Eliminar citas   | general  |
| appointments| reschedule | Reprogramar citas         | granular |
| medical     | view       | Ver expedientes           | general  |
| medical     | create     | Crear expedientes         | general  |
| medical     | edit       | Editar expedientes        | general  |
| medical     | delete     | Eliminar expedientes      | general  |
| medical     | sign       | Firmar digitalmente       | granular |
| medical     | histories  | Ver historial médico      | granular |
```

#### Tabla: `usuario_permiso`
Asignación directa de permisos a usuarios (sin pasar por roles).

| Campo         | Tipo       | Comentario                              |
|---------------|------------|-----------------------------------------|
| usuario_id    | BIGINT     | FK a usuarios                           |
| permiso_id    | BIGINT     | FK a permisos                           |
| asignado_por  | BIGINT     | FK a usuarios (quién asignó el permiso) |
| fecha_asignacion | TIMESTAMP |                                         |
| PRIMARY KEY   | (usuario_id, permiso_id) |                           |

#### Tabla: `plantillas_permisos` (Opcional pero recomendada)
Plantillas predefinidas para asignación rápida.

| Campo         | Tipo       | Comentario                              |
|---------------|------------|-----------------------------------------|
| id            | BIGSERIAL  | PK                                      |
| nombre        | VARCHAR(100)| Nombre de la plantilla (Admin, Recepcionista, Doctor, etc.) |
| descripcion   | TEXT       | Descripción                             |
| es_sistema    | BOOLEAN    | Si es plantilla del sistema (no editable) |
| created_at    | TIMESTAMP  |                                         |
| updated_at    | TIMESTAMP  |                                         |

#### Tabla: `plantilla_permiso_detalle`
Permisos incluidos en cada plantilla.

| Campo         | Tipo       | Comentario                              |
|---------------|------------|-----------------------------------------|
| plantilla_id  | BIGINT     | FK a plantillas_permisos                |
| permiso_id    | BIGINT     | FK a permisos                           |
| PRIMARY KEY   | (plantilla_id, permiso_id) |                         |

### 2.2 Modificación a Tabla `roles`

Los roles se convierten en **etiquetas agrupadoras** de plantillas de permisos, no en limitantes.

| Campo         | Tipo       | Comentario                              |
|---------------|------------|-----------------------------------------|
| id            | BIGSERIAL  | PK                                      |
| nombre        | VARCHAR(100)| Nombre del rol                          |
| descripcion   | VARCHAR(255)| Descripción                             |
| plantilla_id  | BIGINT     | FK a plantillas_permisos (permisos por defecto) |
| es_sistema    | BOOLEAN    | Si es rol del sistema                   |
| created_at    | TIMESTAMP  |                                         |
| updated_at    | TIMESTAMP  |                                         |

---

## 3. ARQUITECTURA DE CÓDIGO

### 3.1 Estructura de Archivos

```
app/
├── Models/
│   ├── Permiso.php
│   ├── PlantillaPermiso.php
│   └── Usuario.php (actualizado)
├── Services/
│   └── Permissions/
│       ├── PermissionService.php      # Lógica de negocio de permisos
│       ├── PermissionRegistrar.php    # Registro de permisos del sistema
│       └── PermissionChecker.php      # Verificación de permisos
├── Helpers/
│   └── PermissionHelper.php           # Funciones globales hasPermission, canEdit, etc.
├── Http/
│   ├── Controllers/
│   │   └── Settings/
│   │       ├── PermissionsController.php
│   │       ├── PermissionTemplatesController.php
│   │       └── UserPermissionsController.php
│   ├── Middleware/
│   │   ├── CheckPermission.php        # Middleware para rutas
│   │   └── CheckModuleAccess.php      # Middleware para módulos
│   └── Requests/
│       └── Settings/
│           ├── AssignPermissionRequest.php
│           ├── CreateTemplateRequest.php
│           └── UpdateUserPermissionsRequest.php
└── Providers/
    └── PermissionServiceProvider.php  # Registro de permisos al boot
```

### 3.2 Helpers Globales

```php
// app/Helpers/PermissionHelper.php

/**
 * Verificar si el usuario autenticado tiene un permiso específico
 * 
 * @param string $modulo
 * @param string|null $accion (null para permiso general del módulo)
 * @return bool
 */
function hasPermission(string $modulo, ?string $accion = null): bool

/**
 * Verificar si puede ver (leer) un módulo
 */
function canView(string $modulo): bool

/**
 * Verificar si puede crear en un módulo
 */
function canCreate(string $modulo): bool

/**
 * Verificar si puede editar en un módulo
 */
function canEdit(string $modulo): bool

/**
 * Verificar si puede eliminar en un módulo
 */
function canDelete(string $modulo): bool

/**
 * Verificar permiso granular específico
 */
function canDo(string $modulo, string $permisoGranular): bool

/**
 * Obtener todos los permisos del usuario
 */
function getUserPermissions(): Collection

/**
 * Verificar si tiene acceso completo a un módulo
 */
function hasFullAccess(string $modulo): bool
```

### 3.3 Uso en Código

```php
// En controladores
public function index()
{
    if (!canView('clients')) {
        abort(403, 'No tienes permiso para ver clientes');
    }
    
    $clients = Client::all();
    return view('clients.index', compact('clients'));
}

public function edit($id)
{
    if (!canEdit('clients')) {
        abort(403, 'No tienes permiso para editar clientes');
    }
    
    // ...
}

public function updateTaxes(Request $request)
{
    if (!canDo('products', 'taxes')) {
        abort(403, 'No tienes permiso para gestionar impuestos');
    }
    
    // ...
}

public function changePrice(Request $request)
{
    if (!canDo('products', 'pricing')) {
        abort(403, 'No tienes permiso para cambiar precios');
    }
    
    // ...
}

// En vistas Blade
@canView('clients')
    <a href="{{ route('clients.index') }}">Clientes</a>
@endcanView

@canEdit('products')
    <button class="btn btn-primary">Editar Producto</button>
@endcanEdit

@canDo('products', 'taxes')
    <a href="{{ route('products.taxes') }}">Impuestos</a>
@endcanDo

// En rutas (middleware)
Route::middleware(['auth', 'permission:clients.edit'])->group(function () {
    Route::put('/clients/{id}', [ClientController::class, 'update']);
});
```

---

## 4. MÓDULOS Y PERMISOS PROPUESTOS

### 4.1 Módulos Principales

| Módulo | Código | Permisos Generales | Permisos Granulares |
|--------|--------|-------------------|---------------------|
| **Configuración** | `settings` | view, edit | empresa, sucursales, logos, monedas |
| **Usuarios** | `users` | view, create, edit, delete | permissions, roles, activate/deactivate |
| **Roles y Permisos** | `permissions` | view, edit, assign | templates, bulk_assign |
| **Clientes/Pacientes** | `clients` | view, create, edit, delete | medical_history, export_data |
| **Doctores** | `doctors` | view, create, edit, delete | schedules, assignments, specialties |
| **Citas** | `appointments` | view, create, edit, delete | reschedule, cancel, confirm, reports |
| **Casos** | `cases` | view, create, edit, delete | close_case, transfer |
| **Expedientes** | `medical_records` | view, create, edit, delete | sign_digital, histories, images |
| **Servicios** | `services` | view, create, edit, delete | pricing, categories |
| **Productos** | `products` | view, create, edit, delete | taxes, accounting, pricing, inventory |
| **Pagos** | `payments` | view, create, edit | refund, reports, export |
| **Reportes** | `reports` | view, export | financial, medical, audit_logs |
| **Auditoría** | `audit` | view, export | delete_logs |
| **Notificaciones** | `notifications` | view, send | templates, bulk_send |

### 4.2 Permisos Especiales por Módulo

#### Módulo de Productos (Ejemplo Complejo)
```php
[
    'general' => ['view', 'create', 'edit', 'delete'],
    'granular' => [
        'taxes' => 'Gestionar impuestos y tasas',
        'accounting' => 'Gestionar cuentas contables',
        'pricing' => 'Cambiar precios y costos',
        'inventory' => 'Gestionar inventario',
        'categories' => 'Gestionar categorías',
        'bulk_operations' => 'Operaciones masivas',
    ]
]
```

#### Módulo de Expedientes Médicos
```php
[
    'general' => ['view', 'create', 'edit', 'delete'],
    'granular' => [
        'sign_digital' => 'Firmar digitalmente documentos',
        'histories' => 'Ver historial médico completo',
        'images' => 'Ver/gestionar imágenes médicas',
        'export' => 'Exportar expedientes',
        'print' => 'Imprimir expedientes',
    ]
]
```

#### Módulo de Citas
```php
[
    'general' => ['view', 'create', 'edit', 'delete'],
    'granular' => [
        'reschedule' => 'Reprogramar citas',
        'cancel' => 'Cancelar citas',
        'confirm' => 'Confirmar citas',
        'own_appointments' => 'Solo ver sus propias citas',
        'all_appointments' => 'Ver todas las citas',
    ]
]
```

---

## 5. PLAN DE IMPLEMENTACIÓN

### Fase 1: Estructura de Base de Datos (Día 1)
- [ ] Crear migración para tabla `permisos`
- [ ] Crear migración para tabla `usuario_permiso`
- [ ] Crear migración para tabla `plantillas_permisos`
- [ ] Crear migración para tabla `plantilla_permiso_detalle`
- [ ] Modificar tabla `roles` para agregar `plantilla_id`
- [ ] Crear modelos Eloquent

### Fase 2: Services y Helpers (Día 2)
- [ ] Crear `PermissionService`
- [ ] Crear `PermissionRegistrar` (registro automático de permisos)
- [ ] Crear `PermissionChecker`
- [ ] Crear `PermissionHelper` con funciones globales
- [ ] Crear `PermissionServiceProvider`
- [ ] Registrar helpers en `config/app.php`

### Fase 3: Middleware (Día 3)
- [ ] Crear `CheckPermission` middleware
- [ ] Crear `CheckModuleAccess` middleware
- [ ] Crear directivas Blade (@canView, @canEdit, etc.)
- [ ] Crear Gate providers

### Fase 4: Controladores y Vistas (Día 4-5)
- [ ] `PermissionsController` - CRUD de permisos
- [ ] `PermissionTemplatesController` - CRUD de plantillas
- [ ] `UserPermissionsController` - Asignación de permisos
- [ ] Vista de gestión de permisos (UI tipo matriz/checklist)
- [ ] Vista de asignación a usuarios
- [ ] Vista de plantillas

### Fase 5: Integración con Módulos Existentes (Día 6-7)
- [ ] Agregar permisos al módulo de Clientes
- [ ] Agregar permisos al módulo de Doctores
- [ ] Agregar permisos al módulo de Citas
- [ ] Agregar permisos al módulo de Configuración
- [ ] Agregar permisos al módulo de Sucursales
- [ ] Agregar middleware a rutas existentes

### Fase 6: Testing y Documentación (Día 8)
- [ ] Tests unitarios para PermissionService
- [ ] Tests de integración para middleware
- [ ] Tests de features para controladores
- [ ] Documentación de uso
- [ ] Seeders de permisos por defecto

---

## 6. SEEDERS INICIALES

```php
// database/seeders/PermissionsSeeder.php

public function run(): void
{
    // Permisos de Configuración
    Permiso::create([
        'modulo' => 'settings',
        'codigo' => 'view',
        'nombre' => 'Ver configuración',
        'tipo' => 'general'
    ]);
    
    // ... más permisos
    
    // Plantilla Administrador
    $adminTemplate = PlantillaPermiso::create([
        'nombre' => 'Administrador',
        'descripcion' => 'Acceso completo al sistema',
        'es_sistema' => true
    ]);
    
    // Asignar todos los permisos a admin
    $todosPermisos = Permiso::all();
    foreach ($todosPermisos as $permiso) {
        PlantillaPermisoDetalle::create([
            'plantilla_id' => $adminTemplate->id,
            'permiso_id' => $permiso->id
        ]);
    }
    
    // Plantilla Recepcionista
    $recepcionista = PlantillaPermiso::create([
        'nombre' => 'Recepcionista',
        'descripcion' => 'Gestión de citas y clientes',
        'es_sistema' => true
    ]);
    
    // Asignar permisos específicos
    $permisosRecepcionista = Permiso::whereIn('modulo', [
        'clients', 'appointments'
    ])->whereIn('codigo', ['view', 'create', 'edit'])->get();
    
    // ... asignar permisos
    
    // Plantilla Doctor
    $doctor = PlantillaPermiso::create([
        'nombre' => 'Doctor',
        'descripcion' => 'Gestión de pacientes y expedientes',
        'es_sistema' => true
    ]);
    
    // ... asignar permisos médicos
}
```

---

## 7. INTERFAZ DE USUARIO PROPUESTA

### 7.1 Vista de Gestión de Permisos

```
┌─────────────────────────────────────────────────────────────┐
│  GESTIÓN DE PERMISOS                                        │
├─────────────────────────────────────────────────────────────┤
│                                                              │
│  Módulo: [Clients ▼]                                        │
│                                                              │
│  ┌──────────────────────────────────────────────────────┐  │
│  │ Permiso          │ Tipo     │ Activo │ Acciones     │  │
│  ├──────────────────────────────────────────────────────┤  │
│  │ ✓ Ver clientes   │ General  │  [✓]   │ [✏️] [🗑️]    │  │
│  │ ✓ Crear clientes │ General  │  [✓]   │ [✏️] [🗑️]    │  │
│  │ ✓ Editar clientes│ General  │  [✓]   │ [✏️] [🗑️]    │  │
│  │ ✓ Eliminar cli.  │ General  │  [✓]   │ [✏️] [🗑️]    │  │
│  │ Ver historial    │ Granular │  [ ]   │ [✏️] [🗑️]    │  │
│  │ Exportar datos   │ Granular │  [ ]   │ [✏️] [🗑️]    │  │
│  └──────────────────────────────────────────────────────┘  │
│                                                              │
│  [+ Agregar Permiso]                                        │
└─────────────────────────────────────────────────────────────┘
```

### 7.2 Vista de Asignación a Usuarios

```
┌─────────────────────────────────────────────────────────────┐
│  ASIGNAR PERMISOS A USUARIO: Juan Pérez                    │
├─────────────────────────────────────────────────────────────┤
│                                                              │
│  Plantilla sugerida: [Doctor ▼]  [Aplicar Plantilla]       │
│                                                              │
│  ┌──────────────────────────────────────────────────────┐  │
│  │ Módulo           │ Ver │ Crear │ Editar │ Eliminar │  │
│  ├──────────────────────────────────────────────────────┤  │
│  │ ✓ Clientes       │ [✓] │  [✓]  │  [✓]   │   [ ]    │  │
│  │ ✓ Doctores       │ [✓] │  [ ]  │  [ ]   │   [ ]    │  │
│  │ ✓ Citas          │ [✓] │  [✓]  │  [✓]   │   [ ]    │  │
│  │ ✓ Expedientes    │ [✓] │  [✓]  │  [✓]   │   [ ]    │  │
│  │ ☐ Configuración  │ [ ] │  [ ]  │  [ ]   │   [ ]    │  │
│  │ ☐ Usuarios       │ [ ] │  [ ]  │  [ ]   │   [ ]    │  │
│  │ ☐ Permisos       │ [ ] │  [ ]  │  [ ]   │   [ ]    │  │
│  └──────────────────────────────────────────────────────┘  │
│                                                              │
│  Permisos Granulares:                                       │
│  ┌──────────────────────────────────────────────────────┐  │
│  │ ☑ Ver historial médico                               │  │
│  │ ☑ Firmar digitalmente                                │  │
│  │ ☐ Exportar expedientes                               │  │
│  └──────────────────────────────────────────────────────┘  │
│                                                              │
│  [💾 Guardar Permisos]                                      │
└─────────────────────────────────────────────────────────────┘
```

---

## 8. CONSIDERACIONES IMPORTANTES

### 8.1 Sobre Roles vs Permisos Directos

**Decisión de diseño:** Los roles son **etiquetas** que aplican plantillas de permisos, pero NO limitan. Un usuario puede tener:
- Una plantilla base (vía rol)
- Permisos adicionales asignados directamente
- Permisos denegados explícitamente (si se requiere en el futuro)

### 8.2 Sobre Doctores como Usuarios

Los doctores son personas que PUEDEN ser usuarios. Si un doctor es usuario:
- Tiene su registro en `personas`
- Tiene su registro en `usuarios` (con email/password)
- Tiene su registro en `doctores` (con especialidad)
- Puede tener permisos de usuario administrativo + permisos médicos

### 8.3 Caché de Permisos

Para rendimiento, los permisos deben cachearse:
```php
// En PermissionService
public function getUserPermissions(int $userId): Collection
{
    return Cache::remember(
        "user_permissions_{$userId}",
        now()->addHours(2),
        fn() => $this->loadPermissionsFromDB($userId)
    );
}

// Clear cache cuando se asignan permisos
Cache::forget("user_permissions_{$userId}");
```

### 8.4 Auditoría de Permisos

Crear tabla `log_permisos` para auditar:
- Quién asignó qué permiso
- Cuándo se modificó
- Qué usuario recibió el permiso

---

## 9. PRÓXIMOS PASOS INMEDIATOS

1. **Crear migraciones** de las nuevas tablas
2. **Crear modelos** Eloquent
3. **Crear PermissionService** base
4. **Crear helpers** globales
5. **Crear seeder** con permisos iniciales
6. **Probar** en un módulo pequeño (ej: Clientes)
7. **Iterar** y ajustar según feedback

---

## 10. PREGUNTAS PARA DEFINIR

1. ¿Los permisos granulares deben ser visibles en la UI principal o en una sección "Avanzado"?
2. ¿Queremos permitir denegar permisos explícitamente (override negativo)?
3. ¿Los doctores deben tener permisos automáticos al ser creados como usuarios?
4. ¿Queremos un log de auditoría de cambios de permisos?
5. ¿Las plantillas deben poder editarse o solo las del sistema son fijas?

---

**Fin del documento de análisis**
