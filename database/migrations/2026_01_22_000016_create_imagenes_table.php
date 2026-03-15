<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('imagenes', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('PK, autoincremental');
            $table->unsignedBigInteger('historial_id')->comment('FK a historial_medico');
            $table->string('ruta', 255)->comment('Ruta del archivo');
            $table->string('descripcion', 255)->nullable()->comment('Descripción de la imagen');
            $table->timestamps();

            $table->foreign('historial_id')->references('id')->on('historial_medico')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('imagenes');
    }
};
