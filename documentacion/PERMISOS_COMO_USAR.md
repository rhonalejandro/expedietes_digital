# Sistema de Permisos - Guía de Uso

**Desarrollo - Permisos de Usuarios - 2026-02-18**

---

## 📋 Resumen de lo Implementado

### Archivos Creados

#### Migraciones
1. `2026_02_18_000001_create_permisos_table.php` - Tabla de permisos
2. `2026_02_18_000002_create_usuario_permiso_table.php` - Relación usuario-permiso
3. `2026_02_18_000003_create_plantillas_permisos_table.php` - Plantillas de permisos
4. `2026_02_18_000004_create_plantilla_permiso_detalle_table.php` - Detalle de plantillas
5. `2026_02_18_000005_update_roles_table_for_permissions.php` - Actualización de roles

#### Modelos
1. `app/Models/Permiso.php` - Modelo de permisos
2. `app/Models/PlantillaPermiso.php` - Modelo de plantillas
3. `app/Models/Usuario.php` - Actualizado con métodos de permisos
4. `app/Models/Rol.php` - Actualizado con relación a plantillas

#### Servicios
1. `app/Services/Permissions/PermissionService.php` - Lógica de negocio

#### Helpers
1. `app/Helpers/PermissionHelper.php` - Clase helper
2. `app/Helpers/PermissionFunctions.php` - Funciones globales

#### Providers
1. `app/Providers/PermissionServiceProvider.php` - Registro de directivas Blade

#### Middleware
1. `app/Http/Middleware/CheckPermission.php` - Verificar permisos en rutas
2. `app/Http/Middleware/CheckModuleAccess.php` - Verificar acceso a módulos

#### Seeders
1. `database/seeders/PermissionsSeeder.php` - Seeders de permisos base

#### Configuración
1. `composer.json` - Actualizado con autoload de funciones

---

## 🚀 Cómo Activar el Sistema

### Paso 1: Ejecutar Migraciones
```bash
php artisan migrate
```

### Paso 2: Registar Autoload
```bash
composer dump-autoload
```

### Paso 3: Ejecutar Seeders
```bash
php artisan db:seed --class=PermissionsSeeder
```

---

## 📖 Uso en Controladores

### Verificar permisos antes de acciones
```php
use App\Http\Controllers\Controller;
use App\Models\Client;

class ClientController extends Controller
{
    public function index()
    {
        // Opción 1: Usando helper global
        if (!canView('clients')) {
            abort(403, 'No tienes permiso para ver clientes');
        }
        
        // Opción 2: Usando el usuario actual
        if (!auth()->user()->canView('clients')) {
            abort(403);
        }
        
        $clients = Client::all();
        return view('clients.index', compact('clients'));
    }
    
    public function create()
    {
        if (!canCreate('clients')) {
            abort(403);
        }
        
        return view('clients.create');
    }
    
    public function edit($id)
    {
        if (!canEdit('clients')) {
            abort(403);
        }
        
        $client = Client::findOrFail($id);
        return view('clients.edit', compact('client'));
    }
    
    public function destroy($id)
    {
        if (!canDelete('clients')) {
            abort(403);
        }
        
        $client = Client::findOrFail($id);
        $client->delete();
        
        return redirect()->route('clients.index');
    }
    
    public function manageTaxes()
    {
        // Permiso granular específico
        if (!canDo('products', 'taxes')) {
            abort(403, 'No puedes gestionar impuestos');
        }
        
        return view('products.taxes');
    }
}
```

---

## 🛣️ Uso en Rutas

### Proteger rutas con middleware
```php
// routes/web.php

// Grupo de rutas con permiso específico
Route::middleware(['auth', 'permission:clients.view'])->group(function () {
    Route::get('/clients', [ClientController::class, 'index'])->name('clients.index');
});

Route::middleware(['auth', 'permission:clients.create'])->group(function () {
    Route::get('/clients/create', [ClientController::class, 'create'])->name('clients.create');
    Route::post('/clients', [ClientController::class, 'store'])->name('clients.store');
});

Route::middleware(['auth', 'permission:clients.edit'])->group(function () {
    Route::get('/clients/{id}/edit', [ClientController::class, 'edit'])->name('clients.edit');
    Route::put('/clients/{id}', [ClientController::class, 'update'])->name('clients.update');
});

Route::middleware(['auth', 'permission:clients.delete'])->group(function () {
    Route::delete('/clients/{id}', [ClientController::class, 'destroy'])->name('clients.destroy');
});

// Usar module.access para acceso básico a un módulo
Route::middleware(['auth', 'module.access:clients'])->group(function () {
    // Todas estas rutas requieren al menos permiso view de clients
    Route::get('/clients', [ClientController::class, 'index']);
    Route::get('/clients/{id}', [ClientController::class, 'show']);
});
```

---

## 🎨 Uso en Vistas Blade

### Directivas condicionales
```blade
{{-- Mostrar enlace solo si puede ver clientes --}}
@canView('clients')
    <a href="{{ route('clients.index') }}" class="nav-link">
        <i class="ti ti-users"></i> Clientes
    </a>
@endcanView

{{-- Mostrar botón de crear solo si puede crear --}}
@canCreate('clients')
    <a href="{{ route('clients.create') }}" class="btn btn-primary">
        <i class="ti ti-plus"></i> Nuevo Cliente
    </a>
@endcanCreate

{{-- Mostrar botón de editar solo si puede editar --}}
@canEdit('clients')
    <a href="{{ route('clients.edit', $client->id) }}" class="btn btn-sm btn-outline-primary">
        <i class="ti ti-edit"></i> Editar
    </a>
@endcanEdit

{{-- Mostrar botón de eliminar solo si puede eliminar --}}
@canDelete('clients')
    <form action="{{ route('clients.destroy', $client->id) }}" method="POST">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-sm btn-outline-danger">
            <i class="ti ti-trash"></i> Eliminar
        </button>
    </form>
@endcanDelete

{{-- Permiso granular --}}
@canDo('products', 'taxes')
    <a href="{{ route('products.taxes') }}">Gestionar Impuestos</a>
@endcanDo

@canDo('medical_records', 'sign_digital')
    <button class="btn btn-success">Firmar Digitalmente</button>
@endcanDo

{{-- Verificar si tiene algún permiso --}}
@hasPermission('clients')
    <p>Tiene al menos un permiso de clientes</p>
@endhasPermission

{{-- Verificar si tiene acceso completo --}}
@hasFullAccess('clients')
    <p>Tiene control total sobre clientes</p>
@endhasFullAccess

{{-- Verificar si es admin --}}
@isAdmin
    <div class="admin-panel">Panel de Administrador</div>
@endisAdmin
```

---

## 🔧 Funciones Helpers Disponibles

```php
// Verificar permisos
hasPermission('clients')                    // Tiene algún permiso de clientes
hasPermission('clients', 'view')            // Tiene permiso específico
canView('clients')                          // Puede ver clientes
canCreate('clients')                        // Puede crear clientes
canEdit('clients')                          // Puede editar clientes
canDelete('clients')                        // Puede eliminar clientes
canDo('products', 'taxes')                  // Permiso granular
hasFullAccess('clients')                    // Tiene todos los permisos CRUD
isAdmin()                                   // Es administrador

// Múltiples permisos
hasAnyPermission([                          // Tiene ALGUNO de estos
    ['clients', 'view'],
    ['products', 'view']
])

hasAllPermissions([                         // Tiene TODOS estos
    ['clients', 'view'],
    ['clients', 'edit']
])

// Obtener información
getUserPermissions()                        // Todos los permisos del usuario
getPermisosPorModulo()                      // Permisos agrupados por módulo
getCurrentUserName()                        // Nombre del usuario actual
isCurrentUser($userId)                      // Es el usuario especificado

// Abortar si no tiene permiso
abortUnlessHasPermission('clients', 'edit')
abortUnlessCanView('clients')
abortUnlessCanEdit('clients')
```

---

## 📊 Asignación de Permisos

### Desde código (PermissionService)
```php
use App\Services\Permissions\PermissionService;
use App\Models\Usuario;
use App\Models\Permiso;

$permissionService = app(PermissionService::class);

// Asignar permiso individual a usuario
$usuario = Usuario::find(1);
$permiso = Permiso::where('modulo', 'clients')->where('codigo', 'view')->first();

$permissionService->assignPermissionToUser(
    $usuario,
    $permiso,
    auth()->user(),           // Quién asigna
    'Permiso para ver clientes'
);

// Asignar plantilla completa
$plantilla = PlantillaPermiso::where('nombre', 'Recepcionista')->first();
$permissionService->assignTemplateToUser($usuario, $plantilla, auth()->user());

// Sincronizar permisos (reemplaza todos los existentes)
$permisosIds = [1, 2, 3, 4, 5];
$permissionService->syncUserPermissions($usuario, $permisosIds, auth()->user());
```

---

## 🎯 Módulos y Permisos Registrados

### Permisos Generales (CRUD)
- `settings` - Configuración
- `users` - Usuarios
- `permissions` - Permisos
- `clients` - Clientes/Pacientes
- `doctors` - Doctores
- `appointments` - Citas
- `cases` - Casos
- `medical_records` - Expedientes
- `services` - Servicios
- `products` - Productos
- `payments` - Pagos
- `reports` - Reportes
- `branches` - Sucursales

### Permisos Granulares (Especiales)
- `users.permissions` - Gestionar permisos
- `users.roles` - Asignar roles
- `products.taxes` - Impuestos
- `products.accounting` - Cuentas contables
- `products.pricing` - Precios
- `medical_records.sign_digital` - Firmar digitalmente
- `medical_records.histories` - Ver historial
- `appointments.reschedule` - Reprogramar citas
- `cases.close_case` - Cerrar casos

---

## 📝 Próximos Pasos Sugeridos

1. **Crear UI de gestión de permisos** - Vista para administrar permisos y asignarlos
2. **Integrar con módulos existentes** - Agregar verificaciones en Clientes, Doctores, etc.
3. **Crear controladores de permisos** - CRUD para gestión de permisos desde la UI
4. **Agregar auditoría** - Log de cambios de permisos
5. **Testing** - Tests unitarios y de integración

---

**Fin del documento**
