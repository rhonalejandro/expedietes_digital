<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('consultas', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('PK, autoincremental');
            $table->unsignedBigInteger('caso_id')->comment('FK a casos');
            $table->unsignedBigInteger('doctor_id')->comment('FK a doctores');
            $table->dateTime('fecha_hora')->comment('Fecha y hora de la consulta');
            $table->string('estado', 30)->default('realizada')->comment('Estado de la consulta: realizada, pendiente, cancelada');
            $table->text('diagnostico')->nullable()->comment('Diagnóstico');
            $table->text('observaciones')->nullable()->comment('Observaciones');
            $table->text('tratamiento')->nullable()->comment('Tratamiento');
            $table->text('receta')->nullable()->comment('Receta médica');
            $table->string('firma_digital', 255)->nullable()->comment('Firma digital o hash');
            $table->timestamps();

            $table->foreign('caso_id')->references('id')->on('casos')->onDelete('cascade');
            $table->foreign('doctor_id')->references('id')->on('doctores')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('consultas');
    }
};
