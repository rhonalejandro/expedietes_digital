<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('notificaciones', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('PK, autoincremental');
            $table->unsignedBigInteger('usuario_id')->comment('FK a usuarios');
            $table->text('mensaje')->comment('Mensaje de la notificación');
            $table->boolean('leido')->default(false)->comment('Leído o no');
            $table->timestamp('fecha')->useCurrent()->comment('Fecha de la notificación');

            $table->foreign('usuario_id')->references('id')->on('usuarios')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notificaciones');
    }
};
