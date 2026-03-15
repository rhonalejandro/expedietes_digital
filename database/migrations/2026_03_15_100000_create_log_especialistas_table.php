<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('log_especialistas', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('especialista_id');
            $table->unsignedBigInteger('usuario_id');
            $table->string('tipo_accion', 50);
            $table->timestamp('fecha')->useCurrent();
            $table->text('detalles')->nullable();
            $table->unsignedBigInteger('sucursal_id')->nullable();

            $table->foreign('especialista_id')->references('id')->on('especialistas')->onDelete('cascade');
            $table->foreign('usuario_id')->references('id')->on('usuarios')->onDelete('cascade');
            $table->foreign('sucursal_id')->references('id')->on('sucursales')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('log_especialistas');
    }
};
