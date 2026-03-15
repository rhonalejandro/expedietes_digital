<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Database\Seeders\MonedaSeeder;
use Database\Seeders\ConfiguracionGeneralSeeder;
use Database\Seeders\UsuarioSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            MonedaSeeder::class,
            ConfiguracionGeneralSeeder::class,
            UsuarioSeeder::class,
        ]);
    }
}
