# Cache Busting con ?v={{ time() }}

## 📖 ¿Qué es Cache Busting?

El cache busting es una técnica para forzar a los navegadores a descargar la versión más reciente de archivos CSS y JS, en lugar de usar versiones almacenadas en caché.

## 🎯 Implementación

### En archivos Blade/Laravel

```blade
<!-- CSS -->
<link rel="stylesheet" href="{{ asset('assets/css/style.css') }}?v={{ time() }}">

<!-- JavaScript -->
<script src="{{ asset('assets/js/script.js') }}?v={{ time() }}"></script>

<!-- Imágenes (opcional) -->
<img src="{{ asset('assets/images/logo.png') }}?v={{ time() }}" alt="Logo">
```

### ¿Por qué `?v={{ time() }}`?

```php
// time() retorna el timestamp actual en segundos
// Ejemplo: ?v=1708123456

// Cada vez que se carga la página, el valor cambia
// Esto fuerza al navegador a descargar el archivo nuevo
```

## ✅ Ventajas

1. **Desarrollo:**
   - ✅ No necesitas limpiar la caché del navegador manualmente
   - ✅ Los cambios se ven inmediatamente
   - ✅ Sin `Ctrl + F5` constante

2. **Producción:**
   - ✅ Los usuarios siempre ven la versión más reciente
   - ✅ Sin problemas de "estilos rotos" después de deploy
   - ✅ Sin JavaScript obsoleto en caché

3. **Simplicidad:**
   - ✅ No requiere configuración de build
   - ✅ No requiere herramientas adicionales
   - ✅ Funciona con cualquier hosting

## ⚠️ Consideraciones

### Desarrollo vs Producción

**Desarrollo (recomendado):**
```blade
<link rel="stylesheet" href="{{ asset('assets/css/style.css') }}?v={{ time() }}">
```

**Producción (alternativa con hash del archivo):**
```blade
<link rel="stylesheet" href="{{ asset('assets/css/style.css') }}?v={{ filemtime(public_path('assets/css/style.css')) }}">
```

**Producción (con Laravel Mix/Vite):**
```blade
<link rel="stylesheet" href="{{ mix('css/style.css') }}">
<!-- o -->
@vite(['resources/css/app.css'])
```

## 📋 Archivos Actualizados

### Layout Principal
`resources/views/layouts/admin/master.blade.php`

```blade
<!-- CSS con cache busting -->
<link rel="stylesheet" href="{{ asset('assets/vendor/bootstrap/bootstrap.min.css') }}?v={{ time() }}">
<link rel="stylesheet" href="{{ asset('assets/css/custom-font-size.css') }}?v={{ time() }}">

<!-- JS con cache busting -->
<script src="{{ asset('assets/js/jquery-3.6.3.min.js') }}?v={{ time() }}"></script>
<script src="{{ asset('assets/js/script.js') }}?v={{ time() }}"></script>
```

### Módulo Settings
`resources/views/modules/settings/index.blade.php`

```blade
@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/modules/settings/css/settings.module.css') }}?v={{ time() }}">
@endpush

@push('scripts')
    <script src="{{ asset('assets/modules/settings/js/settings.module.js') }}?v={{ time() }}"></script>
@endpush
```

## 🔍 ¿Cómo Funciona?

### Sin Cache Busting:
```
http://localhost/expediente_digital/public/assets/css/style.css
<!-- Navegador guarda en caché -->
<!-- Después de cambios, el navegador usa la versión vieja -->
```

### Con Cache Busting:
```
http://localhost/expediente_digital/public/assets/css/style.css?v=1708123456
<!-- Navegador ve URL diferente -->
<!-- Descarga nueva versión -->
```

## 📊 Ejemplo de Uso

### En un Componente Blade

```blade
@props(['type' => 'info'])

<div class="alert alert-{{ $type }}">
    {{ $slot }}
</div>

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/components/alert.css') }}?v={{ time() }}">
@endpush

@push('scripts')
    <script src="{{ asset('assets/components/alert.js') }}?v={{ time() }}"></script>
@endpush
```

### En una Vista de Módulo

```blade
@extends('layouts.admin.master')

@section('content')
<div class="dashboard">
    <!-- Contenido -->
</div>
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/modules/dashboard/style.css') }}?v={{ time() }}">
    <link rel="stylesheet" href="{{ asset('assets/modules/dashboard/charts.css') }}?v={{ time() }}">
@endpush

@push('scripts')
    <script src="{{ asset('assets/modules/dashboard/charts.js') }}?v={{ time() }}"></script>
    <script src="{{ asset('assets/modules/dashboard/widgets.js') }}?v={{ time() }}"></script>
@endpush
```

## 🎯 Mejores Prácticas

### ✅ HACER:
- Usar `?v={{ time() }}` en todos los assets CSS y JS
- Incluir en layouts principales
- Incluir en componentes que tengan assets propios
- Usar en desarrollo y producción

### ❌ NO HACER:
- No usar en imágenes estáticas (logos, iconos)
- No usar en fuentes de Google Fonts
- No usar en CDNs externos (jQuery CDN, Bootstrap CDN)

## 📝 Nota de Rendimiento

**Pregunta frecuente:** ¿`time()` afecta el rendimiento?

**Respuesta:** No significativamente.
- `time()` es una función nativa de PHP muy rápida
- El overhead es mínimo (< 0.001ms por llamada)
- El beneficio de evitar caché obsoleta supera por mucho el costo

**Alternativa para producción de alto tráfico:**
```blade
<!-- Usar timestamp del archivo -->
?v={{ filemtime(public_path('assets/css/style.css')) }}

<!-- O versión estática en .env -->
?v={{ env('APP_VERSION', '1.0.0') }}
```

---

**Convención establecida** ✅ - Todos los assets usan cache busting.
