# 🚀 INSTRUCCIONES DE INICIO - Expediente Digital

## ⚠️ IMPORTANTE: Node.js Desactualizado

Tu versión de Node.js es antigua. **Vite 7 requiere Node.js 20+**.

### Verifica tu versión:
```powershell
node --version
```

### Actualiza Node.js:
1. Ve a https://nodejs.org/
2. Descarga la versión **LTS 20.x** o **22.x**
3. Instala y reinicia la terminal

---

## ✅ SOLUCIÓN TEMPORAL (Funciona YA)

El sistema ya puede funcionar **sin Vite** usando el CSS básico incluido.

### Pasos:

### 1. Ejecutar seeders (si no lo has hecho):
```powershell
php artisan db:seed
```

### 2. Acceder al login:
```
http://localhost:8000/login
```

**Credenciales:**
- Email: `admin@krom-soft.com`
- Contraseña: `Rhonald16*`

---

## 🔧 PARA DESARROLLO (Recomendado)

### Una vez actualizado Node.js:

```powershell
# Eliminar node_modules antiguo
Remove-Item -Recurse -Force node_modules
Remove-Item package-lock.json

# Restaurar package.json original (si lo modificaste)
# O usar el actualizado que ya tiene versiones compatibles

# Instalar dependencias
npm install

# Compilar assets
npm run build

# O en modo desarrollo (con hot-reload)
npm run dev
```

---

## 📁 ESTADO ACTUAL

### ✅ Funciona sin Vite:
- Login: http://localhost:8000/login
- Dashboard: http://localhost:8000/dashboard
- CSS básico incluido en `public/assets/css/style.css`

### ⚠️ Limitaciones:
- Estilos básicos (no incluye todos los estilos del template)
- No hay hot-reload en desarrollo
- SCSS sin compilar

---

## 🎯 PRÓXIMOS PASOS

1. **Actualiza Node.js a versión 20+**
2. Ejecuta `npm install && npm run build`
3. El sistema usará todos los estilos del template

---

## 📊 COMANDOS ÚTILES

```powershell
# Ver logs en tiempo real
php artisan pail

# Limpiar caché
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# Regenerar autoload
composer dump-autoload

# Ver logs de errores
Get-Content storage/logs/laravel.log -Tail 50
```

---

## 🔐 CREDENCIALES

```
Email: admin@krom-soft.com
Contraseña: Rhonald16*
```

---

## 🆘 SOPORTE

Si algo no funciona:

1. **Error de Node.js**: Actualiza a Node.js 20+
2. **Error de base de datos**: Ejecuta `php artisan migrate:fresh --seed`
3. **Error de assets**: Limpia caché `php artisan cache:clear`
4. **Error de vistas**: `php artisan view:clear`

---

**¡El sistema funciona! Pero para producción, actualiza Node.js y compila los assets.** 🚀
