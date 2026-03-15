# Abstracción de Caso Real: Expediente Digital Clínico

## Caso real completo: Registro y atención de un paciente

### 1. Registro de la empresa
- Se crea la empresa "Clínica Podológica Salud Total".
- Datos: nombre, RUC, dirección, teléfono, email.

### 2. Registro de sucursal
- Se crea la sucursal "Sucursal Centro" para la empresa.
- Datos: nombre, dirección, teléfono, email, empresa asociada.

### 3. Alta de empleados y doctores
- Se registran empleados administrativos y doctores.
- Para cada doctor: nombre, apellido, identificación, especialidad, cédula profesional, contacto, email, sucursal asignada.

### 4. Configuración de servicios
- Se definen servicios: consulta podológica, curación, estudio biomecánico, etc.
- Cada servicio tiene nombre, descripción, precio.

### 5. Registro de paciente
- Llega un paciente nuevo: María Pérez.
- Se capturan: nombre, apellido, tipo y número de identificación, fecha de nacimiento, género, contacto, dirección, email, ocupación, nacionalidad, seguro médico, contacto de emergencia.

### 6. Creación de caso/expediente
- Se crea un caso para María Pérez.
- Se asigna un doctor responsable.
- Se registra motivo de consulta: "Dolor en pie derecho".
- Se agregan notas iniciales y se marca el caso como "abierto".

### 7. Agendamiento y atención de consulta
- Se agenda una consulta para María con el doctor asignado.
- El doctor atiende, registra diagnóstico, observaciones, tratamiento, receta, y adjunta una foto del pie.
- Se firma digitalmente la consulta.

### 8. Seguimiento y evolución
- Se agregan nuevas consultas al caso según evolución.
- Se actualiza el estado del caso (en seguimiento, cerrado, etc.).

### 9. Facturación y cobro (opcional)
- Se genera factura y se registra el pago del servicio.

---

## Desarrollo y cobertura del caso

> Aquí se irá documentando cada paso implementado en el sistema para cubrir este caso real, con referencias a migraciones, modelos, controladores, rutas, etc.

---

### [Por completar según avance]
