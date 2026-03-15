<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('archivos_adjuntos', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('PK, autoincremental');
            $table->string('modulo', 50)->comment('Módulo relacionado');
            $table->unsignedBigInteger('registro_id')->comment('ID del registro relacionado');
            $table->string('ruta', 255)->comment('Ruta del archivo');
            $table->string('descripcion', 255)->nullable()->comment('Descripción del archivo');
            $table->timestamp('fecha')->useCurrent()->comment('Fecha de carga');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('archivos_adjuntos');
    }
};
