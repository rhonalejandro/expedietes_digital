<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 150);
            $table->string('telefono', 50)->nullable();
            $table->string('email', 150)->nullable();
            $table->string('origen', 30)->default('chatwoot'); // chatwoot, web, telefono
            $table->unsignedBigInteger('chatwoot_contact_id')->nullable()->index();
            $table->unsignedBigInteger('chatwoot_conv_id')->nullable();
            $table->text('notas')->nullable();
            $table->string('estatus', 20)->default('nuevo'); // nuevo, en_seguimiento, convertido, descartado
            $table->foreignId('convertido_en')->nullable()->constrained('pacientes')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};
