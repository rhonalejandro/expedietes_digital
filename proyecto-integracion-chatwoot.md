# Proyecto: Integración Chatwoot ↔ CRM Global Feet Panama

> **Fecha:** 2026-03-15
> **Autor:** Krom-Soft
> **Estado:** Planificación

---

## 1. Resumen Ejecutivo

Integrar el CRM de Global Feet Panama (`crm.globalfeetpanama.com`) con Chatwoot (`comunicaciones.globalfeetpanama.com`) para que los agentes que **solo tienen acceso a Chatwoot** puedan, desde la misma interfaz de chat:

- Identificar automáticamente al paciente por número de teléfono
- Ver sus citas activas e historial
- Crear nuevas citas
- Crear clientes/leads
- Confirmar o cancelar citas con un botón
- Enviar recordatorios de cita directamente al chat

---

## 2. Arquitectura del Ambiente

```
┌─────────────────────────────────────┐
│  Chatwoot                           │
│  comunicaciones.globalfeetpanama.com │
│  /opt/chatwoot  (Ruby on Rails)     │
│  DB: chatwoot (PostgreSQL)          │
│                                     │
│  ┌──────────────────────────────┐   │
│  │  Dashboard App (iframe)      │   │  ← Widget del CRM embebido
│  │  Sidebar del agente          │   │
│  └──────────────────────────────┘   │
│                                     │
│  Webhooks → CRM (eventos)           │
│  API Chatwoot ← CRM (acciones)      │
└──────────────────┬──────────────────┘
                   │  HTTPS
                   │
┌──────────────────▼──────────────────┐
│  CRM Laravel                        │
│  crm.globalfeetpanama.com           │
│  /www/wwwroot/crm...  (Laravel 11)  │
│  DB: expediente_digital (PostgreSQL)│
│                                     │
│  Nueva API REST autenticada         │
│  Módulo de Leads                    │
│  Widget embebible (iframe)          │
└─────────────────────────────────────┘
```

---

## 3. Flujo Principal de Trabajo

```
Cliente escribe en WhatsApp/canal
        │
        ▼
Chatwoot recibe conversación
        │
        ▼
Agente abre conversación
        │
        ▼
Dashboard App (iframe del CRM) se carga en el sidebar
        │
        ├─► Chatwoot envía al iframe: phone, name, email, conversation_id
        │
        ▼
Widget del CRM consulta API propia: "buscar por teléfono"
        │
        ├── ENCONTRADO ──► Muestra perfil del paciente
        │                         • Datos personales
        │                         • Próximas citas (con botón Confirmar/Cancelar)
        │                         • Historial de citas
        │                         • Botón: Crear nueva cita
        │                         • Botón: Enviar recordatorio al chat
        │
        └── NO ENCONTRADO ► Muestra formulario
                                  • Crear como Lead (rápido, sin ficha completa)
                                  • Crear como Paciente (ficha completa)
                                  • Agendar cita como lead
```

---

## 4. Componentes a Desarrollar

### 4.1 CRM — API REST Autenticada (Nueva)

**Archivo:** `routes/api.php` (a crear)
**Middleware:** Token API propio (sin Laravel Sanctum, token simple por ahora)

#### Endpoints requeridos:

| Método | Endpoint | Descripción |
|--------|----------|-------------|
| `GET` | `/api/v1/pacientes/buscar-por-telefono?tel=+507XXXXXXXX` | Busca paciente por teléfono, retorna datos + citas |
| `GET` | `/api/v1/pacientes/buscar?q=nombre` | Búsqueda por nombre/identificación |
| `POST` | `/api/v1/pacientes` | Crear nuevo paciente |
| `GET` | `/api/v1/leads` | Listar leads |
| `POST` | `/api/v1/leads` | Crear lead (sin ficha completa) |
| `PUT` | `/api/v1/leads/{id}/convertir` | Convertir lead a paciente |
| `GET` | `/api/v1/citas?paciente_id=X` | Citas de un paciente |
| `POST` | `/api/v1/citas` | Crear nueva cita |
| `PATCH` | `/api/v1/citas/{id}/estatus` | Confirmar / Cancelar cita |
| `GET` | `/api/v1/especialistas` | Listar especialistas activos |
| `GET` | `/api/v1/especialistas/{id}/disponibilidad?mes=YYYY-MM` | Disponibilidad mensual |
| `GET` | `/api/v1/especialistas/{id}/horas-disponibles?fecha=YYYY-MM-DD` | Slots del día |
| `GET` | `/api/v1/servicios` | Listar servicios activos |
| `GET` | `/api/v1/sucursales` | Listar sucursales activas |
| `POST` | `/api/v1/webhook/chatwoot` | Recibir webhooks de Chatwoot |

#### Autenticación API:

- Se crea una tabla `api_tokens` (token, nombre, activo, ultimo_uso)
- O más simple: un token fijo en `.env` (`CHATWOOT_API_TOKEN=xxx`)
- El widget envía `Authorization: Bearer {token}` en cada request
- El token se genera en el panel de configuración del CRM

#### Respuesta tipo `buscar-por-telefono`:

```json
{
  "encontrado": true,
  "tipo": "paciente",
  "paciente": {
    "id": 12,
    "nombre": "María López",
    "telefono": "+50769876543",
    "email": "maria@example.com",
    "identificacion": "8-123-456"
  },
  "citas": [
    {
      "id": 45,
      "fecha": "2026-03-20",
      "hora_inicio": "09:00",
      "hora_fin": "09:30",
      "especialista": "Dr. Juan Pérez",
      "servicio": "Podología General",
      "sucursal": "Sede Central",
      "estatus": "pendiente"
    }
  ],
  "total_citas_historico": 8
}
```

---

### 4.2 CRM — Módulo de Leads (Nuevo)

Los **leads** son contactos de Chatwoot que aún no tienen ficha completa como pacientes.

#### Tabla `leads` (nueva migración):

```
id                  PK
nombre              string(150)
telefono            string(50)
email               string(150), nullable
origen              string(30)  → 'chatwoot', 'telefono', 'web'
chatwoot_contact_id integer, nullable  → ID del contacto en Chatwoot
chatwoot_conv_id    integer, nullable  → ID de conversación en Chatwoot
notas               text, nullable
estatus             string(20)  → 'nuevo', 'en_seguimiento', 'convertido', 'descartado'
convertido_en       FK → pacientes.id, nullable
created_at, updated_at
```

#### Funcionalidades:
- Crear lead desde el widget de Chatwoot (rápido, sin ficha médica)
- Ver lista de leads en el CRM
- Botón "Convertir a Paciente" que crea la ficha médica completa
- Agendar cita a un lead (usando `nombre_lead`, `telefono_lead` en tabla `citas`)
- Al convertir a paciente, las citas del lead se reasignan al paciente

---

### 4.3 Dashboard App — Widget embebible (Nuevo)

El Dashboard App es un **iframe** que Chatwoot muestra en el sidebar del agente.

#### Tecnología:
- Página HTML/JS servida desde el CRM Laravel
- URL: `https://crm.globalfeetpanama.com/chatwoot/widget`
- Recibe datos del contacto vía `window.postMessage` (protocolo estándar de Chatwoot)

#### Comunicación Chatwoot → Widget:

Chatwoot envía automáticamente via `postMessage`:
```js
{
  event: "appContext",
  data: {
    contact: {
      id: 123,
      name: "María López",
      phone_number: "+50769876543",
      email: "maria@example.com"
    },
    conversation: {
      id: 456,
      inbox_id: 2
    },
    currentAgent: {
      id: 7,
      name: "Ana García"
    }
  }
}
```

#### Pantallas del Widget:

**Pantalla 1 — Cargando:**
```
┌─────────────────────────┐
│  🔍 Buscando paciente…  │
│  +507 6987-6543         │
└─────────────────────────┘
```

**Pantalla 2 — Paciente encontrado:**
```
┌─────────────────────────┐
│ ✅ María López          │
│ 📋 Paciente registrada  │
│ ─────────────────────── │
│ PRÓXIMAS CITAS          │
│ ─────────────────────── │
│ 📅 Jue 20 Mar 09:00     │
│ Dr. Juan Pérez          │
│ Podología General       │
│ [✓ Confirmar] [✗ Cancel]│
│ [📤 Enviar recordatorio] │
│ ─────────────────────── │
│ [+ Nueva Cita]          │
│ [👁 Ver en CRM]          │
└─────────────────────────┘
```

**Pantalla 3 — No encontrado:**
```
┌─────────────────────────┐
│ ❓ No encontrado         │
│ +507 6987-6543          │
│ ─────────────────────── │
│ [+ Crear Lead rápido]   │
│ [+ Crear como Paciente] │
│ [📅 Agendar como Lead]  │
└─────────────────────────┘
```

**Pantalla 4 — Crear cita (modal inline):**
- Select: Especialista
- Calendário de disponibilidad (mini)
- Select: Horario
- Select: Servicio
- Botón Guardar

#### Archivo de la vista:
`resources/views/chatwoot/widget.blade.php`
Sin layout de admin, solo el widget standalone con estilos mínimos.

---

### 4.4 Webhook de Chatwoot → CRM

Chatwoot enviará eventos al CRM cuando ocurran acciones importantes.

#### Configurar en Chatwoot:
- URL: `https://crm.globalfeetpanama.com/api/v1/webhook/chatwoot`
- Eventos a suscribir:
  - `conversation_created` — nueva conversación
  - `conversation_resolved` — conversación resuelta
  - `message_created` — nuevo mensaje (para crear notas en CRM)

#### Lo que hace el CRM al recibir un webhook:

```
conversation_created
    │
    ├─► Extraer phone_number del contact
    ├─► Buscar en personas.contacto
    │
    ├── ENCONTRADO → No hacer nada (agente ve en widget)
    │
    └── NO ENCONTRADO → Crear Lead automáticamente
            • nombre: del contacto de Chatwoot
            • telefono: phone_number
            • chatwoot_contact_id: contact.id
            • chatwoot_conv_id: conversation.id
            • origen: 'chatwoot'
            • estatus: 'nuevo'
```

---

### 4.5 Acción: Enviar Recordatorio desde el Widget

El agente hace click en "Enviar recordatorio" y el CRM:

1. Formatea el mensaje con los datos de la cita:
```
Hola María 👋

Te recordamos tu cita en Global Feet Panama:

📅 Jueves 20 de Marzo, 2026
⏰ 09:00 AM
👨‍⚕️ Dr. Juan Pérez — Podología General
📍 Sede Central

¿Necesitas confirmar o reagendar? Escríbenos aquí mismo.
```

2. Llama al API de Chatwoot:
```
POST https://comunicaciones.globalfeetpanama.com/api/v1/accounts/{account_id}/conversations/{conv_id}/messages
Authorization: api_access_token {chatwoot_agent_token}
{
  "content": "...",
  "message_type": "outgoing",
  "private": false
}
```

---

## 5. Plan de Implementación por Fases

### Fase 1 — API REST del CRM (Semana 1)
**Objetivo:** Exponer los datos del CRM de forma segura para el widget.

- [ ] Crear `routes/api.php`
- [ ] Crear middleware `ApiTokenMiddleware`
- [ ] Agregar `CHATWOOT_API_TOKEN` al `.env`
- [ ] Crear `ApiCitaController` con endpoints de citas
- [ ] Crear `ApiPacienteController` con búsqueda por teléfono y CRUD
- [ ] Crear `ApiEspecialistaController` con disponibilidad
- [ ] Crear `ApiServicioController` y `ApiSucursalController`
- [ ] Normalización de teléfonos (E.164 ↔ formato local)

### Fase 2 — Módulo de Leads (Semana 1-2)
**Objetivo:** Capturar contactos de Chatwoot que no son pacientes.

- [ ] Migration `create_leads_table`
- [ ] Modelo `Lead` con relaciones
- [ ] `ApiLeadController` (crear, listar, convertir a paciente)
- [ ] Vista del módulo Leads en el CRM (listado, filtros, acciones)
- [ ] Botón "Convertir a Paciente" en la vista del lead
- [ ] Al convertir: re-asignar citas previas de lead al nuevo paciente

### Fase 3 — Widget embebible (Semana 2)
**Objetivo:** El agente puede ver y actuar desde Chatwoot.

- [ ] Ruta `GET /chatwoot/widget` (pública, con token en query param)
- [ ] Vista `chatwoot/widget.blade.php` (standalone, sin layout admin)
- [ ] Lógica JS: escuchar `postMessage` de Chatwoot
- [ ] Pantalla: buscando
- [ ] Pantalla: paciente encontrado + sus citas
- [ ] Pantalla: no encontrado + opciones
- [ ] Acción: Confirmar cita (PATCH `/api/v1/citas/{id}/estatus`)
- [ ] Acción: Cancelar cita
- [ ] Acción: Enviar recordatorio (llama API de Chatwoot)
- [ ] Formulario inline: crear nueva cita (con calendario mini)
- [ ] Formulario inline: crear lead rápido
- [ ] Link "Ver en CRM" que abre el perfil del paciente en nueva pestaña

### Fase 4 — Dashboard App en Chatwoot (Semana 2)
**Objetivo:** Configurar el iframe en Chatwoot.

- [ ] En Chatwoot Admin: Settings → Integrations → Dashboard Apps → New
  - Title: `CRM Global Feet`
  - URL: `https://crm.globalfeetpanama.com/chatwoot/widget?token={API_TOKEN}`
- [ ] Verificar que el `postMessage` llega correctamente al widget
- [ ] Testear con conversación real de WhatsApp

### Fase 5 — Webhooks de Chatwoot (Semana 3)
**Objetivo:** Creación automática de leads cuando llega un nuevo chat.

- [ ] Crear endpoint `POST /api/v1/webhook/chatwoot`
- [ ] Verificar firma HMAC del webhook (seguridad)
- [ ] Handler `conversation_created`: buscar o crear lead
- [ ] En Chatwoot Admin: Settings → Integrations → Webhooks → New
  - URL: `https://crm.globalfeetpanama.com/api/v1/webhook/chatwoot`
  - Eventos: `conversation_created`

### Fase 6 — Envío de recordatorios (Semana 3)
**Objetivo:** El agente envía mensajes desde el widget.

- [ ] Agregar `CHATWOOT_API_URL` y `CHATWOOT_API_TOKEN` al `.env` del CRM
- [ ] Servicio `ChatwootService` en Laravel (cliente HTTP para API de Chatwoot)
- [ ] Endpoint `POST /api/v1/citas/{id}/recordatorio` en el CRM
- [ ] Template de mensaje configurable
- [ ] Botón en el widget que dispara el recordatorio

---

## 6. Estructura de Archivos Nuevos

```
crm.globalfeetpanama.com/
├── routes/
│   └── api.php                              ← NUEVO: rutas API REST
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   └── Api/
│   │   │       ├── ApiPacienteController.php    ← NUEVO
│   │   │       ├── ApiCitaController.php        ← NUEVO
│   │   │       ├── ApiLeadController.php        ← NUEVO
│   │   │       ├── ApiEspecialistaController.php ← NUEVO
│   │   │       ├── ApiSucursalController.php    ← NUEVO
│   │   │       ├── ApiServicioController.php    ← NUEVO
│   │   │       └── ChatwootWebhookController.php ← NUEVO
│   │   └── Middleware/
│   │       └── ApiTokenMiddleware.php           ← NUEVO
│   ├── Models/
│   │   └── Lead.php                            ← NUEVO
│   └── Services/
│       └── ChatwootService.php                 ← NUEVO (HTTP client)
├── database/
│   └── migrations/
│       └── 2026_03_15_create_leads_table.php   ← NUEVO
└── resources/
    └── views/
        └── chatwoot/
            └── widget.blade.php                ← NUEVO (iframe standalone)
```

---

## 7. Variables de Entorno

### CRM `.env` (agregar):
```env
# Token para autenticar el widget/Chatwoot → CRM
CHATWOOT_WIDGET_TOKEN=gf_crm_XXXXXXXXXXXXXXXXXXXXXXXX

# Datos para que el CRM llame a Chatwoot (enviar mensajes)
CHATWOOT_API_URL=https://comunicaciones.globalfeetpanama.com
CHATWOOT_ACCOUNT_ID=1
CHATWOOT_API_ACCESS_TOKEN=XXXXXXXXXXXXXXXXXXXXXXXX

# Verificar webhooks entrantes de Chatwoot
CHATWOOT_WEBHOOK_SECRET=XXXXXXXXXXXXXXXXXXXXXXXX
```

### Chatwoot `.env` (no requiere cambios, configuración vía UI)

---

## 8. Decisiones de Diseño

### ¿Por qué Dashboard App (iframe) y no extensión nativa?
- Chatwoot no tiene SDK de extensiones; el iframe es el mecanismo oficial
- El CRM ya está desplegado y tiene los datos, evitamos duplicar lógica
- Actualizaciones del widget se despliegan solo en el CRM, sin tocar Chatwoot

### ¿Por qué API token y no sesión de usuario?
- Los agentes de Chatwoot NO tienen cuentas en el CRM
- El widget actúa como "servicio" con un token de lectura/escritura limitada
- En el futuro se puede hacer matching agente Chatwoot → usuario CRM

### ¿Cómo se normaliza el teléfono?
- Chatwoot guarda teléfonos en formato E.164: `+50769876543`
- El CRM en `personas.contacto` puede tener: `6987-6543`, `507 6987 6543`, etc.
- La búsqueda debe ser flexible: strip de `+507`, espacios y guiones
- Función helper: `normalizarTelefono($tel)` → solo dígitos → comparar últimos 8

### ¿Leads vs Pacientes?
- **Lead**: contacto de Chatwoot sin ficha médica → tabla `leads`
- **Paciente**: persona con ficha médica completa → tablas `personas` + `pacientes`
- Se puede agendar cita a un lead (campos `nombre_lead`, `telefono_lead` en `citas`)
- La conversión Lead → Paciente crea la ficha y reasigna las citas

### ¿Qué pasa si el mismo número tiene varios registros?
- Mostrar los resultados al agente para que elija
- Puede ser que una persona tenga teléfono actualizado

---

## 9. Seguridad

| Riesgo | Mitigación |
|--------|-----------|
| Widget accedido sin autorización | Token en query param + validación CORS (solo `comunicaciones.globalfeetpanama.com`) |
| Webhook falso | Verificación HMAC con `CHATWOOT_WEBHOOK_SECRET` |
| API expuesta | Token API requerido en `Authorization: Bearer` para todos los endpoints |
| XSS en widget | Escapado de datos, CSP headers |
| IDOR (acceder citas de otro paciente) | Validar que `paciente_id` pertenece a la respuesta de búsqueda del mismo request |

---

## 10. Casos de Uso Detallados

### UC-1: Agente identifica a un paciente
1. Cliente escribe por WhatsApp con número `+50769876543`
2. Agente abre la conversación en Chatwoot
3. Widget carga, recibe el `phone_number` del contacto
4. Widget llama `GET /api/v1/pacientes/buscar-por-telefono?tel=+50769876543`
5. CRM normaliza → busca en `personas.contacto` → encuentra a María López
6. Widget muestra: perfil, citas próximas, historial

### UC-2: Agente confirma una cita
1. Widget muestra cita pendiente del día siguiente
2. Agente hace click en "✓ Confirmar"
3. Widget llama `PATCH /api/v1/citas/45/estatus` con `{ estatus: "confirmada" }`
4. CRM actualiza la cita, registra log
5. Widget muestra badge verde "Confirmada"

### UC-3: Agente envía recordatorio
1. Cita confirmada aparece en el widget
2. Agente hace click en "📤 Enviar recordatorio"
3. CRM llama a la API de Chatwoot: `POST /conversations/{id}/messages`
4. Mensaje formateado aparece en el chat de WhatsApp del cliente

### UC-4: Nuevo cliente, crear como lead
1. Número desconocido escribe
2. Widget muestra "No encontrado"
3. Agente hace click "Crear Lead rápido"
4. Formulario mínimo: nombre, teléfono (pre-llenado)
5. Lead creado en CRM con `origen: 'chatwoot'`, `chatwoot_contact_id: 123`

### UC-5: Agendar cita a un lead
1. Lead creado (UC-4)
2. Agente hace click "📅 Agendar cita"
3. Widget muestra formulario: especialista → fecha → hora → servicio
4. CRM crea cita con `nombre_lead`, `telefono_lead`, `origen: 'chatwoot'`
5. Widget confirma y muestra la cita creada

### UC-6: Convertir lead a paciente
1. En el CRM (vista Leads), el admin ve el lead de María
2. Click "Convertir a Paciente"
3. Se abre formulario con datos pre-llenados del lead
4. Al guardar: se crea `Persona` + `Paciente`, se reasignan las citas, el lead queda `estatus: 'convertido'`

### UC-7: Webhook auto-crea lead
1. Nuevo número desconocido inicia conversación
2. Chatwoot envía webhook `conversation_created` al CRM
3. CRM busca el teléfono → no encontrado
4. CRM crea lead automáticamente: `{ nombre: "Contacto de Chatwoot", telefono: "+50769876543", chatwoot_contact_id: 456, estatus: "nuevo" }`
5. Al abrir el agente la conversación, el widget ya muestra el lead creado

---

## 11. Chatwoot: Configuración Requerida

### Dashboard App
```
Settings → Integrations → Dashboard Apps → + New Dashboard App
  Title: CRM Global Feet
  URL: https://crm.globalfeetpanama.com/chatwoot/widget?token=gf_crm_XXXXX
```

### Webhook
```
Settings → Integrations → Webhooks → + New Webhook
  URL: https://crm.globalfeetpanama.com/api/v1/webhook/chatwoot
  Subscribed Events:
    ✅ Conversation Created
    ✅ Conversation Resolved  (opcional, para estadísticas)
```

### API Access Token para el CRM
```
Profile → Access Token (del agente o cuenta de servicio)
→ Copiar y poner en CRM .env como CHATWOOT_API_ACCESS_TOKEN
```

---

## 12. Dependencias y Prerrequisitos

| Item | Estado |
|------|--------|
| Chatwoot desplegado y funcional | ✅ Listo en `comunicaciones.globalfeetpanama.com` |
| CRM Laravel funcional | ✅ Listo en `crm.globalfeetpanama.com` |
| Campo `origen` en tabla `citas` | ✅ Ya existe (web, chatwoot, mobile, telefono) |
| Campos `nombre_lead`, `telefono_lead` en citas | ✅ Ya existen |
| WhatsApp Business conectado a Chatwoot | ❓ Verificar |
| HTTPS en ambos dominios | ✅ Requerido para iframe y webhooks |
| Tabla `leads` | ❌ Pendiente (migración) |
| API REST del CRM | ❌ Pendiente (Phase 1) |

---

## 13. Métricas de Éxito

- [ ] Agente puede identificar un paciente por teléfono en < 3 segundos
- [ ] Agente puede crear una cita sin salir de Chatwoot
- [ ] Agente puede enviar recordatorio con 1 click
- [ ] Leads de Chatwoot aparecen automáticamente en el CRM
- [ ] 0 datos de pacientes expuestos sin autenticación

---

## 14. Orden de Implementación Sugerido

```
Semana 1
  └─ Fase 1: API REST (endpoints base)
  └─ Fase 2: Módulo Leads (migración + modelo + API)

Semana 2
  └─ Fase 3: Widget HTML/JS (pantallas + acciones básicas)
  └─ Fase 4: Configurar Dashboard App en Chatwoot y probar

Semana 3
  └─ Fase 5: Webhooks (auto-creación de leads)
  └─ Fase 6: Envío de recordatorios vía Chatwoot API

Semana 4
  └─ Módulo Leads en CRM (vista admin)
  └─ Pulir UX del widget
  └─ Testing end-to-end con WhatsApp real
  └─ Documentación de uso para agentes
```

---

*Documento generado por Krom-Soft — actualizar conforme avance la implementación.*
