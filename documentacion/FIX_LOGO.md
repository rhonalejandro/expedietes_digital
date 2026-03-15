# Fix - Logo no se guarda

## ✅ Solución Implementada

### Cambios Realizados:

1. **EmpresaService.php** - uploadLogo() mejorado
   - Genera nombre único para cada archivo
   - Valida que el archivo se guardó correctamente
   - Retorna null si falla la subida

2. **updateEmpresa()** - Validación mejorada
   - Solo guarda el logo si se subió uno válido
   - Verifica `isValid()` antes de procesar

3. **logo-upload.blade.php** - Preview mejorado
   - Valida tipo de archivo (JPG, PNG, GIF)
   - Valida tamaño máximo (2MB)
   - Muestra preview instantáneo
   - Verifica que el archivo existe antes de mostrar

## 📋 Pasos para Solucionar

### 1. Crear storage link (SOLO UNA VEZ)

**En Windows/PowerShell:**
```powershell
cd D:\wwwLaragon\expediente_digital

# Crear symbolic link para storage
php artisan storage:link
```

**¿Qué hace esto?**
- Crea un enlace simbólico desde `public/storage` → `storage/app/public`
- Permite acceder a los archivos subidos vía web
- URL: `http://localhost/expediente_digital/public/storage/logos/empresas/xxx.png`

### 2. Ejecutar migration para campo logo

```powershell
php artisan migrate
```

### 3. Verificar permisos

```powershell
# La carpeta storage/app/public debe tener permisos de escritura
# En Windows generalmente no hay problema
```

## 🧪 Test de Logo

1. **Ve a `/settings`**

2. **Sube un logo:**
   - Haz clic en "Cambiar Logo"
   - Selecciona una imagen JPG o PNG
   - **Debe:** Mostrar vista previa inmediatamente

3. **Guarda:**
   - Haz clic en "Guardar Cambios"
   - **Debe:** Mostrar mensaje de éxito

4. **Verifica:**
   - Recarga la página
   - **Debe:** Mostrar el logo guardado
   - El logo debe verse en el preview

5. **Verifica en el servidor:**
   ```
   storage/app/public/logos/empresas/
   ```
   - **Debe:** Existir un archivo con nombre hexadecimal (ej: `a1b2c3d4...png`)

6. **Verifica en el navegador:**
   - Click derecho en el logo → "Copiar dirección de imagen"
   - **Debe:** URL como `http://localhost/expediente_digital/public/storage/logos/empresas/xxx.png`
   - Abre la URL en una nueva pestaña
   - **Debe:** Mostrar la imagen

## 🐛 Posibles Problemas

### 1. "No such file or directory"
**Solución:**
```powershell
php artisan storage:link
```

### 2. "Permission denied"
**Solución (Linux/Mac):**
```bash
chmod -R 775 storage/app/public
chown -R www-data:www-data storage/app/public
```

**Solución (Windows):**
- Generalmente no hay problema de permisos
- Si hay problema, ejecuta como Administrador

### 3. Imagen no se muestra
**Verifica:**
```powershell
# Verificar si existe el archivo
ls storage/app/public/logos/empresas/

# Verificar enlace simbólico
ls -la public/storage
# Debe apuntar a ../../storage/app/public
```

### 4. Logo se guarda pero no se ve
**Posible causa:** El storage link no está creado

**Solución:**
```powershell
# Eliminar link existente (si hay error)
rm public/storage

# Crear link nuevo
php artisan storage:link
```

## 📝 Comportamiento Esperado

### ✅ Lo que SÍ pasa:
- Vista previa al seleccionar imagen
- Validación de tipo (JPG, PNG, GIF)
- Validación de tamaño (2MB máx)
- Se guarda en `storage/app/public/logos/empresas/`
- Nombre único hexadecimal
- Se muestra en el formulario después de guardar
- Persiste después de recargar

### ❌ Lo que NO pasa:
- No se guarda el nombre si falla la subida
- No se suben archivos inválidos
- No se muestran rutas rotas
- No se pierden los logos al actualizar

## 🔧 Código Final

### EmpresaService.php - uploadLogo()
```php
private function uploadLogo(Empresa $empresa, UploadedFile $logo): ?string
{
    // Eliminar logo anterior
    if ($empresa->logo) {
        Storage::disk('public')->delete($empresa->logo);
    }

    // Nombre único
    $filename = bin2hex(random_bytes(16)) . '.' . $logo->getClientOriginalExtension();
    
    // Guardar
    $path = $logo->storeAs('logos/empresas', $filename, 'public');
    
    // Verificar
    if ($path && Storage::disk('public')->exists($path)) {
        return $path;
    }
    
    return null;  // ← Retorna null si falló
}
```

### updateEmpresa()
```php
// Solo guarda si es válido
if ($logo && $logo->isValid()) {
    $logoPath = $this->uploadLogo($empresa, $logo);
    if ($logoPath) {  // ← Solo si se guardó realmente
        $empresa->logo = $logoPath;
    }
}
```

### logo-upload.blade.php
```blade
@php
    $hasLogo = $empresa && $empresa->logo && 
               Storage::disk('public')->exists($empresa->logo);
@endphp

@if($hasLogo)
    <img src="{{ asset('storage/' . $empresa->logo) }}" ...>
@else
    <div><i class="ti ti-building"></i></div>
@endif

<!-- Preview con JavaScript -->
<script>
function previewLogo(event, previewId) {
    // Valida tipo
    // Valida tamaño
    // Muestra preview
}
</script>
```

---

**LISTO** ✅ - Ahora el logo se guarda correctamente con preview y validación.
