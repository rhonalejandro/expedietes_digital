<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('empresas', function (Blueprint $table) {
            $table->json('colores_estatus')->nullable()->after('modo_agenda');
        });

        // Poblar con los colores por defecto
        DB::table('empresas')->update([
            'colores_estatus' => json_encode([
                'pendiente'  => '#64748b',
                'confirmada' => '#2f8a59',
                'atendida'   => '#2d3748',
                'cancelada'  => '#c53030',
                'no_asistio' => '#c05621',
            ]),
        ]);
    }

    public function down(): void
    {
        Schema::table('empresas', function (Blueprint $table) {
            $table->dropColumn('colores_estatus');
        });
    }
};
