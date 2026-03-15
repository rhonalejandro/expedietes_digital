<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('citas', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('PK, autoincremental');
            $table->unsignedBigInteger('doctor_id')->comment('FK a doctores');
            $table->unsignedBigInteger('paciente_id')->comment('FK a pacientes');
            $table->unsignedBigInteger('sucursal_id')->comment('FK a sucursales');
            $table->date('fecha')->comment('Fecha de la cita');
            $table->time('hora_inicio')->comment('Hora de inicio');
            $table->time('hora_fin')->comment('Hora de fin');
            $table->string('estatus', 30)->comment('Estado (confirmada, etc.)');
            $table->text('observaciones')->nullable()->comment('Observaciones');
            $table->timestamps();

            $table->foreign('doctor_id')->references('id')->on('doctores')->onDelete('cascade');
            $table->foreign('paciente_id')->references('id')->on('pacientes')->onDelete('cascade');
            $table->foreign('sucursal_id')->references('id')->on('sucursales')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('citas');
    }
};
