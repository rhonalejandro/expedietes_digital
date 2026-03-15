# Actualización de Permisos del Administrador

**Desarrollo - Permisos de Usuarios - Seeders - 2026-02-18**

---

## 📋 Resumen de Cambios

### Archivos Modificados
1. `database/seeders/UsuarioSeeder.php` - Actualizado para asignar todos los permisos
2. `database/seeders/PermissionsSeeder.php` - Agregado rol Super Administrador
3. `database/seeders/UpdateAdminPermissionsSeeder.php` - NUEVO seeder de actualización

---

## 🚀 Cómo Ejecutar la Actualización

### Opción 1: Seeder de Actualización (Recomendado)

Si ya tienes el sistema en producción y solo quieres actualizar tu usuario:

```bash
php artisan db:seed --class=UpdateAdminPermissionsSeeder
```

Este seeder:
- ✅ Busca tu usuario `admin@krom-soft.com`
- ✅ Le asigna el rol "Super Administrador"
- ✅ Le asigna la plantilla "Administrador"
- ✅ Le asigna TODOS los permisos directamente
- ✅ Muestra un resumen de lo realizado

---

### Opción 2: Re-hacer todos los seeders

Si quieres reiniciar desde cero:

```bash
# Opción A: Solo seeders específicos
php artisan db:seed --class=PermissionsSeeder
php artisan db:seed --class=UsuarioSeeder
php artisan db:seed --class=UpdateAdminPermissionsSeeder

# Opción B: Todos los seeders (incluye fresh)
php artisan migrate:fresh --seed
```

---

## 📊 Lo que Tendrás Después de Ejecutar

### Tu Usuario
```
Email: admin@krom-soft.com
Password: Rhonald16*
Rol: Super Administrador
```

### Permisos
- ✅ **Todos los permisos generales** (view, create, edit, delete) de todos los módulos
- ✅ **Todos los permisos granulares** (taxes, pricing, sign_digital, etc.)
- ✅ **Acceso completo** a:
  - Configuración
  - Usuarios
  - Permisos
  - Clientes
  - Doctores
  - Citas
  - Casos
  - Expedientes
  - Servicios
  - Productos
  - Pagos
  - Reportes
  - Sucursales

### Roles y Plantillas
- **Rol:** Super Administrador
- **Plantilla:** Administrador
- **Permisos directos:** Todos los permisos activos
- **Permisos por plantilla:** Todos los permisos
- **Total:** Permiso completo en todo el sistema

---

## 🔍 Verificar Permisos

Puedes verificar tus permisos en la base de datos:

```sql
-- Ver roles del admin
SELECT u.email, r.nombre as rol 
FROM usuarios u
JOIN usuario_rol ur ON u.id = ur.usuario_id
JOIN roles r ON ur.rol_id = r.id
WHERE u.email = 'admin@krom-soft.com';

-- Ver permisos directos del admin
SELECT u.email, p.modulo, p.codigo, p.nombre
FROM usuarios u
JOIN usuario_permiso up ON u.id = up.usuario_id
JOIN permisos p ON up.permiso_id = p.id
WHERE u.email = 'admin@krom-soft.com'
ORDER BY p.modulo, p.codigo;

-- Contar permisos
SELECT COUNT(*) as total_permisos
FROM usuarios u
JOIN usuario_permiso up ON u.id = up.usuario_id
WHERE u.email = 'admin@krom-soft.com';
```

---

## 🎯 Comandos Útiles

### Ver permisos en consola (Tinker)
```bash
php artisan tinker
```

```php
$admin = App\Models\Usuario::where('email', 'admin@krom-soft.com')->first();

// Ver permisos
$admin->getAllPermisos()->count();  // Total de permisos
$admin->getAllPermisos()->groupBy('modulo');  // Por módulo

// Verificar permisos específicos
$admin->hasPermission('clients');        // true
$admin->canView('clients');              // true
$admin->canEdit('products');             // true
$admin->canDo('products', 'taxes');      // true
$admin->hasFullAccess('medical_records'); // true
$admin->isAdmin();                       // true
```

---

## 🛡️ Seguridad

### Tu Password
- **Actual:** `Rhonald16*`
- **Recomendación:** Cámbialo después del primer login

### Cambiar Password
Desde la UI:
1. Ve a tu perfil
2. Click en "Cambiar contraseña"
3. Ingresa la nueva contraseña
4. Guarda

Desde código (Tinker):
```bash
php artisan tinker
```

```php
$admin = App\Models\Usuario::where('email', 'admin@krom-soft.com')->first();
$admin->password = Hash::make('TuNuevoPassword123*');
$admin->save();
```

---

## 📝 Notas Importantes

1. **No elimines el usuario admin** - Es el super administrador del sistema
2. **No elimines la plantilla Administrador** - Tiene todos los permisos
3. **No elimines el rol Super Administrador** - Es tu acceso principal
4. **Puedes crear otros usuarios** - Asígnales roles con menos permisos

---

## ✅ Output Esperado

Al ejecutar el seeder verás algo como:

```
🔐 Actualizando permisos del administrador...
   ✓ Usuario encontrado: admin@krom-soft.com
   ✓ Rol 'Super Administrador' asignado
   ✓ Plantilla 'Administrador' creada con 67 permisos
   ✓ Plantilla asignada al rol Super Administrador
   ✓ 67 permisos asignados directamente al usuario
   ✓ Total de permisos del usuario: 67
✅ Usuario admin actualizado exitosamente como SUPER ADMIN

📋 Datos de acceso:
   Email: admin@krom-soft.com
   Password: Rhonald16*

🎯 Permisos:
   - Rol: Super Administrador
   - Plantilla: Administrador (todos los permisos)
   - Permisos directos: 67 permisos
   - Total efectivo: 67 permisos
```

---

**¡Listo! Ya eres Super Admin con todos los permisos del sistema.** 🎉
