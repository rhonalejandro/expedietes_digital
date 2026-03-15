<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('horarios_doctores', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('PK, autoincremental');
            $table->unsignedBigInteger('doctor_id')->comment('FK a doctores');
            $table->unsignedBigInteger('sucursal_id')->comment('FK a sucursales');
            $table->string('dia_semana', 20)->comment('Día de la semana');
            $table->time('hora_inicio')->comment('Hora de inicio');
            $table->time('hora_fin')->comment('Hora de fin');
            $table->integer('duracion_cita')->comment('Duración en minutos');
            $table->integer('citas_maximas')->nullable()->comment('Citas máximas por día');
            $table->timestamps();

            $table->foreign('doctor_id')->references('id')->on('doctores')->onDelete('cascade');
            $table->foreign('sucursal_id')->references('id')->on('sucursales')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('horarios_doctores');
    }
};
