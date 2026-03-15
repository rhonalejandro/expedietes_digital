<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('usuarios', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('PK, autoincremental');
            $table->unsignedBigInteger('persona_id')->comment('FK a personas');
            $table->string('nombre', 100)->comment('Nombre del usuario');
            $table->string('email', 150)->unique()->comment('Correo de acceso');
            $table->string('password', 255)->comment('Contraseña (hash)');
            $table->rememberToken()->nullable()->comment('Token para recordar sesión');
            $table->boolean('estado')->default(true)->comment('Activo/Inactivo');
            $table->timestamps();

            $table->foreign('persona_id')->references('id')->on('personas')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('usuarios');
    }
};
