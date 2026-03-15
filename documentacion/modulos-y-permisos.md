# Manual: Sistema de Módulos y Permisos

**Proyecto:** Expediente Digital
**Framework:** Laravel 12 + PostgreSQL
**Fecha:** 2026-03-14

---

## Indice

1. [Conceptos fundamentales](#1-conceptos-fundamentales)
2. [Arquitectura y tablas](#2-arquitectura-y-tablas)
3. [Developer Panel](#3-developer-panel)
4. [Crear un módulo](#4-crear-un-modulo)
5. [Crear acciones de un módulo](#5-crear-acciones-de-un-modulo)
6. [Usar permisos en controladores](#6-usar-permisos-en-controladores)
7. [Usar permisos en vistas Blade](#7-usar-permisos-en-vistas-blade)
8. [Plantillas de permisos](#8-plantillas-de-permisos)
9. [Asignar permisos a usuarios](#9-asignar-permisos-a-usuarios)
10. [Flujo completo de ejemplo](#10-flujo-completo-de-ejemplo)
11. [Referencia de funciones](#11-referencia-de-funciones)
12. [Configuracion en el servidor](#12-configuracion-en-el-servidor)

---

## 1. Conceptos fundamentales

### Por que no usamos roles

Los sistemas basados en roles tienen un problema: son rígidos. Si un usuario necesita un permiso extra fuera de su rol, tienes que crear un nuevo rol o romper toda la lógica. Con el tiempo, el sistema de roles se vuelve inmanejable.

Este sistema usa un enfoque diferente: **permisos directos por módulo y acción**, con plantillas como atajo opcional.

### Los tres niveles

```
Nivel 1 — Modulos
  Son las secciones del sistema: Pacientes, Expedientes, Citas, etc.
  Cada módulo tiene un slug único que lo identifica.

Nivel 2 — Acciones
  Son las operaciones disponibles dentro de un módulo.
  Ejemplos: ver, crear, editar, eliminar, exportar, imprimir, aprobar.

Nivel 3 — Asignacion
  Se le dice a cada usuario qué acciones puede realizar en qué módulos.
  Opcionalmente usando plantillas como punto de partida.
```

### Tipos de accion

**General** — Operaciones CRUD estándar. Convenio recomendado:

| Codigo | Significado            |
|--------|------------------------|
| ver    | Puede leer/listar      |
| crear  | Puede crear registros  |
| editar | Puede modificar        |
| eliminar | Puede dar de baja    |

**Granular** — Operaciones especiales de negocio. Ejemplos:

| Codigo          | Significado                      |
|-----------------|----------------------------------|
| exportar_pdf    | Puede exportar a PDF             |
| aprobar_caso    | Puede aprobar un caso clínico    |
| ver_historial   | Puede ver historial completo     |
| gestionar_pagos | Puede registrar y editar pagos   |

---

## 2. Arquitectura y tablas

### Tablas involucradas

```
modulos
  id, nombre, slug, url, descripcion, icono, orden, activo

permisos
  id, modulo (= modulos.slug), codigo, nombre, descripcion, tipo, estado

usuario_permiso  (tabla pivote)
  usuario_id, permiso_id, asignado_por, fecha_asignacion, observaciones

plantillas_permisos
  id, nombre, descripcion, es_sistema, es_activa, orden, color, icono

plantilla_permiso_detalle  (tabla pivote)
  plantilla_id, permiso_id, creado_por, notas
```

### Como se relacionan

La tabla `modulos` y la tabla `permisos` se relacionan de forma lógica:
el campo `permisos.modulo` almacena el mismo valor que `modulos.slug`.
No hay FK física para mantener compatibilidad con permisos existentes.

```
modulos.slug  =  permisos.modulo
"pacientes"       "pacientes"
```

Cuando creas una acción desde el Developer Panel, el sistema escribe
automáticamente en la tabla `permisos` usando el slug del módulo.

---

## 3. Developer Panel

El Developer Panel es el área exclusiva del desarrollador para registrar
módulos y sus acciones. Está protegido por TOTP (Google Authenticator).

### Configuracion inicial (solo una vez)

**Paso 1.** Instalar el paquete TOTP (ya instalado):
```
composer require pragmarx/google2fa
```

**Paso 2.** Generar el secret y configurar Google Authenticator:
```
php artisan developer:setup-totp
```

El comando muestra el secret y la URI de provisioning. Abre Google
Authenticator en tu celular, toca el botón "+" y elige una de estas opciones:

- "Escanear código QR" — pega la URI en un generador de QR en línea, escanea.
- "Ingresar clave de configuración" — escribe el secret manualmente.

**Paso 3.** Acceder al panel:
```
http://tu-dominio/developer
```

Ingresa el código de 6 dígitos que muestra la app. El código se renueva
cada 30 segundos. La sesión permanece activa por 8 horas.

### Variables de entorno relevantes

```env
DEVELOPER_TOTP_SECRET=tu_secret_aqui
DEVELOPER_SESSION_HOURS=8
DEVELOPER_TOTP_WINDOW=1
```

`DEVELOPER_TOTP_WINDOW=1` permite un margen de 30 segundos de tolerancia
hacia adelante y hacia atrás (útil si el reloj del celular tiene pequeñas
diferencias).

---

## 4. Crear un módulo

### Desde el Developer Panel

1. Accede a `/developer`
2. Clic en "Nuevo Módulo"
3. Completa el formulario:

| Campo       | Descripcion                              | Ejemplo           |
|-------------|------------------------------------------|-------------------|
| Nombre      | Nombre visible en el sistema             | Pacientes         |
| Slug        | Identificador único (auto-generado)      | pacientes         |
| URL Base    | Ruta principal del módulo                | /pacientes        |
| Icono       | Clase de icono Tabler                    | ti ti-users       |
| Orden       | Posición en menú (menor = primero)       | 10                |
| Descripcion | Texto descriptivo del módulo             | Gestión de...     |

El slug se genera automáticamente desde el nombre. Solo puede contener
letras minúsculas, números y guiones bajos. Es el valor que usarás en
todo el código para referenciar el módulo.

### Convenciones de slug

Usa siempre singular o plural consistente en todo el proyecto:

```
pacientes       (módulo de pacientes)
expedientes     (módulo de expedientes)
citas           (módulo de citas)
casos           (módulo de casos clínicos)
reportes        (módulo de reportes)
configuracion   (módulo de configuración)
```

---

## 5. Crear acciones de un módulo

### Desde el Developer Panel

1. En la lista de módulos, clic en el ícono de ojo del módulo deseado
2. Clic en "Nueva Acción"
3. Completa el formulario:

| Campo       | Descripcion                              | Ejemplo              |
|-------------|------------------------------------------|----------------------|
| Nombre      | Descripción legible de la acción         | Ver Pacientes        |
| Código      | Identificador único dentro del módulo    | ver                  |
| Tipo        | General (CRUD) o Granular (especial)     | general              |
| Descripcion | Para qué sirve esta acción               | Permite listar...    |

El código se auto-genera desde el nombre. No puede repetirse dentro del
mismo módulo. Este es el valor que usarás en `canDo()`.

### Acciones recomendadas para un módulo típico

Al crear un módulo nuevo, se recomienda registrar estas acciones base:

```
ver       — Ver/listar registros del módulo        (tipo: general)
crear     — Crear nuevos registros                 (tipo: general)
editar    — Editar registros existentes            (tipo: general)
eliminar  — Eliminar/desactivar registros          (tipo: general)
```

Y luego las granulares que apliquen según el negocio:

```
exportar  — Exportar datos a PDF/Excel             (tipo: granular)
imprimir  — Imprimir fichas o reportes             (tipo: granular)
```

### Lo que ocurre internamente

Cuando guardas una acción, el sistema llama a `PermissionService::registerPermission()`
que escribe en la tabla `permisos`:

```
modulo: "pacientes"
codigo: "ver"
nombre: "Ver Pacientes"
tipo:   "general"
estado: true
```

Este permiso ya queda disponible para asignar a usuarios y plantillas
desde el área de Configuración > Permisos.

---

## 6. Usar permisos en controladores

### Verificar si tiene acceso a un módulo

```php
use App\Helpers\PermissionHelper;

// Tiene cualquier permiso del módulo "pacientes"
if (PermissionHelper::hasPermission('pacientes')) {
    // mostrar enlace en menú
}
```

### Verificar una acción específica

```php
// Puede ver pacientes
if (PermissionHelper::hasPermission('pacientes', 'ver')) {
    // continuar
}

// Shortcut para acciones CRUD estándar
PermissionHelper::canView('pacientes');      // equivale a hasPermission('pacientes', 'view')
PermissionHelper::canCreate('pacientes');   // equivale a hasPermission('pacientes', 'create')
PermissionHelper::canEdit('pacientes');     // equivale a hasPermission('pacientes', 'edit')
PermissionHelper::canDelete('pacientes');   // equivale a hasPermission('pacientes', 'delete')

// Para acciones granulares o con códigos personalizados
PermissionHelper::canDo('pacientes', 'ver');       // codigo personalizado
PermissionHelper::canDo('pacientes', 'exportar');  // acción granular
```

### Abortar si no tiene permiso (403)

```php
// En el método del controlador, al inicio:
PermissionHelper::abortUnlessHasPermission('pacientes', 'ver');

// Equivalente manual:
if (!PermissionHelper::hasPermission('pacientes', 'ver')) {
    abort(403, 'No tienes permiso para ver pacientes.');
}

// Shortcuts:
PermissionHelper::abortUnlessCanView('pacientes');
PermissionHelper::abortUnlessCanEdit('pacientes');
```

### Ejemplo real en un controlador

```php
namespace App\Http\Controllers;

use App\Helpers\PermissionHelper;

class PacienteController extends Controller
{
    public function index()
    {
        PermissionHelper::abortUnlessHasPermission('pacientes', 'ver');

        $pacientes = Paciente::with('persona')->paginate(15);
        return view('modules.pacientes.index', compact('pacientes'));
    }

    public function create()
    {
        PermissionHelper::abortUnlessHasPermission('pacientes', 'crear');
        return view('modules.pacientes.create');
    }

    public function destroy($id)
    {
        PermissionHelper::abortUnlessHasPermission('pacientes', 'eliminar');

        $paciente = Paciente::findOrFail($id);
        $paciente->delete();

        return redirect()->route('pacientes.index')
            ->with('success', 'Paciente eliminado.');
    }
}
```

### Usando las funciones globales (sin el namespace)

Las mismas funciones están disponibles como helpers globales en cualquier
parte del código sin necesidad de importar la clase:

```php
if (hasPermission('pacientes', 'ver')) { ... }
if (canView('pacientes')) { ... }
if (canCreate('pacientes')) { ... }
if (canEdit('pacientes')) { ... }
if (canDelete('pacientes')) { ... }
if (canDo('pacientes', 'exportar')) { ... }
if (hasFullAccess('pacientes')) { ... }   // tiene TODOS los permisos del módulo
if (isAdmin()) { ... }                    // es administrador del sistema
```

---

## 7. Usar permisos en vistas Blade

### Condicionales básicos

```blade
{{-- Mostrar enlace solo si puede ver pacientes --}}
@if (hasPermission('pacientes', 'ver'))
    <a href="{{ route('pacientes.index') }}">Ver Pacientes</a>
@endif

{{-- Mostrar botón solo si puede crear --}}
@if (canCreate('pacientes'))
    <a href="{{ route('pacientes.create') }}" class="btn btn-primary">
        Nuevo Paciente
    </a>
@endif

{{-- Mostrar acciones de tabla según permisos --}}
@if (canEdit('pacientes'))
    <a href="{{ route('pacientes.edit', $paciente->id) }}">Editar</a>
@endif

@if (canDelete('pacientes'))
    <button class="btn-delete">Eliminar</button>
@endif

{{-- Acción granular --}}
@if (canDo('pacientes', 'exportar'))
    <button>Exportar PDF</button>
@endif
```

### Verificar varios permisos a la vez

```blade
{{-- Mostrar sección si tiene al menos uno de estos permisos --}}
@if (hasAnyPermission([['pacientes', 'ver'], ['pacientes', 'crear']]))
    <div class="seccion-pacientes">...</div>
@endif

{{-- Mostrar solo si tiene TODOS estos permisos --}}
@if (hasAllPermissions([['pacientes', 'ver'], ['pacientes', 'editar']]))
    <div>Panel completo</div>
@endif
```

### Ocultar secciones del menú lateral

```blade
{{-- En sidebar.blade.php --}}
@if (hasPermission('pacientes'))
    <li class="{{ request()->routeIs('pacientes.*') ? 'active' : '' }}">
        <a href="{{ route('pacientes.index') }}">
            <i class="ti ti-users"></i> Pacientes
        </a>
    </li>
@endif

@if (hasPermission('expedientes'))
    <li class="{{ request()->routeIs('expedientes.*') ? 'active' : '' }}">
        <a href="{{ route('expedientes.index') }}">
            <i class="ti ti-folder"></i> Expedientes
        </a>
    </li>
@endif
```

---

## 8. Plantillas de permisos

Las plantillas son conjuntos de permisos predefinidos. Sirven para asignar
rápidamente un paquete de permisos a usuarios con el mismo perfil de trabajo,
sin tener que hacerlo permiso por permiso.

### Ejemplos de plantillas

**Plantilla: Recepcionista**
- pacientes.ver
- pacientes.crear
- citas.ver
- citas.crear
- citas.editar

**Plantilla: Doctor**
- pacientes.ver
- expedientes.ver
- expedientes.crear
- expedientes.editar
- casos.ver
- casos.crear
- casos.editar

**Plantilla: Administrador**
- Todos los permisos de todos los módulos

### Crear una plantilla (desde Configuración > Permisos)

1. Ir a Configuracion > Plantillas de Permisos
2. Clic en "Nueva Plantilla"
3. Asignar nombre, color e icono identificatorio
4. Seleccionar los permisos que incluye la plantilla
5. Guardar

### Aplicar una plantilla a un usuario

La plantilla es el punto de partida, no la restriccion final. Después de
aplicar una plantilla, puedes agregar o quitar permisos individuales al
usuario sin afectar a otros usuarios que usen la misma plantilla.

```
Usuario Juan → Plantilla "Recepcionista" aplicada
              + permiso extra: casos.ver    (permiso adicional individual)
              - sin permiso: citas.editar   (se quitó de su perfil personal)
```

---

## 9. Asignar permisos a usuarios

### Desde la interfaz (Configuración > Usuarios)

1. Ir a Configuracion > Usuarios
2. Seleccionar el usuario
3. Pestaña "Permisos"
4. Opción A: Aplicar una plantilla como base
5. Opción B: Marcar permisos individuales por módulo
6. Guardar

### Desde código (PermissionService)

```php
use App\Services\Permissions\PermissionService;

$service = app(PermissionService::class);
$usuario = Usuario::find($id);

// Asignar un permiso individual
$permiso = Permiso::where('modulo', 'pacientes')->where('codigo', 'ver')->first();
$service->assignPermissionToUser($usuario, $permiso, auth()->user());

// Aplicar una plantilla completa
$plantilla = PlantillaPermiso::find($plantillaId);
$service->assignTemplateToUser($usuario, $plantilla, auth()->user());

// Sincronizar permisos (reemplaza todos los existentes)
$service->syncUserPermissions($usuario, [1, 2, 5, 8], auth()->user());

// Quitar un permiso
$service->removePermissionFromUser($usuario, $permiso);
```

### Cache de permisos

Los permisos del usuario se cachean por 120 minutos para no hacer
consultas a la base de datos en cada verificación. El caché se limpia
automáticamente cuando se modifican los permisos del usuario.

Si necesitas forzar la actualización:

```php
$service->clearUserPermissionsCache($usuario->id);
$service->clearAllPermissionsCache();  // limpia caché de todos los usuarios
```

---

## 10. Flujo completo de ejemplo

### Caso: Crear el módulo de Expedientes

**Paso 1.** Ir a `/developer`, ingresar código TOTP.

**Paso 2.** Crear el módulo:
```
Nombre:      Expedientes
Slug:        expedientes
URL Base:    /expedientes
Icono:       ti ti-folder
Orden:       30
```

**Paso 3.** Agregar acciones:
```
Nombre: Ver Expedientes     Código: ver       Tipo: general
Nombre: Crear Expediente    Código: crear     Tipo: general
Nombre: Editar Expediente   Código: editar    Tipo: general
Nombre: Eliminar Expediente Código: eliminar  Tipo: general
Nombre: Ver Historial       Código: ver_historial   Tipo: granular
Nombre: Agregar Nota        Código: agregar_nota    Tipo: granular
Nombre: Imprimir Expediente Código: imprimir        Tipo: granular
```

**Paso 4.** El sistema creó en la tabla `permisos`:
```
{modulo: "expedientes", codigo: "ver",           nombre: "Ver Expedientes"}
{modulo: "expedientes", codigo: "crear",         nombre: "Crear Expediente"}
{modulo: "expedientes", codigo: "editar",        nombre: "Editar Expediente"}
{modulo: "expedientes", codigo: "eliminar",      nombre: "Eliminar Expediente"}
{modulo: "expedientes", codigo: "ver_historial", nombre: "Ver Historial"}
{modulo: "expedientes", codigo: "agregar_nota",  nombre: "Agregar Nota"}
{modulo: "expedientes", codigo: "imprimir",      nombre: "Imprimir Expediente"}
```

**Paso 5.** Usar en el controlador `ExpedienteController`:
```php
public function index()
{
    PermissionHelper::abortUnlessHasPermission('expedientes', 'ver');
    // ...
}

public function store(Request $request)
{
    PermissionHelper::abortUnlessHasPermission('expedientes', 'crear');
    // ...
}

public function imprimirFicha($id)
{
    PermissionHelper::abortUnlessHasPermission('expedientes', 'imprimir');
    // ...
}
```

**Paso 6.** Usar en la vista `expedientes/show.blade.php`:
```blade
@if (canDo('expedientes', 'agregar_nota'))
    <button class="btn btn-secondary">Agregar Nota</button>
@endif

@if (canDo('expedientes', 'imprimir'))
    <button class="btn btn-outline-primary">Imprimir Expediente</button>
@endif

@if (canEdit('expedientes'))
    <a href="{{ route('expedientes.edit', $expediente->id) }}">Editar</a>
@endif
```

**Paso 7.** Agregar el módulo a la plantilla "Doctor" en Configuracion > Plantillas:
- Abrir plantilla "Doctor"
- Marcar: expedientes.ver, expedientes.crear, expedientes.editar,
  expedientes.ver_historial, expedientes.agregar_nota

**Paso 8.** Todos los usuarios con esa plantilla ya tienen acceso automáticamente.

---

## 11. Referencia de funciones

### Funciones globales (disponibles en cualquier archivo)

| Funcion | Descripcion |
|---------|-------------|
| `hasPermission('modulo')` | Tiene cualquier permiso del módulo |
| `hasPermission('modulo', 'codigo')` | Tiene ese permiso específico |
| `canView('modulo')` | Tiene codigo 'view' en el módulo |
| `canCreate('modulo')` | Tiene codigo 'create' en el módulo |
| `canEdit('modulo')` | Tiene codigo 'edit' en el módulo |
| `canDelete('modulo')` | Tiene codigo 'delete' en el módulo |
| `canDo('modulo', 'codigo')` | Tiene ese código (cualquier tipo) |
| `hasFullAccess('modulo')` | Tiene todos los permisos del módulo |
| `hasAnyPermission([...])` | Tiene al menos uno de la lista |
| `hasAllPermissions([...])` | Tiene todos los de la lista |
| `isAdmin()` | Es administrador del sistema |
| `abortUnlessHasPermission('m','c')` | Lanza 403 si no tiene permiso |
| `abortUnlessCanView('modulo')` | Lanza 403 si no puede ver |
| `abortUnlessCanEdit('modulo')` | Lanza 403 si no puede editar |
| `getCurrentUserName()` | Nombre del usuario autenticado |

### Metodos estaticos de PermissionHelper

Los mismos métodos, accesibles con el namespace completo:

```php
use App\Helpers\PermissionHelper;

PermissionHelper::hasPermission($modulo, $codigo);
PermissionHelper::canView($modulo);
PermissionHelper::canCreate($modulo);
PermissionHelper::canEdit($modulo);
PermissionHelper::canDelete($modulo);
PermissionHelper::canDo($modulo, $codigoGranular);
PermissionHelper::hasFullAccess($modulo);
PermissionHelper::hasAnyPermission($arrayDePermisos);
PermissionHelper::hasAllPermissions($arrayDePermisos);
PermissionHelper::isAdmin();
PermissionHelper::getUserPermissions();
PermissionHelper::getPermisosPorModulo();
PermissionHelper::abortUnlessHasPermission($modulo, $codigo, $mensaje);
PermissionHelper::abortUnlessCanView($modulo);
PermissionHelper::abortUnlessCanEdit($modulo);
```

### PermissionService (logica de negocio)

```php
use App\Services\Permissions\PermissionService;

$service = app(PermissionService::class);

// Registrar permiso programáticamente
$service->registerPermission('modulo', 'codigo', 'Nombre', 'descripcion', 'general');

// Verificar si usuario tiene permiso (por ID)
$service->userHasPermission($usuarioId, 'modulo', 'codigo');

// Obtener todos los permisos de un usuario
$service->getUserPermissions($usuarioId);

// Asignar/quitar permisos
$service->assignPermissionToUser($usuario, $permiso, $asignadoPor);
$service->removePermissionFromUser($usuario, $permiso);
$service->assignTemplateToUser($usuario, $plantilla, $asignadoPor);
$service->syncUserPermissions($usuario, $arrayDeIds, $asignadoPor);

// Cache
$service->clearUserPermissionsCache($usuarioId);
$service->clearAllPermissionsCache();
```

---

## 12. Configuracion en el servidor

### Variables .env necesarias

```env
# Developer Panel — TOTP
DEVELOPER_TOTP_SECRET=     # generado con php artisan developer:setup-totp
DEVELOPER_SESSION_HOURS=8  # horas de sesión activa (default: 8)
DEVELOPER_TOTP_WINDOW=1    # tolerancia en pasos de 30s (default: 1)
```

### Primer deploy al servidor

```bash
# 1. Copiar .env y configurar variables de DB, APP_KEY, etc.
# 2. Instalar dependencias
composer install --no-dev --optimize-autoloader

# 3. Correr migraciones
php artisan migrate

# 4. Configurar TOTP en el servidor
php artisan developer:setup-totp
# Agrega una segunda cuenta en Google Authenticator con el nuevo secret.
# Puedes nombrarla "ExpedienteDigital - Produccion" para distinguirla.

# 5. Limpiar caché
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Usar el mismo secret en local y produccion

Si prefieres usar un solo código de Google Authenticator para ambos ambientes,
copia el valor de `DEVELOPER_TOTP_SECRET` del `.env` local al `.env` del servidor.
No es necesario correr `developer:setup-totp` de nuevo.

### Ruta del Developer Panel

```
Local:       http://localhost/expediente_digital/public/developer
Produccion:  https://tu-dominio.com/developer
```

La ruta no requiere autenticacion de usuario del sistema (no verifica `auth`),
solo el TOTP. Esto permite acceso incluso si el sistema de login falla.

---

*Fin del manual.*
