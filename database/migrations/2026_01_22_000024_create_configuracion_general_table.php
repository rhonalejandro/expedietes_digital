<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('configuracion_general', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('PK, autoincremental');
            $table->string('clave', 100)->comment('Nombre de la configuración');
            $table->string('valor', 255)->comment('Valor de la configuración');
            $table->string('descripcion', 255)->nullable()->comment('Descripción');
            $table->timestamp('fecha')->useCurrent()->comment('Fecha de la configuración');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('configuracion_general');
    }
};
