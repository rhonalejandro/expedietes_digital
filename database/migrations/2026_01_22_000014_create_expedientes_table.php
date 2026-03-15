<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('expedientes', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('PK, autoincremental');
            $table->unsignedBigInteger('caso_id')->comment('FK a casos');
            $table->unsignedBigInteger('cita_id')->comment('FK a citas');
            $table->unsignedBigInteger('doctor_id')->comment('FK a doctores');
            $table->unsignedBigInteger('paciente_id')->comment('FK a pacientes');
            $table->date('fecha')->comment('Fecha del expediente');
            $table->text('diagnostico')->nullable()->comment('Diagnóstico');
            $table->text('tratamiento')->nullable()->comment('Tratamiento');
            $table->text('notas')->nullable()->comment('Notas adicionales');
            $table->timestamps();

            $table->foreign('caso_id')->references('id')->on('casos')->onDelete('cascade');
            $table->foreign('cita_id')->references('id')->on('citas')->onDelete('cascade');
            $table->foreign('doctor_id')->references('id')->on('doctores')->onDelete('cascade');
            $table->foreign('paciente_id')->references('id')->on('pacientes')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expedientes');
    }
};
