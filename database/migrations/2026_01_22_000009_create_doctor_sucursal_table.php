<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('doctor_sucursal', function (Blueprint $table) {
            $table->unsignedBigInteger('doctor_id')->comment('FK a doctores');
            $table->unsignedBigInteger('sucursal_id')->comment('FK a sucursales');
            $table->primary(['doctor_id', 'sucursal_id']);
            $table->foreign('doctor_id')->references('id')->on('doctores')->onDelete('cascade');
            $table->foreign('sucursal_id')->references('id')->on('sucursales')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('doctor_sucursal');
    }
};
