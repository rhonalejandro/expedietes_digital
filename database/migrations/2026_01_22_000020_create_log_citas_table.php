<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('log_citas', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('PK, autoincremental');
            $table->unsignedBigInteger('cita_id')->comment('FK a citas');
            $table->unsignedBigInteger('usuario_id')->comment('FK a usuarios');
            $table->string('tipo_accion', 50)->comment('Acción realizada');
            $table->timestamp('fecha')->useCurrent()->comment('Fecha de la acción');
            $table->text('detalles')->nullable()->comment('Detalles de la acción');
            $table->unsignedBigInteger('sucursal_id')->nullable()->comment('FK a sucursales');
            // $table->unsignedBigInteger('empresa_id')->nullable()->comment('FK a empresas');

            $table->foreign('cita_id')->references('id')->on('citas')->onDelete('cascade');
            $table->foreign('usuario_id')->references('id')->on('usuarios')->onDelete('cascade');
            $table->foreign('sucursal_id')->references('id')->on('sucursales')->onDelete('set null');
            // $table->foreign('empresa_id')->references('id')->on('empresas')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('log_citas');
    }
};
