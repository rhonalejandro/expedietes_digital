<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('sucursales', function (Blueprint $table) {
            // Renombrar contacto a telefono si existe
            if (Schema::hasColumn('sucursales', 'contacto')) {
                $table->renameColumn('contacto', 'telefono');
            }
            
            // Agregar encargado si no existe
            if (!Schema::hasColumn('sucursales', 'encargado')) {
                $table->string('encargado', 100)->nullable()->after('email');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sucursales', function (Blueprint $table) {
            if (Schema::hasColumn('sucursales', 'encargado')) {
                $table->dropColumn('encargado');
            }
            
            if (Schema::hasColumn('sucursales', 'telefono')) {
                $table->renameColumn('telefono', 'contacto');
            }
        });
    }
};
