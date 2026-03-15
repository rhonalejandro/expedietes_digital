<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('sucursales', function (Blueprint $table) {
            $table->string('imagen')->nullable()->comment('Imagen/foto de la sucursal')->after('estado');
            $table->softDeletes()->comment('Soft delete para eliminación lógica');
        });
    }

    public function down(): void
    {
        Schema::table('sucursales', function (Blueprint $table) {
            $table->dropColumn('imagen');
            $table->dropSoftDeletes();
        });
    }
};
