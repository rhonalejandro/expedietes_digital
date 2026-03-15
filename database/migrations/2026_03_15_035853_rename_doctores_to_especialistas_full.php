<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Renombrar tabla principal
        Schema::rename('doctores', 'especialistas');

        // 2. Agregar campos profesionales nuevos
        Schema::table('especialistas', function (Blueprint $table) {
            $table->string('tratamiento', 20)->nullable()->after('persona_id')
                  ->comment('Dr., Dra., Lic., Lcda., Téc., Mtro., etc.');
            $table->string('profesion', 100)->nullable()->after('tratamiento')
                  ->comment('Podólogo, Fisioterapeuta, etc.');
            $table->string('num_colegiado', 50)->nullable()->after('profesion')
                  ->comment('Número de colegiado o N/A');
            $table->string('telefono', 50)->nullable()->after('num_colegiado');
            $table->string('email', 150)->nullable()->after('telefono');
            $table->string('firma', 255)->nullable()->after('email')
                  ->comment('Ruta imagen PNG de la firma');
        });

        // 3. Renombrar pivote doctor_sucursal → especialista_sucursal
        Schema::rename('doctor_sucursal', 'especialista_sucursal');
        DB::statement('ALTER TABLE especialista_sucursal RENAME COLUMN doctor_id TO especialista_id');
        DB::statement('ALTER TABLE especialista_sucursal DROP CONSTRAINT IF EXISTS doctor_sucursal_doctor_id_foreign');
        DB::statement('ALTER TABLE especialista_sucursal ADD CONSTRAINT especialista_sucursal_especialista_id_foreign
            FOREIGN KEY (especialista_id) REFERENCES especialistas(id) ON DELETE CASCADE');

        // 4. Renombrar horarios_doctores → horarios_especialistas
        Schema::rename('horarios_doctores', 'horarios_especialistas');
        DB::statement('ALTER TABLE horarios_especialistas RENAME COLUMN doctor_id TO especialista_id');
        DB::statement('ALTER TABLE horarios_especialistas DROP CONSTRAINT IF EXISTS horarios_doctores_doctor_id_foreign');
        DB::statement('ALTER TABLE horarios_especialistas ADD CONSTRAINT horarios_especialistas_especialista_id_foreign
            FOREIGN KEY (especialista_id) REFERENCES especialistas(id) ON DELETE CASCADE');

        // 5. Renombrar doctor_id en tablas relacionadas
        foreach (['citas', 'casos', 'expedientes', 'consultas'] as $tabla) {
            DB::statement("ALTER TABLE {$tabla} RENAME COLUMN doctor_id TO especialista_id");
            DB::statement("ALTER TABLE {$tabla} DROP CONSTRAINT IF EXISTS {$tabla}_doctor_id_foreign");
            DB::statement("ALTER TABLE {$tabla} ADD CONSTRAINT {$tabla}_especialista_id_foreign
                FOREIGN KEY (especialista_id) REFERENCES especialistas(id) ON DELETE SET NULL");
        }
    }

    public function down(): void
    {
        foreach (['citas', 'casos', 'expedientes', 'consultas'] as $tabla) {
            DB::statement("ALTER TABLE {$tabla} DROP CONSTRAINT IF EXISTS {$tabla}_especialista_id_foreign");
            DB::statement("ALTER TABLE {$tabla} RENAME COLUMN especialista_id TO doctor_id");
        }

        DB::statement('ALTER TABLE horarios_especialistas DROP CONSTRAINT IF EXISTS horarios_especialistas_especialista_id_foreign');
        DB::statement('ALTER TABLE horarios_especialistas RENAME COLUMN especialista_id TO doctor_id');
        Schema::rename('horarios_especialistas', 'horarios_doctores');

        DB::statement('ALTER TABLE especialista_sucursal DROP CONSTRAINT IF EXISTS especialista_sucursal_especialista_id_foreign');
        DB::statement('ALTER TABLE especialista_sucursal RENAME COLUMN especialista_id TO doctor_id');
        Schema::rename('especialista_sucursal', 'doctor_sucursal');

        Schema::table('especialistas', function (Blueprint $table) {
            $table->dropColumn(['tratamiento', 'profesion', 'num_colegiado', 'telefono', 'email', 'firma']);
        });

        Schema::rename('especialistas', 'doctores');
    }
};
