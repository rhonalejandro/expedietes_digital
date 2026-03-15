<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('historial_medico', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('PK, autoincremental');
            $table->unsignedBigInteger('expediente_id')->comment('FK a expedientes');
            $table->date('fecha')->comment('Fecha del registro');
            $table->text('evolucion')->nullable()->comment('Evolución');
            $table->text('observaciones')->nullable()->comment('Observaciones');
            $table->timestamps();

            $table->foreign('expediente_id')->references('id')->on('expedientes')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('historial_medico');
    }
};
