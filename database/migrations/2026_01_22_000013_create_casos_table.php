<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('casos', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('PK, autoincremental');
            $table->unsignedBigInteger('paciente_id')->comment('FK a pacientes');
            $table->unsignedBigInteger('doctor_id')->comment('FK a doctores');
            $table->unsignedBigInteger('sucursal_id')->comment('FK a sucursales');
            $table->text('descripcion')->nullable()->comment('Descripción del caso');
            $table->date('fecha_apertura')->comment('Fecha de apertura');
            $table->string('estado', 30)->comment('Estado del caso');
            $table->timestamps();

            $table->foreign('paciente_id')->references('id')->on('pacientes')->onDelete('cascade');
            $table->foreign('doctor_id')->references('id')->on('doctores')->onDelete('cascade');
            $table->foreign('sucursal_id')->references('id')->on('sucursales')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('casos');
    }
};
