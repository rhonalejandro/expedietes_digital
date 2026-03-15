<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MonedaSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('monedas')->updateOrInsert(
            ['codigo' => 'VES'],
            [
                'nombre' => 'Bolívar',
                'simbolo' => 'Bs.',
                'por_defecto' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
        DB::table('monedas')->updateOrInsert(
            ['codigo' => 'USD'],
            [
                'nombre' => 'Dólar estadounidense',
                'simbolo' => '$',
                'por_defecto' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }
}
