# Módulo de Configuración - Expediente Digital

## 📖 Descripción

Módulo de configuración de empresa y gestión de sucursales desarrollado con arquitectura modular basada en componentes, siguiendo los principios SOLID, DRY y Open/Closed.

## 🎯 Características

- ✅ **Arquitectura Modular**: Código organizado en componentes reutilizables
- ✅ **Principios SOLID**: Cada clase tiene una única responsabilidad
- ✅ **DRY**: Funciones globales reutilizables en GlobalHelper
- ✅ **Vistas Limpias**: Máximo 300 líneas por vista
- ✅ **Componentes Reutilizables**: UI components para toda la aplicación
- ✅ **Inyección de Dependencias**: Services inyectados en controladores
- ✅ **Validación**: Request classes para validación de datos
- ✅ **Assets Organizados**: CSS y JS por módulo y componente

## 🚀 Instalación

```powershell
# En Windows/PowerShell
cd D:\wwwLaragon\expediente_digital

# Registar autoload
composer dump-autoload

# Limpiar caché
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

# Ejecutar migraciones
php artisan migrate
```

## 📂 Estructura

```
Settings Module/
├── Controllers/
│   ├── SettingsController      # Orquestador
│   ├── EmpresaController       # Empresa
│   └── SucursalesController    # Sucursales
├── Services/
│   ├── EmpresaService          # Lógica empresa
│   └── SucursalService         # Lógica sucursales
├── Requests/
│   ├── UpdateEmpresaRequest
│   ├── StoreSucursalRequest
│   └── UpdateSucursalRequest
├── Helpers/
│   └── GlobalHelper            # Funciones globales
├── Components/
│   ├── ui/                     # UI genérico
│   └── settings/               # Settings específico
└── Modules/
    └── settings/               # Vistas principales
```

## 🔌 Uso de Componentes

### UI Components

```blade
<!-- Botón -->
<x-ui.button variant="primary" icon="ti ti-check">
    Guardar
</x-ui.button>

<!-- Input -->
<x-ui.input 
    name="email" 
    label="Correo"
    type="email"
    :required="true"
/>

<!-- Toggle -->
<x-ui.toggle name="activo" label="Activo" />
```

### Settings Components

```blade
<!-- Breadcrumb -->
<x-settings.breadcrumb 
    :items="['Configuración']"
    home-url="{{ route('dashboard') }}"
/>

<!-- Navigation -->
<x-settings.navigation 
    :tabs="[
        ['id' => 'empresa', 'label' => 'Empresa', 'icon' => 'ti ti-building'],
        ['id' => 'sucursales', 'label' => 'Sucursales', 'badge' => 3],
    ]"
    active-tab="empresa"
/>

<!-- Alert -->
<x-settings.alert-message type="success" :message="'Guardado'" />
```

## 🛣️ Rutas

| Método | URL | Nombre | Descripción |
|--------|-----|--------|-------------|
| GET | `/settings` | `settings.index` | Vista principal |
| GET | `/settings/empresa` | `settings.empresa.edit` | Editar empresa |
| PUT | `/settings/empresa` | `settings.empresa.update` | Actualizar empresa |
| GET | `/settings/sucursales` | `settings.sucursales.index` | Lista sucursales |
| POST | `/settings/sucursal` | `settings.sucursal.store` | Crear sucursal |
| PUT | `/settings/sucursal/{id}` | `settings.sucursal.update` | Actualizar sucursal |
| DELETE | `/settings/sucursal/{id}` | `settings.sucursal.destroy` | Eliminar sucursal |
| GET | `/settings/sucursal/{id}/toggle` | `settings.sucursal.toggle` | Cambiar estado |

## 📝 Ejemplos

### Controlador

```php
class EmpresaController extends Controller
{
    public function __construct(EmpresaService $service)
    {
        $this->service = $service;
    }

    public function update(UpdateEmpresaRequest $request)
    {
        $this->service->updateEmpresa(
            $request->validated(),
            $request->file('logo')
        );
        
        return back()->with('success', 'Actualizado correctamente');
    }
}
```

### Service

```php
class EmpresaService
{
    public function updateEmpresa(array $data, $logo)
    {
        $empresa = $this->getOrCreateDefault();
        $empresa->update($data);
        
        if ($logo) {
            $this->uploadLogo($empresa, $logo);
        }
        
        return $empresa->fresh();
    }
}
```

### Vista Modular

```blade
@extends('layouts.admin.master')

@section('content')
    <x-settings.breadcrumb :items="['Configuración']" />
    
    <div class="row">
        <div class="col-lg-3">
            <x-settings.navigation :tabs="$tabs" />
        </div>
        
        <div class="col-lg-9">
            <x-settings.tabs.empresa.form :empresa="$empresa" />
        </div>
    </div>
@endsection
```

## 🎨 Estilos

Los estilos están organizados en:

- **Módulo**: `public/assets/modules/settings/css/settings.module.css`
- **Componentes**: `public/assets/components/settings/tabs/empresa/css/`

### Variables CSS

```css
:root {
    --settings-primary: #667eea;
    --settings-radius: 12px;
    --settings-shadow: 0 2px 12px rgba(0,0,0,0.05);
}
```

## 🧪 JavaScript

```javascript
// Módulo principal
SettingsModule.init();

// Notificaciones
SettingsModule.showToast('Guardado correctamente', 'success');

// Confirmación
const confirmed = await SettingsModule.confirm('¿Eliminar?');

// Sucursales
SucursalesModule.editSucursal(sucursal);
SucursalesModule.toggleSucursal(id);
```

## 📊 Principios Aplicados

### SOLID

1. **Single Responsibility**: Cada controlador/service tiene una función
2. **Open/Closed**: Componentes extensibles sin modificar código
3. **Liskov Substitution**: Componentes intercambiables
4. **Interface Segregation**: Interfaces pequeñas
5. **Dependency Inversion**: Inyección de dependencias

### DRY

- GlobalHelper para funciones comunes
- Componentes UI reutilizables
- Services para lógica compartida

### Límites de Código

| Tipo | Máximo | Ideal |
|------|--------|-------|
| Vistas | 300 líneas | < 150 |
| Componentes | 100 líneas | < 80 |
| Controladores | 150 líneas | < 100 |
| Services | 200 líneas | < 150 |

## 🔒 Seguridad

- Validación en Request classes
- Autorización en controladores
- Sanitización en GlobalHelper
- CSRF protection en formularios

## 📄 Licencia

MIT License - Expediente Digital

## 👥 Contribución

1. Crear rama feature
2. Seguir principios SOLID
3. Mantener vistas < 300 líneas
4. Usar componentes existentes
5. Crear tests si aplica

---

**Desarrollado con ❤️ siguiendo mejores prácticas**
