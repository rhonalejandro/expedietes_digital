<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('adjuntos', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('PK, autoincremental');
            $table->unsignedBigInteger('consulta_id')->comment('FK a consultas');
            $table->string('tipo', 50)->comment('Tipo de archivo: imagen, documento, etc.');
            $table->string('ruta', 255)->comment('Ruta del archivo');
            $table->string('descripcion', 255)->nullable()->comment('Descripción del archivo');
            $table->timestamps();

            $table->foreign('consulta_id')->references('id')->on('consultas')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('adjuntos');
    }
};
