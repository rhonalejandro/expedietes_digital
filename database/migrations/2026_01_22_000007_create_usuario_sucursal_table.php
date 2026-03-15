<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('usuario_sucursal', function (Blueprint $table) {
            $table->unsignedBigInteger('usuario_id')->comment('FK a usuarios');
            $table->unsignedBigInteger('sucursal_id')->comment('FK a sucursales');
            $table->primary(['usuario_id', 'sucursal_id']);
            $table->foreign('usuario_id')->references('id')->on('usuarios')->onDelete('cascade');
            $table->foreign('sucursal_id')->references('id')->on('sucursales')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('usuario_sucursal');
    }
};
