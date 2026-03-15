<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('sucursales', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('PK, autoincremental');
            // $table->unsignedBigInteger('empresa_id')->comment('FK a empresas');
            $table->string('nombre', 150)->comment('Nombre de la sucursal');
            $table->string('direccion', 255)->nullable()->comment('Dirección');
            $table->string('telefono', 100)->nullable()->comment('Teléfono de contacto');
            $table->string('email', 150)->nullable()->comment('Correo electrónico');
            $table->string('encargado', 100)->nullable()->comment('Nombre del encargado');
            $table->boolean('estado')->default(true)->comment('Activo/Inactivo');
            $table->timestamps();

            // $table->foreign('empresa_id')->references('id')->on('empresas')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sucursales');
    }
};
