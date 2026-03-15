# Contexto Primera Parte

## Resumen General del Sistema
Este sistema es una aplicación de expediente digital para una clínica podológica, diseñada para ser multiempresa, multisucursal y escalable, con la posibilidad de integración futura con aplicaciones móviles. El desarrollo sigue principios SOLID, DRY y Open/Closed, priorizando la organización, la documentación y la reutilización de código.

## Decisiones Técnicas y Arquitectura
- **Framework:** Laravel (PHP)
- **Base de datos:** PostgreSQL (instalado y configurado en WSL)
- **Estructura modular:** Separación clara de modelos, migraciones, controladores y recursos públicos.
- **Internacionalización:** Uso de campos genéricos para identificación y tipo de identificación, adaptable a cualquier país.
- **Auditoría:** Tablas de logs por módulo para registrar toda acción relevante.
- **Documentación:** Cada tabla y modelo está documentado, con comentarios en migraciones y relaciones Eloquent en los modelos.

## Estructura de Datos y Migraciones
Se han creado migraciones para todas las tablas principales y auxiliares, incluyendo:
- personas, empresas, sucursales, usuarios, roles, usuario_rol, usuario_sucursal, doctores, doctor_sucursal, pacientes, horarios_doctores, citas, casos, expedientes, historial_medico, imagenes, servicios, pagos, log_clientes, log_citas, log_expedientes, notificaciones, archivos_adjuntos, configuracion_general.
- Cada campo en las migraciones tiene un comentario descriptivo para facilitar la comprensión desde la base de datos.
- Se han definido claves foráneas y relaciones muchos a muchos mediante tablas pivote.

## Modelos Eloquent
- Se han generado modelos para cada entidad principal y auxiliar.
- Cada modelo tiene documentación y relaciones Eloquent definidas (hasOne, hasMany, belongsTo, belongsToMany, etc.).
- Los modelos pivote (usuario_rol, usuario_sucursal, doctor_sucursal) están implementados como clases Pivot.

## Relaciones Clave
- Una persona puede ser usuario, paciente, doctor, etc., sin duplicidad de datos.
- Un usuario puede tener múltiples roles y acceso a varias sucursales.
- Un doctor puede estar asignado a varias sucursales y tener diferentes horarios por sucursal.
- Las citas se agendan por sucursal, doctor y paciente, y cada cita atendida genera un expediente médico.
- Un caso puede tener varios expedientes (evolución, revisiones, etc.), y cada expediente puede tener historial médico e imágenes asociadas.
- Toda acción relevante queda registrada en la tabla de log correspondiente.

## Organización de Recursos
- Los recursos públicos (CSS, JS) se organizarán en public/components y public/modules, con subcarpetas js y css para cada módulo o componente.
- Bootstrap se usará como base de estilos, y cada módulo tendrá sus propios estilos y scripts independientes.

## Buenas Prácticas y Reglas
- Prohibido el uso de gradientes en CSS.
- Las vistas deben ser cortas y divididas en partials o componentes reutilizables.
- Reutilización de controladores y funciones, evitando duplicidad de código.
- Documentación exhaustiva en código y migraciones.
- Auditoría y logs por módulo, con responsabilidad única.
- Preparado para internacionalización y crecimiento futuro (mobile, multiempresa, multisucursal).

## Estado Actual
- Migraciones y modelos completos y documentados.
- Base de datos PostgreSQL lista y conectada.
- Estructura de carpetas y organización definida.
- Listo para avanzar a controladores, seeders, factories, lógica de negocio y vistas.

---

Este documento resume todo lo realizado hasta ahora y sirve como referencia para cualquier desarrollador que se integre al proyecto. Si necesitas detalles de alguna tabla, modelo o relación, consulta los archivos estructura_y_analisis_de_datos.md y los modelos en app/Models.
