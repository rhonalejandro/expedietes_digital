# Componentes Modales - Documentación

## 📖 Descripción

Componentes modales personalizados que reemplazan las alertas y confirmaciones nativas del navegador, proporcionando una experiencia de usuario más consistente y atractiva.

## 🎯 Componentes Disponibles

### 1. Modal Alert (`<x-ui.modal-alert>`)

Reemplaza la función `alert()` nativa del navegador.

**Uso básico:**
```blade
<x-ui.modal-alert type="info" title="Información" />
```

**Tipos disponibles:**
- `info` - Información general (azul)
- `success` - Éxito (verde)
- `warning` - Advertencia (amarillo/naranja)
- `error` - Error (rojo)
- `question` - Pregunta (azul)

**Funciones JavaScript:**
```javascript
// Mostrar alerta
showAlert('Título', 'Mensaje', 'info');

// Ejemplos
showAlert('Éxito', 'Datos guardados correctamente', 'success');
showAlert('Error', 'Ocurrió un problema', 'error');
showAlert('Advertencia', 'Revisa los datos', 'warning');
```

### 2. Modal Confirm (`<x-ui.modal-confirm>`)

Reemplaza la función `confirm()` nativa del navegador.

**Uso básico:**
```blade
<x-ui.modal-confirm 
    title="¿Estás seguro?" 
    confirm-text="Sí, eliminar"
    confirm-variant="danger"
/>
```

**Funciones JavaScript:**
```javascript
// Mostrar confirmación
showConfirm('Título', 'Mensaje', function() {
    // Código a ejecutar si confirma
    console.log('Confirmado!');
});

// Ejemplo con eliminación
showConfirm(
    'Eliminar Sucursal',
    '¿Estás seguro de que deseas eliminar esta sucursal?',
    function() {
        // Enviar formulario o hacer petición AJAX
        window.location.href = '/sucursal/1/delete';
    }
);
```

## 📋 Ejemplos de Uso

### En Vistas Blade

```blade
@extends('layouts.admin.master')

@section('content')
<div class="container-fluid">
    <!-- Botones que usan modales -->
    <button onclick="showAlert('Información', 'Este es un mensaje de prueba')" class="btn btn-info">
        Mostrar Alerta
    </button>
    
    <button onclick="deleteItem(1)" class="btn btn-danger">
        Eliminar
    </button>
</div>

<!-- Incluir componentes modales (una sola vez por página) -->
<x-ui.modal-alert type="info" title="Información" />
<x-ui.modal-alert type="success" title="Éxito" />
<x-ui.modal-alert type="error" title="Error" />
<x-ui.modal-confirm title="¿Estás seguro?" />
@endsection

@push('scripts')
<script>
// Función que usa confirmación
function deleteItem(id) {
    showConfirm(
        'Eliminar Elemento',
        '¿Estás seguro de que deseas eliminar este elemento?',
        function() {
            // El usuario confirmó
            fetch('/items/' + id, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            }).then(() => {
                showAlert('Éxito', 'Elemento eliminado correctamente', 'success');
            });
        }
    );
}
</script>
@endpush
```

### Sobrescribir Alert/Confirm Nativos

Los componentes automáticamente sobrescriben las funciones nativas:

```javascript
// Esto ahora usa el modal personalizado en lugar del nativo
alert('Este mensaje usa el modal personalizado!');

// Esto también usa el modal personalizado
if (confirm('¿Estás seguro?')) {
    console.log('Confirmado con modal personalizado!');
}
```

## 🎨 Personalización

### Modal Alert

```blade
<x-ui.modal-alert 
    type="success"
    title="¡Éxito!"
    message="Operación completada"
    confirm-text="Aceptar"
    cancel-text="Cancelar"
    :show-cancel="false"
/>
```

### Modal Confirm

```blade
<x-ui.modal-confirm 
    title="¿Confirmar acción?"
    message="Esta acción no se puede deshacer"
    confirm-text="Sí, continuar"
    cancel-text="No, cancelar"
    confirm-variant="danger"
/>
```

## 🔧 Funciones JavaScript Globales

### showAlert()

```javascript
showAlert(title, message, type);
```

**Parámetros:**
- `title` (string) - Título del modal
- `message` (string) - Mensaje a mostrar
- `type` (string) - Tipo de alerta: 'info', 'success', 'warning', 'error', 'question'

**Ejemplo:**
```javascript
showAlert('Guardado', 'Los datos se guardaron correctamente', 'success');
```

### showConfirm()

```javascript
showConfirm(title, message, callback);
```

**Parámetros:**
- `title` (string) - Título del modal
- `message` (string) - Mensaje a mostrar
- `callback` (function) - Función a ejecutar si el usuario confirma

**Ejemplo:**
```javascript
showConfirm(
    'Eliminar',
    '¿Estás seguro?',
    function() {
        console.log('Elemento eliminado');
    }
);
```

## 📝 Casos de Uso Comunes

### 1. Eliminar Registro

```blade
<button onclick="deleteSucursal({{ $id }})" class="btn btn-danger">
    <i class="ti ti-trash"></i> Eliminar
</button>

@push('scripts')
<script>
function deleteSucursal(id) {
    showConfirm(
        'Eliminar Sucursal',
        '¿Estás seguro de que deseas eliminar esta sucursal? Esta acción no se puede deshacer.',
        function() {
            // Crear formulario y enviar
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '/settings/sucursal/' + id;
            
            form.innerHTML = `
                @csrf
                @method('DELETE')
            `;
            
            document.body.appendChild(form);
            form.submit();
        }
    );
}
</script>
@endpush
```

### 2. Cambiar Estado

```javascript
function toggleEstado(id, estadoActual) {
    const accion = estadoActual ? 'desactivar' : 'activar';
    
    showConfirm(
        'Cambiar Estado',
        `¿Estás seguro de que deseas ${accion} este registro?`,
        function() {
            window.location.href = '/registro/' + id + '/toggle';
        }
    );
}
```

### 3. Guardar con Confirmación

```javascript
function guardarConConfirmacion() {
    showConfirm(
        'Confirmar Guardado',
        '¿Estás seguro de que deseas guardar los cambios?',
        function() {
            document.getElementById('miFormulario').submit();
        }
    );
}
```

## ⚠️ Consideraciones Importantes

1. **Una sola instancia por tipo**: Incluye cada tipo de modal una sola vez por página
2. **Alpine.js requerido**: Los modales usan Alpine.js para la reactividad
3. **Bootstrap Modal**: Usa Bootstrap 5 para el renderizado
4. **CSRF Token**: Necesario para peticiones POST/DELETE

## 🎯 Mejores Prácticas

### ✅ HACER:
- Incluir los modales al final de la vista, antes de `@endsection`
- Usar mensajes claros y descriptivos
- Proporcionar feedback después de acciones
- Validar antes de mostrar confirmación

### ❌ NO HACER:
- Incluir múltiples instancias del mismo modal
- Usar para mensajes muy largos
- Reemplazar validaciones de formulario
- Usar en lugar de notificaciones toast

## 📦 Dependencias

- **Alpine.js** v3.x.x (incluido vía CDN)
- **Bootstrap** 5.x (modales)
- **Tabler Icons** (íconos)

---

**Componentes listos para usar** ✅ - Reemplazan alertas nativas con modales personalizados.
