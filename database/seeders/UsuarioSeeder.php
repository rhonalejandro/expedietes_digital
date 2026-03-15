<?php

/**
 * Desarrollo - Permisos de Usuarios - Seeders - 2026-02-18
 * 
 * Seeder: UsuarioSeeder (Actualizado)
 * 
 * Cambios realizados:
 * - Ahora crea al usuario con rol de Super Administrador
 * - Asigna la plantilla de Administrador con todos los permisos
 * - Se asegura que el usuario tenga todos los permisos directos también
 * 
 * @package Database\Seeders
 */

namespace Database\Seeders;

use App\Models\Persona;
use App\Models\Usuario;
use App\Models\Rol;
use App\Models\PlantillaPermiso;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Class UsuarioSeeder
 * 
 * Crea el usuario administrador con permisos completos.
 */
class UsuarioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * @return void
     */
    public function run(): void
    {
        // Crear persona base para el usuario admin
        $persona = Persona::firstOrCreate(
            ['identificacion' => 'admin@krom-soft.com'],
            [
                'nombre' => 'Super',
                'apellido' => 'Administrador',
                'tipo_identificacion' => 'correo',
                'email' => 'admin@krom-soft.com',
                'estado' => true,
            ]
        );

        // Crear o actualizar usuario admin
        $usuario = Usuario::firstOrCreate(
            ['email' => 'admin@krom-soft.com'],
            [
                'persona_id' => $persona->id,
                'nombre' => 'Administrador',
                'password' => Hash::make('Rhonald16*'),
                'estado' => true,
            ]
        );

        // Actualizar nombre si es necesario
        if ($usuario->nombre !== 'Administrador') {
            $usuario->update(['nombre' => 'Administrador']);
        }

        // Obtener o crear rol de Super Administrador
        $rol = Rol::firstOrCreate(
            ['nombre' => 'Super Administrador'],
            [
                'descripcion' => 'Acceso total al sistema con todos los permisos',
                'es_sistema' => true,
            ]
        );

        // Asignar rol al usuario
        $usuario->roles()->syncWithoutDetaching([$rol->id]);

        // Asignar plantilla de Administrador (con todos los permisos)
        $plantillaAdmin = PlantillaPermiso::where('nombre', 'Administrador')->first();
        
        if ($plantillaAdmin) {
            // Asignar plantilla al rol si no la tiene
            if (!$rol->plantilla_id) {
                $rol->update(['plantilla_id' => $plantillaAdmin->id]);
            }
        }

        // Asignar TODOS los permisos directamente al usuario también
        $this->assignAllPermissionsToUser($usuario);
    }

    /**
     * Asignar todos los permisos activos a un usuario.
     * 
     * @param Usuario $usuario
     * @return void
     */
    private function assignAllPermissionsToUser(Usuario $usuario): void
    {
        $todosPermisos = \App\Models\Permiso::where('estado', true)->get();
        
        foreach ($todosPermisos as $permiso) {
            $usuario->permisos()->syncWithoutDetaching([$permiso->id]);
        }
    }
}
