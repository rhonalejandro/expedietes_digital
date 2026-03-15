<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('servicios', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('PK, autoincremental');
            $table->string('nombre', 100)->comment('Nombre del servicio');
            $table->string('descripcion', 255)->nullable()->comment('Descripción');
            $table->decimal('precio', 10, 2)->comment('Precio del servicio');
            $table->boolean('estado')->default(true)->comment('Activo/Inactivo');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('servicios');
    }
};
