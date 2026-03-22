<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        foreach (['log_citas', 'log_clientes', 'log_especialistas'] as $t) {
            Schema::table($t, function (Blueprint $table) {
                $table->unsignedBigInteger('usuario_id')->nullable()->change();
            });
        }
    }

    public function down(): void
    {
        foreach (['log_citas', 'log_clientes', 'log_especialistas'] as $t) {
            Schema::table($t, function (Blueprint $table) {
                $table->unsignedBigInteger('usuario_id')->nullable(false)->change();
            });
        }
    }
};
