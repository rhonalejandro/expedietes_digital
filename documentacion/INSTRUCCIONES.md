# 📋 Instrucciones para Iniciar el Sistema

## 🚀 Primeros Pasos

### 1. Ejecutar Migraciones y Seeders

Desde **Laragon** (Windows), abre la terminal en la carpeta del proyecto y ejecuta:

```bash
php artisan migrate:fresh --seed
```

Esto creará:
- Todas las tablas de la base de datos
- Usuario administrador por defecto:
  - **Email:** `admin@krom-soft.com`
  - **Contraseña:** `Rhonald16*`

### 2. Compilar Assets

```bash
npm install
npm run build
```

### 3. Iniciar el Servidor de Desarrollo

```bash
php artisan serve
```

O usa el comando personalizado definido en composer.json:

```bash
composer dev
```

### 4. Acceder al Sistema

Abre tu navegador y ve a:
```
http://localhost:8000/login
```

O la ruta que Laragon haya configurado para tu proyecto.

---

## 📁 Estructura de Assets del Template

Los assets del template **ki-admin** fueron organizados de la siguiente manera:

```
public/assets/
├── vendor/          # Librerías de terceros (Bootstrap, Tabler Icons, etc.)
├── images/          # Imágenes y avatares
├── svg/             # Iconos SVG y sprites
├── fonts/           # Fuentes personalizadas
├── scss/            # Hojas de estilo SCSS
└── js/              # Scripts JavaScript
```

---

## 🔐 Credenciales de Acceso

| Rol | Email | Contraseña |
|-----|-------|------------|
| Administrador | admin@krom-soft.com | Rhonald16* |

---

## 🎨 Vistas Implementadas

### Autenticación
- ✅ Login (`/login`) - Adaptado del template ki-admin (sign_in_1)
- ✅ Logout (POST `/logout`)

### Dashboard
- ✅ Panel administrativo con sidebar, header y footer
- ✅ Tarjetas de resumen (pacientes, doctores, citas, casos)

---

## 📝 Próximos Pasos

1. **Módulo de Empresas** - CRUD completo
2. **Módulo de Sucursales** - CRUD completo
3. **Módulo de Pacientes** - CRUD completo
4. **Módulo de Doctores** - CRUD completo
5. **Módulo de Citas** - Agenda y gestión
6. **Módulo de Casos** - Expedientes médicos
7. **Módulo de Consultas** - Historial clínico

---

## 🛠️ Comandos Útiles

```bash
# Limpiar caché
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# Ver logs
php artisan pail

# Ejecutar tests
composer test

# Formatear código
composer dev
```

---

## 📌 Notas Importantes

1. **Base de Datos:** El sistema usa PostgreSQL. Asegúrate de que el servicio esté corriendo.
2. **Session Driver:** Está configurado para usar `database`. Las sesiones se guardan en la BD.
3. **Assets:** Los archivos SCSS se compilan con Vite. Ejecuta `npm run dev` para desarrollo con hot-reload.
4. **Template:** Se adaptó el template ki-admin_laravel manteniendo una estructura organizada y escalable.

---

## 🔧 Configuración de Laragon

Si usas Laragon:
1. El proyecto ya está en `d:/wwwLaragon/expediente_digital`
2. Laragon auto-configura el host virtual
3. Accede desde: `http://expediente_digital.test/login`

---
