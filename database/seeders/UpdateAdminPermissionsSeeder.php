<?php

/**
 * Desarrollo - Permisos de Usuarios - Seeders - 2026-02-18
 * 
 * Seeder: UpdateAdminPermissionsSeeder
 * 
 * Propósito: Actualizar usuario admin con todos los permisos
 * Ejecutar después de haber corrido PermissionsSeeder
 * 
 * Uso:
 *   php artisan db:seed --class=UpdateAdminPermissionsSeeder
 * 
 * @package Database\Seeders
 */

namespace Database\Seeders;

use App\Models\Usuario;
use App\Models\Permiso;
use App\Models\Rol;
use App\Models\PlantillaPermiso;
use Illuminate\Database\Seeder;

/**
 * Class UpdateAdminPermissionsSeeder
 */
class UpdateAdminPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * @return void
     */
    public function run(): void
    {
        $this->command->info('🔐 Actualizando permisos del administrador...');

        // Buscar usuario admin
        $admin = Usuario::where('email', 'admin@krom-soft.com')->first();

        if (!$admin) {
            $this->command->error('❌ No se encontró el usuario admin@krom-soft.com');
            return;
        }

        $this->command->info("   ✓ Usuario encontrado: {$admin->email}");

        // Obtener rol de Super Administrador
        $rolSuperAdmin = Rol::where('nombre', 'Super Administrador')->first();

        if (!$rolSuperAdmin) {
            $this->command->warn('⚠ Creando rol Super Administrador...');
            $rolSuperAdmin = Rol::create([
                'nombre' => 'Super Administrador',
                'descripcion' => 'Acceso total al sistema con todos los permisos',
                'es_sistema' => true,
            ]);
        }

        // Asignar rol al usuario
        $admin->roles()->syncWithoutDetaching([$rolSuperAdmin->id]);
        $this->command->info("   ✓ Rol 'Super Administrador' asignado");

        // Obtener o crear plantilla de Administrador
        $plantillaAdmin = PlantillaPermiso::where('nombre', 'Administrador')->first();

        if (!$plantillaAdmin) {
            $this->command->warn('⚠ Creando plantilla de Administrador...');
            $plantillaAdmin = PlantillaPermiso::create([
                'nombre' => 'Administrador',
                'descripcion' => 'Acceso completo a todos los módulos y permisos del sistema',
                'es_sistema' => true,
                'es_activa' => true,
                'orden' => 1,
                'color' => '#667eea',
                'icono' => 'ti ti-shield-lock',
            ]);

            // Asignar todos los permisos a la plantilla
            $todosPermisos = Permiso::where('estado', true)->get();
            foreach ($todosPermisos as $permiso) {
                $plantillaAdmin->agregarPermiso($permiso->id);
            }

            $this->command->info("   ✓ Plantilla 'Administrador' creada con " . $todosPermisos->count() . " permisos");
        }

        // Asignar plantilla al rol
        if (!$rolSuperAdmin->plantilla_id) {
            $rolSuperAdmin->update(['plantilla_id' => $plantillaAdmin->id]);
            $this->command->info("   ✓ Plantilla asignada al rol Super Administrador");
        }

        // Asignar TODOS los permisos directamente al usuario
        $todosPermisos = Permiso::where('estado', true)->get();
        $contador = 0;

        foreach ($todosPermisos as $permiso) {
            $admin->permisos()->syncWithoutDetaching([$permiso->id]);
            $contador++;
        }

        $this->command->info("   ✓ {$contador} permisos asignados directamente al usuario");

        // Verificar permisos finales
        $permisosTotales = $admin->getAllPermisos()->count();
        $this->command->info("   ✓ Total de permisos del usuario: {$permisosTotales}");

        $this->command->info("✅ Usuario admin actualizado exitosamente como SUPER ADMIN");
        $this->command->info("");
        $this->command->info("📋 Datos de acceso:");
        $this->command->info("   Email: admin@krom-soft.com");
        $this->command->info("   Password: Rhonald16*");
        $this->command->info("");
        $this->command->info("🎯 Permisos:");
        $this->command->info("   - Rol: Super Administrador");
        $this->command->info("   - Plantilla: Administrador (todos los permisos)");
        $this->command->info("   - Permisos directos: {$contador} permisos");
        $this->command->info("   - Total efectivo: {$permisosTotales} permisos");
    }
}
