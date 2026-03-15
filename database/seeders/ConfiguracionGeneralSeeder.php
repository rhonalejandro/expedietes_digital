<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ConfiguracionGeneralSeeder extends Seeder
{
    public function run(): void
    {
        $configuraciones = [
            [
                'clave' => 'formato_fecha',
                'valor' => 'd/m/Y',
                'descripcion' => 'Formato de fecha por defecto',
                'fecha' => now(),
            ],
            [
                'clave' => 'separador_decimal',
                'valor' => ',',
                'descripcion' => 'Separador decimal por defecto',
                'fecha' => now(),
            ],
            [
                'clave' => 'separador_miles',
                'valor' => '.',
                'descripcion' => 'Separador de miles por defecto',
                'fecha' => now(),
            ],
            [
                'clave' => 'moneda_defecto',
                'valor' => 'VES',
                'descripcion' => 'Código de la moneda por defecto',
                'fecha' => now(),
            ],
        ];

        foreach ($configuraciones as $config) {
            DB::table('configuracion_general')->updateOrInsert(
                ['clave' => $config['clave']],
                $config
            );
        }
    }
}
