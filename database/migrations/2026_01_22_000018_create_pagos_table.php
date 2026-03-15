<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('pagos', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('PK, autoincremental');
            $table->unsignedBigInteger('cita_id')->comment('FK a citas');
            $table->unsignedBigInteger('paciente_id')->comment('FK a pacientes');
            $table->decimal('monto', 10, 2)->comment('Monto pagado');
            $table->string('metodo_pago', 50)->comment('Método de pago');
            $table->date('fecha')->comment('Fecha del pago');
            $table->string('estado', 30)->comment('Estado del pago');
            $table->timestamps();

            $table->foreign('cita_id')->references('id')->on('citas')->onDelete('cascade');
            $table->foreign('paciente_id')->references('id')->on('pacientes')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pagos');
    }
};
