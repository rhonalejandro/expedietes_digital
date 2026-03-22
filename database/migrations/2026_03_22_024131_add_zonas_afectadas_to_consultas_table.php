<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('consultas', function (Blueprint $table) {
            // JSON: {"izquierdo":["talon","dedo_1","uña_1"],"derecho":["antepi","dedo_3"]}
            $table->json('zonas_afectadas')->nullable()->after('indicaciones');
        });
    }

    public function down(): void
    {
        Schema::table('consultas', function (Blueprint $table) {
            $table->dropColumn('zonas_afectadas');
        });
    }
};
