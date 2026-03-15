<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('personas', function (Blueprint $table) {
            $table->string('ocupacion', 100)->nullable()->after('genero')->comment('Ocupación de la persona');
            $table->string('nacionalidad', 50)->nullable()->after('ocupacion')->comment('Nacionalidad');
            $table->string('seguro_medico', 100)->nullable()->after('nacionalidad')->comment('Seguro médico');
            $table->string('contacto_emergencia', 100)->nullable()->after('seguro_medico')->comment('Contacto de emergencia');
        });
    }

    public function down(): void
    {
        Schema::table('personas', function (Blueprint $table) {
            $table->dropColumn(['ocupacion', 'nacionalidad', 'seguro_medico', 'contacto_emergencia']);
        });
    }
};
