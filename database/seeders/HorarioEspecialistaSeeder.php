<?php

namespace Database\Seeders;

use App\Models\Especialista;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HorarioEspecialistaSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('horarios_especialistas')->truncate();

        $dias = ['lunes', 'martes', 'miercoles', 'jueves', 'viernes'];

        $especialistas = Especialista::all();

        foreach ($especialistas as $esp) {
            foreach ($dias as $dia) {
                DB::table('horarios_especialistas')->insert([
                    'especialista_id' => $esp->id,
                    'sucursal_id'     => 1,
                    'dia_semana'      => $dia,
                    'hora_inicio'     => '09:00:00',
                    'hora_fin'        => '17:00:00',
                    'duracion_cita'   => 30,
                    'citas_maximas'   => 16, // 8h / 30min
                    'created_at'      => now(),
                    'updated_at'      => now(),
                ]);
            }
        }

        $this->command->info('Horarios creados para ' . $especialistas->count() . ' especialistas.');
    }
}
