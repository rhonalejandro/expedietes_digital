<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('monedas', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('PK, autoincremental');
            $table->string('codigo', 10)->unique()->comment('Código ISO de la moneda, ej: USD, MXN, EUR');
            $table->string('nombre', 50)->comment('Nombre de la moneda');
            $table->string('simbolo', 10)->comment('Símbolo, ej: $, €, Bs.');
            $table->boolean('por_defecto')->default(false)->comment('Indica si es la moneda por defecto del sistema');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('monedas');
    }
};
