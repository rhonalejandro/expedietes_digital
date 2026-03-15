---

## Tablas y Estructura Detallada

### Tabla: personas
| Campo              | Tipo         | Longitud | Comentario                                 |
|--------------------|-------------|----------|--------------------------------------------|
| id                 | BIGSERIAL   |          | PK, autoincremental                        |
| nombre             | VARCHAR     | 100      | Nombre de la persona                       |
| apellido           | VARCHAR     | 100      | Apellido de la persona                     |
| tipo_identificacion| VARCHAR     | 50       | Tipo de documento (cédula, pasaporte, etc) |
| identificacion     | VARCHAR     | 50       | Número de identificación                   |
| fecha_nacimiento   | DATE        |          | Fecha de nacimiento                        |
| contacto           | VARCHAR     | 100      | Teléfono o celular                         |
| direccion          | VARCHAR     | 255      | Dirección física                           |
| email              | VARCHAR     | 150      | Correo electrónico                         |
| genero             | VARCHAR     | 20       | Género                                     |
| ocupacion          | VARCHAR     | 100      | Ocupación de la persona                    |
| nacionalidad       | VARCHAR     | 50       | Nacionalidad                               |
| seguro_medico      | VARCHAR     | 100      | Seguro médico                              |
| contacto_emergencia| VARCHAR     | 100      | Contacto de emergencia                     |
| estado             | BOOLEAN     |          | Activo/Inactivo                            |
| created_at         | TIMESTAMP   |          | Fecha de creación                          |
| updated_at         | TIMESTAMP   |          | Fecha de actualización                     |

### Tabla: empresas
| Campo      | Tipo       | Longitud | Comentario                |
|------------|------------|----------|---------------------------|
| id                   | BIGSERIAL  |          | PK, autoincremental                        |
| nombre               | VARCHAR    | 150      | Nombre de la empresa                       |
| tipo_identificacion  | VARCHAR    | 50       | Tipo de identificación (RUC, RIF, Cédula Jurídica, etc.) |
| identificacion       | VARCHAR    | 50       | Número de identificación                   |
| telefono             | VARCHAR    | 100      | Teléfono principal                         |
| email                | VARCHAR    | 150      | Correo electrónico                         |
| pagina_web           | VARCHAR    | 150      | Página web                                 |
| redes_sociales       | JSON       |          | Redes sociales (estructura flexible)        |
| direccion            | VARCHAR    | 255      | Dirección física                           |
| estado               | BOOLEAN    |          | Activo/Inactivo                            |
| created_at           | TIMESTAMP  |          | Fecha de creación                          |
| updated_at           | TIMESTAMP  |          | Fecha de actualización                     |

### Tabla: sucursales
| Campo      | Tipo       | Longitud | Comentario                |
|------------|------------|----------|---------------------------|
| id         | BIGSERIAL  |          | PK, autoincremental       |
| empresa_id | BIGINT     |          | FK a empresas             |
| nombre     | VARCHAR    | 150      | Nombre de la sucursal     |
| direccion  | VARCHAR    | 255      | Dirección                 |
| contacto   | VARCHAR    | 100      | Teléfono                  |
| email      | VARCHAR    | 150      | Correo electrónico        |
| estado     | BOOLEAN    |          | Activo/Inactivo           |
| created_at | TIMESTAMP  |          | Fecha de creación         |
| updated_at | TIMESTAMP  |          | Fecha de actualización    |

### Tabla: usuarios
| Campo      | Tipo       | Longitud | Comentario                |
|------------|------------|----------|---------------------------|
| id         | BIGSERIAL  |          | PK, autoincremental       |
| persona_id | BIGINT     |          | FK a personas             |
| email      | VARCHAR    | 150      | Correo de acceso          |
| password   | VARCHAR    | 255      | Contraseña (hash)         |
| estado     | BOOLEAN    |          | Activo/Inactivo           |
| created_at | TIMESTAMP  |          | Fecha de creación         |
| updated_at | TIMESTAMP  |          | Fecha de actualización    |

### Tabla: roles
| Campo      | Tipo       | Longitud | Comentario                |
|------------|------------|----------|---------------------------|
| id         | BIGSERIAL  |          | PK, autoincremental       |
| nombre     | VARCHAR    | 100      | Nombre del rol            |
| descripcion| VARCHAR    | 255      | Descripción del rol       |
| created_at | TIMESTAMP  |          | Fecha de creación         |
| updated_at | TIMESTAMP  |          | Fecha de actualización    |

### Tabla: usuario_rol
| Campo      | Tipo       | Longitud | Comentario                |
|------------|------------|----------|---------------------------|
| usuario_id | BIGINT     |          | FK a usuarios             |
| rol_id     | BIGINT     |          | FK a roles                |

### Tabla: usuario_sucursal
| Campo      | Tipo       | Longitud | Comentario                |
|------------|------------|----------|---------------------------|
| usuario_id | BIGINT     |          | FK a usuarios             |
| sucursal_id| BIGINT     |          | FK a sucursales           |

### Tabla: doctores
| Campo      | Tipo       | Longitud | Comentario                |
|------------|------------|----------|---------------------------|
| id         | BIGSERIAL  |          | PK, autoincremental       |
| persona_id | BIGINT     |          | FK a personas             |
| especialidad| VARCHAR   | 100      | Especialidad médica       |
| estado     | BOOLEAN    |          | Activo/Inactivo           |
| created_at | TIMESTAMP  |          | Fecha de creación         |
| updated_at | TIMESTAMP  |          | Fecha de actualización    |

### Tabla: doctor_sucursal
| Campo      | Tipo       | Longitud | Comentario                |
|------------|------------|----------|---------------------------|
| doctor_id  | BIGINT     |          | FK a doctores             |
| sucursal_id| BIGINT     |          | FK a sucursales           |

### Tabla: pacientes
| Campo      | Tipo       | Longitud | Comentario                |
|------------|------------|----------|---------------------------|
| id         | BIGSERIAL  |          | PK, autoincremental       |
| persona_id | BIGINT     |          | FK a personas             |
| estado     | BOOLEAN    |          | Activo/Inactivo           |
| created_at | TIMESTAMP  |          | Fecha de creación         |
| updated_at | TIMESTAMP  |          | Fecha de actualización    |

### Tabla: horarios_doctores
| Campo         | Tipo       | Longitud | Comentario                |
|---------------|------------|----------|---------------------------|
| id            | BIGSERIAL  |          | PK, autoincremental       |
| doctor_id     | BIGINT     |          | FK a doctores             |
| sucursal_id   | BIGINT     |          | FK a sucursales           |
| dia_semana    | VARCHAR    | 20       | Día de la semana          |
| hora_inicio   | TIME       |          | Hora de inicio            |
| hora_fin      | TIME       |          | Hora de fin               |
| duracion_cita | INTEGER    |          | Duración en minutos       |
| citas_maximas | INTEGER    |          | Citas máximas por día     |
| created_at    | TIMESTAMP  |          | Fecha de creación         |
| updated_at    | TIMESTAMP  |          | Fecha de actualización    |

### Tabla: citas
| Campo         | Tipo       | Longitud | Comentario                |
|---------------|------------|----------|---------------------------|
| id            | BIGSERIAL  |          | PK, autoincremental       |
| doctor_id     | BIGINT     |          | FK a doctores             |
| paciente_id   | BIGINT     |          | FK a pacientes            |
| sucursal_id   | BIGINT     |          | FK a sucursales           |
| fecha         | DATE       |          | Fecha de la cita          |
| hora_inicio   | TIME       |          | Hora de inicio            |
| hora_fin      | TIME       |          | Hora de fin               |
| estatus       | VARCHAR    | 30       | Estado (confirmada, etc.) |
| observaciones | TEXT       |          | Observaciones             |
| created_at    | TIMESTAMP  |          | Fecha de creación         |
| updated_at    | TIMESTAMP  |          | Fecha de actualización    |

### Tabla: casos
| Campo         | Tipo       | Longitud | Comentario                |
|---------------|------------|----------|---------------------------|
| id            | BIGSERIAL  |          | PK, autoincremental       |
| paciente_id   | BIGINT     |          | FK a pacientes            |
| doctor_id     | BIGINT     |          | FK a doctores             |
| sucursal_id   | BIGINT     |          | FK a sucursales           |
| descripcion   | TEXT       |          | Descripción del caso      |
| motivo        | VARCHAR    | 255      | Motivo de consulta        |
| estado        | VARCHAR    | 30       | Estado del caso           |
| fecha_apertura| DATE       |          | Fecha de apertura         |
| fecha_cierre  | DATE       |          | Fecha de cierre           |
| notas_iniciales| TEXT      |          | Notas iniciales del caso  |
| created_at    | TIMESTAMP  |          | Fecha de creación         |
| updated_at    | TIMESTAMP  |          | Fecha de actualización    |
### Tabla: consultas
| Campo         | Tipo       | Longitud | Comentario                |
|---------------|------------|----------|---------------------------|
| id            | BIGSERIAL  |          | PK, autoincremental       |
| caso_id       | BIGINT     |          | FK a casos                |
| doctor_id     | BIGINT     |          | FK a doctores             |
| fecha_hora    | DATETIME   |          | Fecha y hora de la consulta|
| estado        | VARCHAR    | 30       | Estado de la consulta     |
| diagnostico   | TEXT       |          | Diagnóstico               |
| observaciones | TEXT       |          | Observaciones             |
| tratamiento   | TEXT       |          | Tratamiento               |
| receta        | TEXT       |          | Receta médica             |
| firma_digital | VARCHAR    | 255      | Firma digital o hash      |
| created_at    | TIMESTAMP  |          | Fecha de creación         |
| updated_at    | TIMESTAMP  |          | Fecha de actualización    |
### Tabla: adjuntos
| Campo         | Tipo       | Longitud | Comentario                |
|---------------|------------|----------|---------------------------|
| id            | BIGSERIAL  |          | PK, autoincremental       |
| consulta_id   | BIGINT     |          | FK a consultas            |
| tipo          | VARCHAR    | 50       | Tipo de archivo           |
| ruta          | VARCHAR    | 255      | Ruta del archivo          |
| descripcion   | VARCHAR    | 255      | Descripción del archivo   |
| created_at    | TIMESTAMP  |          | Fecha de creación         |
| updated_at    | TIMESTAMP  |          | Fecha de actualización    |

### Tabla: expedientes
| Campo         | Tipo       | Longitud | Comentario                |
|---------------|------------|----------|---------------------------|
| id            | BIGSERIAL  |          | PK, autoincremental       |
| caso_id       | BIGINT     |          | FK a casos                |
| cita_id       | BIGINT     |          | FK a citas                |
| doctor_id     | BIGINT     |          | FK a doctores             |
| paciente_id   | BIGINT     |          | FK a pacientes            |
| fecha         | DATE       |          | Fecha del expediente      |
| diagnostico   | TEXT       |          | Diagnóstico               |
| tratamiento   | TEXT       |          | Tratamiento               |
| notas         | TEXT       |          | Notas adicionales         |
| created_at    | TIMESTAMP  |          | Fecha de creación         |
| updated_at    | TIMESTAMP  |          | Fecha de actualización    |

### Tabla: historial_medico
| Campo         | Tipo       | Longitud | Comentario                |
|---------------|------------|----------|---------------------------|
| id            | BIGSERIAL  |          | PK, autoincremental       |
| expediente_id | BIGINT     |          | FK a expedientes          |
| fecha         | DATE       |          | Fecha del registro        |
| evolucion     | TEXT       |          | Evolución                 |
| observaciones | TEXT       |          | Observaciones             |
| created_at    | TIMESTAMP  |          | Fecha de creación         |
| updated_at    | TIMESTAMP  |          | Fecha de actualización    |

### Tabla: imagenes
| Campo         | Tipo       | Longitud | Comentario                |
|---------------|------------|----------|---------------------------|
| id            | BIGSERIAL  |          | PK, autoincremental       |
| historial_id  | BIGINT     |          | FK a historial_medico     |
| ruta          | VARCHAR    | 255      | Ruta del archivo          |
| descripcion   | VARCHAR    | 255      | Descripción de la imagen  |
| created_at    | TIMESTAMP  |          | Fecha de creación         |
| updated_at    | TIMESTAMP  |          | Fecha de actualización    |

### Tabla: servicios
| Campo         | Tipo       | Longitud | Comentario                |
|---------------|------------|----------|---------------------------|
| id            | BIGSERIAL  |          | PK, autoincremental       |
| nombre        | VARCHAR    | 100      | Nombre del servicio       |
| descripcion   | VARCHAR    | 255      | Descripción               |
| precio        | DECIMAL    | 10,2     | Precio del servicio       |
| estado        | BOOLEAN    |          | Activo/Inactivo           |
| created_at    | TIMESTAMP  |          | Fecha de creación         |
| updated_at    | TIMESTAMP  |          | Fecha de actualización    |

### Tabla: pagos
| Campo         | Tipo       | Longitud | Comentario                |
|---------------|------------|----------|---------------------------|
| id            | BIGSERIAL  |          | PK, autoincremental       |
| cita_id       | BIGINT     |          | FK a citas                |
| paciente_id   | BIGINT     |          | FK a pacientes            |
| monto         | DECIMAL    | 10,2     | Monto pagado              |
| metodo_pago   | VARCHAR    | 50       | Método de pago            |
| fecha         | DATE       |          | Fecha del pago            |
| estado        | VARCHAR    | 30       | Estado del pago           |
| created_at    | TIMESTAMP  |          | Fecha de creación         |
| updated_at    | TIMESTAMP  |          | Fecha de actualización    |

### Tabla: log_clientes
| Campo               | Tipo       | Longitud | Comentario                |
|---------------------|------------|----------|---------------------------|
| id                  | BIGSERIAL  |          | PK, autoincremental       |
| cliente_id          | BIGINT     |          | FK a pacientes            |
| usuario_id          | BIGINT     |          | FK a usuarios             |
| tipo_accion         | VARCHAR    | 50       | Acción realizada          |
| fecha               | TIMESTAMP  |          | Fecha de la acción        |
| detalles            | TEXT       |          | Detalles de la acción     |
| sucursal_id         | BIGINT     |          | FK a sucursales           |
| empresa_id          | BIGINT     |          | FK a empresas             |

### Tabla: log_citas
| Campo               | Tipo       | Longitud | Comentario                |
|---------------------|------------|----------|---------------------------|
| id                  | BIGSERIAL  |          | PK, autoincremental       |
| cita_id             | BIGINT     |          | FK a citas                |
| usuario_id          | BIGINT     |          | FK a usuarios             |
| tipo_accion         | VARCHAR    | 50       | Acción realizada          |
| fecha               | TIMESTAMP  |          | Fecha de la acción        |
| detalles            | TEXT       |          | Detalles de la acción     |
| sucursal_id         | BIGINT     |          | FK a sucursales           |
| empresa_id          | BIGINT     |          | FK a empresas             |

### Tabla: log_expedientes
| Campo               | Tipo       | Longitud | Comentario                |
|---------------------|------------|----------|---------------------------|
| id                  | BIGSERIAL  |          | PK, autoincremental       |
| expediente_id       | BIGINT     |          | FK a expedientes          |
| usuario_id          | BIGINT     |          | FK a usuarios             |
| tipo_accion         | VARCHAR    | 50       | Acción realizada          |
| fecha               | TIMESTAMP  |          | Fecha de la acción        |
| detalles            | TEXT       |          | Detalles de la acción     |
| sucursal_id         | BIGINT     |          | FK a sucursales           |
| empresa_id          | BIGINT     |          | FK a empresas             |

### Tabla: notificaciones
| Campo         | Tipo       | Longitud | Comentario                |
|---------------|------------|----------|---------------------------|
| id            | BIGSERIAL  |          | PK, autoincremental       |
| usuario_id    | BIGINT     |          | FK a usuarios             |
| mensaje       | TEXT       |          | Mensaje de la notificación|
| leido         | BOOLEAN    |          | Leído o no                |
| fecha         | TIMESTAMP  |          | Fecha de la notificación  |

### Tabla: archivos_adjuntos
| Campo         | Tipo       | Longitud | Comentario                |
|---------------|------------|----------|---------------------------|
| id            | BIGSERIAL  |          | PK, autoincremental       |
| modulo        | VARCHAR    | 50       | Módulo relacionado        |
| registro_id   | BIGINT     |          | ID del registro relacionado|
| ruta          | VARCHAR    | 255      | Ruta del archivo          |
| descripcion   | VARCHAR    | 255      | Descripción del archivo   |
| fecha         | TIMESTAMP  |          | Fecha de carga            |

### Tabla: configuracion_general
| Campo         | Tipo       | Longitud | Comentario                |
|---------------|------------|----------|---------------------------|
| id            | BIGSERIAL  |          | PK, autoincremental       |
| clave         | VARCHAR    | 100      | Nombre de la configuración|
| valor         | VARCHAR    | 255      | Valor de la configuración |
| descripcion   | VARCHAR    | 255      | Descripción               |
| fecha         | TIMESTAMP  |          | Fecha de la configuración |

# Estructura y Análisis de Datos

## Estructura de Datos Propuesta

### 1. Personas (Normalización)
- Tabla central: `personas`
- Campos: id, nombre, apellido, tipo_identificacion, identificacion, fecha_nacimiento, contacto, dirección, email, etc.
- Relaciona a usuarios, doctores, pacientes, vendedores, etc.
- Permite que una persona tenga múltiples roles sin duplicar datos.

### 2. Empresas y Sucursales
- `empresas`: id, nombre, ruc, dirección, contacto, etc.
- `sucursales`: id, empresa_id (FK), nombre, dirección, contacto, etc.

### 3. Usuarios y Roles
- `usuarios`: id, persona_id (FK), email, password, estado, etc.
- `roles`: id, nombre, descripción.
- `usuario_rol`: usuario_id, rol_id (pivote).
- `usuario_sucursal`: usuario_id, sucursal_id (pivote, acceso por sucursal).
- Permisos: usar tabla o paquete para granularidad.

### 4. Doctores y Pacientes
- `doctores`: id, persona_id (FK), especialidad, estado, etc.
- `doctor_sucursal`: doctor_id, sucursal_id (pivote, asignación de doctores a sucursales).
- `pacientes`: id, persona_id (FK), estado, etc.

### 5. Horarios y Citas
- `horarios_doctores`: id, doctor_id, sucursal_id, dia_semana, hora_inicio, hora_fin, duracion_cita, citas_maximas, etc.
- `citas`: id, doctor_id, paciente_id, sucursal_id, fecha, hora_inicio, hora_fin, estatus, observaciones, etc.

### 6. Casos, Expedientes e Historial Médico
- `casos`: id, paciente_id, doctor_id, sucursal_id, descripcion, fecha_apertura, estado, etc.
- `expedientes`: id, caso_id, cita_id, doctor_id, paciente_id, fecha, diagnostico, tratamiento, notas, etc.
- `historial_medico`: id, expediente_id, fecha, evolucion, observaciones, etc.
- `imagenes`: id, historial_id, ruta, descripcion, etc.

### 7. Servicios y Pagos
- `servicios`: id, nombre, descripcion, precio, estado, etc.
- `pagos`: id, cita_id, paciente_id, monto, metodo_pago, fecha, estado, etc.

### 8. Auditoría y Logs
- Tablas de log por módulo: ejemplo `log_clientes`, `log_citas`, `log_expedientes`, etc.
- Cada log: id, registro_afectado_id, usuario_id, tipo_accion, fecha, detalles, sucursal_id, empresa_id, etc.
- Todo cambio relevante queda registrado.

### 9. Otros
- `notificaciones`, `archivos_adjuntos`, `configuracion_general`, etc.

## Descripción de Relaciones y Lógica
- Una persona puede ser usuario, doctor, paciente, vendedor, etc.
- Un doctor puede estar en varias sucursales y tener diferentes horarios por sucursal.
- Las citas se agendan por sucursal y doctor.
- Cada cita atendida genera un expediente, que se asocia a un caso.
- Un caso puede tener varios expedientes (evolución, revisiones, etc.).
- El historial médico y las imágenes se asocian a los expedientes.
- Los usuarios pueden tener acceso restringido a sucursales específicas.
- Toda acción relevante queda registrada en la tabla de log correspondiente.
- El sistema está preparado para operar en varios países, usando campos genéricos para identificación y tipo de identificación.

## Observaciones
- PostgreSQL es altamente recomendado para este tipo de sistema.
- La estructura propuesta es flexible, escalable y preparada para crecimiento futuro (mobile, multiempresa, multisucursal, auditoría, etc.).
- Se recomienda documentar y versionar cada cambio en la estructura de datos.
