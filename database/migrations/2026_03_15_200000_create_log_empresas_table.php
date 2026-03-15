<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('log_empresas', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('empresa_id')->comment('FK a empresas');
            $table->unsignedBigInteger('usuario_id')->comment('FK a usuarios');
            $table->string('tipo_accion', 50)->comment('Acción realizada');
            $table->timestamp('fecha')->useCurrent()->comment('Fecha y hora de la acción');
            $table->jsonb('detalles')->nullable()->comment('Cambios en formato JSON');
            $table->unsignedBigInteger('sucursal_id')->nullable()->comment('FK a sucursales');

            $table->foreign('empresa_id')->references('id')->on('empresas')->onDelete('cascade');
            $table->foreign('usuario_id')->references('id')->on('usuarios')->onDelete('cascade');
            $table->foreign('sucursal_id')->references('id')->on('sucursales')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('log_empresas');
    }
};
