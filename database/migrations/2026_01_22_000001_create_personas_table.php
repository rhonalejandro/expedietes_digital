<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('personas', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('PK, autoincremental');
            $table->string('nombre', 100)->comment('Nombre de la persona');
            $table->string('apellido', 100)->comment('Apellido de la persona');
            $table->string('tipo_identificacion', 50)->comment('Tipo de documento (cédula, pasaporte, etc)');
            $table->string('identificacion', 50)->comment('Número de identificación');
            $table->date('fecha_nacimiento')->nullable()->comment('Fecha de nacimiento');
            $table->string('contacto', 100)->nullable()->comment('Teléfono o celular');
            $table->string('direccion', 255)->nullable()->comment('Dirección física');
            $table->string('email', 150)->nullable()->comment('Correo electrónico');
            $table->string('genero', 20)->nullable()->comment('Género');
            $table->boolean('estado')->default(true)->comment('Activo/Inactivo');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('personas');
    }
};
