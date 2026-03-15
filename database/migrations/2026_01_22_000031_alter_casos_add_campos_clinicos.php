<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('casos', function (Blueprint $table) {
            $table->string('motivo', 255)->nullable()->after('doctor_id')->comment('Motivo de consulta');
            $table->date('fecha_cierre')->nullable()->after('fecha_apertura')->comment('Fecha de cierre del caso');
            $table->text('notas_iniciales')->nullable()->after('fecha_cierre')->comment('Notas iniciales del caso');
        });
    }

    public function down(): void
    {
        Schema::table('casos', function (Blueprint $table) {
            $table->dropColumn(['motivo', 'fecha_cierre', 'notas_iniciales']);
        });
    }
};
