<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // Hacer paciente_id nullable (puede ser lead sin ficha)
        DB::statement('ALTER TABLE citas ALTER COLUMN paciente_id DROP NOT NULL');

        Schema::table('citas', function (Blueprint $table) {
            // Campos para leads (sin ficha de paciente aún)
            $table->string('nombre_lead', 150)->nullable()->after('paciente_id')
                  ->comment('Nombre provisional si el paciente no tiene ficha');
            $table->string('telefono_lead', 50)->nullable()->after('nombre_lead')
                  ->comment('Teléfono provisional del lead');

            // Vínculo opcional con caso
            $table->unsignedBigInteger('caso_id')->nullable()->after('telefono_lead')
                  ->comment('FK a casos — se vincula después de la consulta');

            // Vínculo opcional con servicio
            $table->unsignedBigInteger('servicio_id')->nullable()->after('caso_id')
                  ->comment('FK a servicios — servicio a prestar');

            // Motivo de consulta
            $table->text('motivo')->nullable()->after('observaciones')
                  ->comment('Motivo de la cita');

            // Origen de la cita
            $table->string('origen', 30)->default('web')->after('motivo')
                  ->comment('Origen: web, chatwoot, mobile, telefono');

            // Estado renombrado a "estado" (consistente con otros módulos)
            // estatus se mantiene y renombramos con un alias en el modelo

            $table->foreign('caso_id')->references('id')->on('casos')->onDelete('set null');
            $table->foreign('servicio_id')->references('id')->on('servicios')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('citas', function (Blueprint $table) {
            $table->dropForeign(['caso_id']);
            $table->dropForeign(['servicio_id']);
            $table->dropColumn(['nombre_lead', 'telefono_lead', 'caso_id', 'servicio_id', 'motivo', 'origen']);
        });

        DB::statement('ALTER TABLE citas ALTER COLUMN paciente_id SET NOT NULL');
    }
};
