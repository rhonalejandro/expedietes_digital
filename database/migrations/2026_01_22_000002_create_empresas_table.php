<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('empresas', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('PK, autoincremental');
            $table->string('nombre', 150)->comment('Nombre de la empresa');
            $table->string('ruc', 50)->nullable()->comment('Registro único contribuyente');
            $table->string('direccion', 255)->nullable()->comment('Dirección');
            $table->string('contacto', 100)->nullable()->comment('Teléfono');
            $table->string('email', 150)->nullable()->comment('Correo electrónico');
            $table->boolean('estado')->default(true)->comment('Activo/Inactivo');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('empresas');
    }
};
