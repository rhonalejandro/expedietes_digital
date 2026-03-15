## Estructura de Tablas y Relaciones Clave

1. **Doctores**
2. **Pacientes**
3. **Servicios**
4. **Horarios de Doctores**
   - Días, horas, duración de cita, citas máximas por día, etc.
5. **Citas**
   - doctor_id, paciente_id, fecha, hora_inicio, hora_fin, estatus (confirmada, por confirmar, cancelada, atendida), observaciones, etc.
6. **Usuarios**
7. **Roles**
8. **Permisos**
9. **Casos**
   - paciente_id, doctor_id, descripción, fecha_apertura, estado, etc.
10. **Expedientes**
  - caso_id, cita_id, doctor_id, paciente_id, fecha, diagnóstico, tratamiento, notas, etc.
11. **Historial Médico**
  - expediente_id, fecha, evolución, observaciones, imágenes, etc.
12. **Imágenes**
  - historial_id o expediente_id, ruta, descripción, etc.
13. **Tablas de Auditoría/Logs por Módulo**
  - Cada módulo (clientes, citas, expedientes, traslados, notas, etc.) tendrá su propia tabla de log para registrar acciones relevantes: creación, edición, eliminación, traslados, cambios de fecha, etc. Cada log debe tener foreign key al registro afectado, usuario que realizó la acción, tipo de acción, fecha y detalles.

## Lógica de Negocio y Auditoría

- El horario del doctor define la disponibilidad, duración y cantidad máxima de citas.
- La tabla de citas gestiona la agenda y el estatus de cada cita.
- Cada cita atendida genera un expediente médico.
- El expediente está ligado a un caso (ejemplo: “uña encarnada”).
- Un caso puede tener varios expedientes (evolución, revisiones, etc.).
- Si una cita no tiene caso asociado, se crea uno nuevo.
- El historial médico se asocia a cada expediente y puede incluir imágenes.
- El doctor solo puede ver las citas y expedientes de los pacientes que atiende.
- Toda acción relevante (registro, edición, traslado, nota, cambio de fecha, etc.) debe quedar registrada en la tabla de log correspondiente al módulo.

## Internacionalización y Datos de Identificación

- El sistema debe estar preparado para operar en Panamá y otros países.
- Para los datos de identificación de personas, se utilizarán los campos:
  - `identificacion`: valor del documento de identidad.
  - `tipo_identificacion`: tipo de documento (cédula, pasaporte, etc.), sin restringir a formatos locales como RUC, CI, RIF, etc.

## Recomendaciones Adicionales

- Considerar tablas para notificaciones, archivos adjuntos y configuración general.
- Usar relaciones claras en las migraciones (foreign keys y claves de búsqueda).
- Documentar bien cada tabla y relación en este archivo.
# Contexto del Proyecto: Expediente Digital para Clínica Podológica

## Descripción General
Este sistema es una aplicación de expediente digital diseñada para la gestión clínica podológica. El objetivo es crear una plataforma robusta, escalable y mantenible, que permita la futura integración con aplicaciones móviles y garantice la máxima organización y reutilización de código.

## Principios y Buenas Prácticas
- **SOLID**: Cada clase y módulo debe cumplir con los principios SOLID para asegurar flexibilidad, mantenibilidad y escalabilidad.
- **DRY (Don't Repeat Yourself)**: Evitar la duplicación de código. Antes de crear nuevas funciones, revisar la existencia de implementaciones similares.
- **Open/Closed**: El sistema debe estar abierto a la extensión pero cerrado a la modificación, permitiendo agregar nuevas funcionalidades sin alterar el código existente.
- **Documentación exhaustiva**: Todo el código debe estar bien documentado. No se permiten iconos, logs, ni uso de `dd()` en la documentación.
- **Uso de TODO**: Utilizar comentarios TODO para marcar tareas pendientes o mejoras identificadas.

## Organización y Estructura
- **Pensando en Mobile**: Desde el inicio, la arquitectura debe considerar la futura integración con aplicaciones móviles.
- **Organización de Recursos**:
  - Todos los archivos de estilos y JavaScript deben estar descargados y organizados en la carpeta `public`.
  - Estructura recomendada:
    - `public/components/` para componentes independientes.
    - `public/modules/` para módulos completos.
    - Dentro de cada componente o módulo:
      - `js/` para scripts JavaScript.
      - `css/` para hojas de estilo.
  - Cada componente o módulo debe tener sus propios archivos CSS y JS, evitando archivos con más de 1000 líneas de código (responsabilidad única).
  - Se recomienda que las vistas y componentes no superen las 300-500 líneas de código. Si un archivo crece demasiado, dividirlo en partials, subcomponentes o módulos para mantener la legibilidad y facilidad de mantenimiento.
  - Nunca usar gradientes en el CSS bajo ninguna circunstancia.
  - Una hoja de estilo general para estilos globales.
  - Utilizar la última versión de Bootstrap como base de estilos.

## Reutilización, Vistas y Controladores
- Analizar y definir controladores compartidos con responsabilidades claras para maximizar la reutilización y evitar duplicidad de código.
- Documentar exhaustivamente cada controlador y sus métodos.
- Las vistas no pueden ser largas: aplicar responsabilidad única en cada módulo desarrollado.
- Si una vista crece demasiado, dividirla en partials o usar componentes. Si un componente puede ser reutilizado por otro módulo, implementarlo como componente reutilizable.

## Índice de Consultas
- Crear un archivo llamado `indice_consultas.md`.
- Por cada operación a base de datos (SELECT, UPDATE, CREATE, DELETE):
  - Registrar:
    - Nombre de la tabla
    - Ejemplo de consulta
    - Nombre del controlador que realiza la consulta
    - Condiciones de la consulta
    - Objetivo de la consulta
    - Campos retornados
    - Si hay JOIN, especificar las tablas involucradas
- Antes de crear una nueva consulta, revisar este índice para evitar duplicidad y fomentar la reutilización.

## Metodologías y Organización
- Fomentar la responsabilidad única en cada archivo, clase y función.
- Mantener la estructura modular y escalable.
- Si se identifica una mejor metodología de organización, evaluarla e implementarla si aporta valor.

## Resumen
Este contexto debe ser consultado y respetado en cada etapa del desarrollo. La organización, documentación y reutilización son pilares fundamentales para el éxito y escalabilidad del sistema.
