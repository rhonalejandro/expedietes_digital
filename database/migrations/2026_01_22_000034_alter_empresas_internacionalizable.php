<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('empresas', function (Blueprint $table) {
            $table->dropColumn('ruc');
            $table->string('tipo_identificacion', 50)->after('nombre')->comment('Tipo de identificación (RUC, RIF, Cédula Jurídica, etc.)');
            $table->string('identificacion', 50)->after('tipo_identificacion')->comment('Número de identificación');
            $table->renameColumn('contacto', 'telefono');
            $table->string('pagina_web', 150)->nullable()->after('email')->comment('Página web');
            $table->json('redes_sociales')->nullable()->after('pagina_web')->comment('Redes sociales en formato JSON');
        });
    }

    public function down(): void
    {
        Schema::table('empresas', function (Blueprint $table) {
            $table->dropColumn(['tipo_identificacion', 'identificacion', 'pagina_web', 'redes_sociales']);
            $table->renameColumn('telefono', 'contacto');
            $table->string('ruc', 50)->nullable()->comment('Registro único contribuyente');
        });
    }
};
