<?php

/**
 * Desarrollo - Permisos de Usuarios - 2026-02-18
 * 
 * Seeder: PermissionsSeeder
 * 
 * Propósito: Sembrar permisos base del sistema
 * Registra todos los permisos generales (CRUD) para cada módulo
 * 
 * Principios aplicados:
 * - Single Responsibility: Solo siembra permisos
 * - DRY: Usa métodos auxiliares para evitar repetición
 * - Documentación exhaustiva
 * 
 * @package Database\Seeders
 */

namespace Database\Seeders;

use App\Models\Permiso;
use App\Models\PlantillaPermiso;
use App\Models\Rol;
use Illuminate\Database\Seeder;

/**
 * Class PermissionsSeeder
 * 
 * Seeder para registrar permisos base del sistema.
 * 
 * @package Database\Seeders
 */
class PermissionsSeeder extends Seeder
{
    /**
     * Módulos del sistema con sus permisos generales.
     * 
     * @var array<string, array<string, string>>
     */
    protected array $modulos = [
        // Módulo de Configuración
        'settings' => [
            'view' => 'Ver configuración',
            'edit' => 'Editar configuración',
        ],

        // Módulo de Usuarios
        'users' => [
            'view' => 'Ver usuarios',
            'create' => 'Crear usuarios',
            'edit' => 'Editar usuarios',
            'delete' => 'Eliminar usuarios',
        ],

        // Módulo de Roles y Permisos
        'permissions' => [
            'view' => 'Ver permisos',
            'edit' => 'Editar permisos',
            'assign' => 'Asignar permisos',
        ],

        // Módulo de Clientes/Pacientes
        'clients' => [
            'view' => 'Ver clientes',
            'create' => 'Crear clientes',
            'edit' => 'Editar clientes',
            'delete' => 'Eliminar clientes',
        ],

        // Módulo de Doctores
        'doctors' => [
            'view' => 'Ver doctores',
            'create' => 'Crear doctores',
            'edit' => 'Editar doctores',
            'delete' => 'Eliminar doctores',
        ],

        // Módulo de Citas
        'appointments' => [
            'view' => 'Ver citas',
            'create' => 'Agendar citas',
            'edit' => 'Editar citas',
            'delete' => 'Cancelar citas',
        ],

        // Módulo de Casos
        'cases' => [
            'view' => 'Ver casos',
            'create' => 'Crear casos',
            'edit' => 'Editar casos',
            'delete' => 'Eliminar casos',
        ],

        // Módulo de Expedientes Médicos
        'medical_records' => [
            'view' => 'Ver expedientes',
            'create' => 'Crear expedientes',
            'edit' => 'Editar expedientes',
            'delete' => 'Eliminar expedientes',
        ],

        // Módulo de Servicios
        'services' => [
            'view' => 'Ver servicios',
            'create' => 'Crear servicios',
            'edit' => 'Editar servicios',
            'delete' => 'Eliminar servicios',
        ],

        // Módulo de Productos
        'products' => [
            'view' => 'Ver productos',
            'create' => 'Crear productos',
            'edit' => 'Editar productos',
            'delete' => 'Eliminar productos',
        ],

        // Módulo de Pagos
        'payments' => [
            'view' => 'Ver pagos',
            'create' => 'Registrar pagos',
            'edit' => 'Editar pagos',
        ],

        // Módulo de Reportes
        'reports' => [
            'view' => 'Ver reportes',
            'export' => 'Exportar reportes',
        ],

        // Módulo de Sucursales
        'branches' => [
            'view' => 'Ver sucursales',
            'create' => 'Crear sucursales',
            'edit' => 'Editar sucursales',
            'delete' => 'Eliminar sucursales',
        ],
    ];

    /**
     * Permisos granulares por módulo.
     * 
     * @var array<string, array<string, string>>
     */
    protected array $permisosGranulares = [
        'users' => [
            'permissions' => 'Gestionar permisos de usuarios',
            'roles' => 'Asignar roles a usuarios',
            'activate_deactivate' => 'Activar/desactivar usuarios',
        ],

        'products' => [
            'taxes' => 'Gestionar impuestos de productos',
            'accounting' => 'Gestionar cuentas contables',
            'pricing' => 'Cambiar precios y costos',
            'inventory' => 'Gestionar inventario',
        ],

        'medical_records' => [
            'sign_digital' => 'Firmar digitalmente documentos',
            'histories' => 'Ver historial médico completo',
            'images' => 'Gestionar imágenes médicas',
            'export' => 'Exportar expedientes',
            'print' => 'Imprimir expedientes',
        ],

        'appointments' => [
            'reschedule' => 'Reprogramar citas',
            'confirm' => 'Confirmar citas',
            'own_appointments' => 'Solo ver citas propias',
            'all_appointments' => 'Ver todas las citas',
        ],

        'cases' => [
            'close_case' => 'Cerrar casos',
            'transfer' => 'Transferir casos',
        ],

        'clients' => [
            'medical_history' => 'Ver historial médico de clientes',
            'export_data' => 'Exportar datos de clientes',
        ],

        'doctors' => [
            'schedules' => 'Gestionar horarios de doctores',
            'assignments' => 'Asignar doctores a sucursales',
            'specialties' => 'Gestionar especialidades',
        ],

        'reports' => [
            'financial' => 'Ver reportes financieros',
            'medical' => 'Ver reportes médicos',
            'audit_logs' => 'Ver logs de auditoría',
        ],

        'settings' => [
            'empresa' => 'Configurar datos de empresa',
            'sucursales' => 'Gestionar sucursales',
            'logos' => 'Cambiar logos',
            'monedas' => 'Gestionar monedas',
        ],
    ];

    /**
     * Ejecutar el seeder.
     * 
     * @return void
     */
    public function run(): void
    {
        $this->command->info('🌱 Iniciando seeder de permisos...');

        // Registrar permisos generales
        $this->registerGeneralPermissions();

        // Registrar permisos granulares
        $this->registerGranularPermissions();

        // Crear plantillas de permisos
        $this->createPermissionTemplates();

        // Crear roles del sistema
        $this->createSystemRoles();

        $this->command->info('✅ Permisos registrados exitosamente.');
    }

    /**
     * Registrar permisos generales (CRUD) para cada módulo.
     * 
     * @return void
     */
    protected function registerGeneralPermissions(): void
    {
        $this->command->info('📋 Registrando permisos generales...');

        foreach ($this->modulos as $modulo => $permisos) {
            foreach ($permisos as $codigo => $nombre) {
                Permiso::firstOrCreate(
                    ['modulo' => $modulo, 'codigo' => $codigo],
                    [
                        'nombre' => $nombre,
                        'descripcion' => "Permiso para {$nombre} en el módulo {$modulo}",
                        'tipo' => Permiso::TIPO_GENERAL,
                        'estado' => true,
                    ]
                );
            }
        }

        $totalGenerales = Permiso::where('tipo', Permiso::TIPO_GENERAL)->count();
        $this->command->info("   ✓ {$totalGenerales} permisos generales registrados.");
    }

    /**
     * Registrar permisos granulares (especiales) para cada módulo.
     * 
     * @return void
     */
    protected function registerGranularPermissions(): void
    {
        $this->command->info('🔍 Registrando permisos granulares...');

        foreach ($this->permisosGranulares as $modulo => $permisos) {
            foreach ($permisos as $codigo => $nombre) {
                Permiso::firstOrCreate(
                    ['modulo' => $modulo, 'codigo' => $codigo],
                    [
                        'nombre' => $nombre,
                        'descripcion' => "Permiso especial para {$nombre} en el módulo {$modulo}",
                        'tipo' => Permiso::TIPO_GRANULAR,
                        'estado' => true,
                    ]
                );
            }
        }

        $totalGranulares = Permiso::where('tipo', Permiso::TIPO_GRANULAR)->count();
        $this->command->info("   ✓ {$totalGranulares} permisos granulares registrados.");
    }

    /**
     * Crear plantillas de permisos predefinidas.
     * 
     * @return void
     */
    protected function createPermissionTemplates(): void
    {
        $this->command->info('📁 Creando plantillas de permisos...');

        // Plantilla Administrador (todos los permisos)
        $adminTemplate = PlantillaPermiso::firstOrCreate(
            ['nombre' => 'Administrador'],
            [
                'descripcion' => 'Acceso completo a todos los módulos y permisos del sistema',
                'es_sistema' => true,
                'es_activa' => true,
                'orden' => 1,
                'color' => '#667eea',
                'icono' => 'ti ti-shield-lock',
            ]
        );

        // Asignar todos los permisos al administrador
        $todosPermisos = Permiso::all();
        foreach ($todosPermisos as $permiso) {
            $adminTemplate->agregarPermiso($permiso->id);
        }

        // Plantilla Recepcionista
        $recepcionista = PlantillaPermiso::firstOrCreate(
            ['nombre' => 'Recepcionista'],
            [
                'descripcion' => 'Gestión de citas, clientes y operaciones básicas',
                'es_sistema' => true,
                'es_activa' => true,
                'orden' => 2,
                'color' => '#11998e',
                'icono' => 'ti ti-reception',
            ]
        );

        // Asignar permisos de recepcionista
        $permisosRecepcionista = Permiso::whereIn('modulo', [
            'clients', 'appointments', 'branches'
        ])->whereIn('codigo', ['view', 'create', 'edit'])->get();

        foreach ($permisosRecepcionista as $permiso) {
            $recepcionista->agregarPermiso($permiso->id);
        }

        // Plantilla Doctor
        $doctor = PlantillaPermiso::firstOrCreate(
            ['nombre' => 'Doctor'],
            [
                'descripcion' => 'Gestión de pacientes, expedientes y citas médicas',
                'es_sistema' => true,
                'es_activa' => true,
                'orden' => 3,
                'color' => '#f093fb',
                'icono' => 'ti ti-user-md',
            ]
        );

        // Asignar permisos de doctor
        $permisosDoctor = Permiso::whereIn('modulo', [
            'clients', 'appointments', 'medical_records', 'cases'
        ])->get();

        foreach ($permisosDoctor as $permiso) {
            $doctor->agregarPermiso($permiso->id);
        }

        // Agregar permisos granulares para doctores
        $permisosGranularesDoctor = Permiso::where('modulo', 'medical_records')
            ->whereIn('codigo', ['sign_digital', 'histories', 'images'])
            ->get();

        foreach ($permisosGranularesDoctor as $permiso) {
            $doctor->agregarPermiso($permiso->id);
        }

        $this->command->info("   ✓ 3 plantillas de permisos creadas.");
    }

    /**
     * Crear roles del sistema asociados a plantillas.
     * 
     * @return void
     */
    protected function createSystemRoles(): void
    {
        $this->command->info('🎭 Creando roles del sistema...');

        $plantillas = PlantillaPermiso::all()->keyBy('nombre');

        // Rol Administrador
        $adminRol = Rol::firstOrCreate(
            ['nombre' => 'Administrador'],
            [
                'descripcion' => 'Administrador del sistema con acceso completo',
                'plantilla_id' => $plantillas['Administrador']?->id,
                'es_sistema' => true,
            ]
        );
        
        // Asegurar que el rol de Administrador tenga la plantilla asignada
        if ($plantillas['Administrador'] && !$adminRol->plantilla_id) {
            $adminRol->update(['plantilla_id' => $plantillas['Administrador']->id]);
        }

        // Rol Super Administrador (mismo que Administrador pero con énfasis en que es el principal)
        Rol::firstOrCreate(
            ['nombre' => 'Super Administrador'],
            [
                'descripcion' => 'Super Administrador con todos los permisos del sistema',
                'plantilla_id' => $plantillas['Administrador']?->id,
                'es_sistema' => true,
            ]
        );

        // Rol Recepcionista
        Rol::firstOrCreate(
            ['nombre' => 'Recepcionista'],
            [
                'descripcion' => 'Personal de recepción que gestiona citas y clientes',
                'plantilla_id' => $plantillas['Recepcionista']?->id,
                'es_sistema' => true,
            ]
        );

        // Rol Doctor
        Rol::firstOrCreate(
            ['nombre' => 'Doctor'],
            [
                'descripcion' => 'Doctor que atiende pacientes y gestiona expedientes',
                'plantilla_id' => $plantillas['Doctor']?->id,
                'es_sistema' => true,
            ]
        );

        $this->command->info("   ✓ 4 roles del sistema creados.");
    }
}
