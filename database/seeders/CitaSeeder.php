<?php

namespace Database\Seeders;

use App\Models\Cita;
use App\Models\Especialista;
use App\Models\Paciente;
use App\Models\Servicio;
use App\Models\Sucursal;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CitaSeeder extends Seeder
{
    // Pesos de estatus: más confirmadas, menos canceladas
    private array $estatusPool = [];

    public function run(): void
    {
        $especialistas = Especialista::where('estado', true)->pluck('id')->toArray();
        $pacientes     = Paciente::where('estado', true)->pluck('id')->toArray();
        $servicios     = Servicio::where('estado', true)->pluck('id')->toArray();
        $sucursales    = Sucursal::where('estado', true)->pluck('id')->toArray();

        if (empty($especialistas) || empty($pacientes) || empty($sucursales)) {
            $this->command->warn('Faltan datos base. Abortando CitaSeeder.');
            return;
        }

        // Truncar tabla limpiamente
        DB::statement('TRUNCATE TABLE citas RESTART IDENTITY CASCADE');
        $this->command->info('Tabla citas truncada.');

        // Pool de estatus con distribución realista
        $this->estatusPool = array_merge(
            array_fill(0, 45, 'confirmada'),
            array_fill(0, 35, 'pendiente'),
            array_fill(0, 20, 'cancelada')
        );

        $origenes = ['web', 'web', 'telefono', 'telefono', 'chatwoot', 'mobile'];

        $motivos = [
            'Revisión de callosidades',
            'Dolor en el talón',
            'Uña encarnada',
            'Control pie diabético',
            'Evaluación de plantillas',
            'Hongos en las uñas',
            'Fisura plantar',
            'Seguimiento postoperatorio',
            'Consulta primera vez',
            'Dolor en el arco plantar',
            'Verruga plantar',
            'Fascitis plantar',
            null, null, null,
        ];

        $inicio = Carbon::create(2026, 3, 1);
        $fin    = Carbon::create(2026, 3, 31);
        $total  = 0;

        $fecha = $inicio->copy();
        while ($fecha->lte($fin)) {

            if ($fecha->dayOfWeek === Carbon::SUNDAY) {
                $fecha->addDay();
                continue;
            }

            $esSabado = ($fecha->dayOfWeek === Carbon::SATURDAY);

            // Horario: lunes-viernes 9-17, sábados 9-13
            $horaMin = 9 * 60;          // 540 min
            $horaMax = $esSabado ? 13 * 60 : 17 * 60; // 780 / 1020 min

            foreach ($especialistas as $espId) {

                /*
                 * Algoritmo de cursor secuencial:
                 * - El cursor arranca a las 9:00
                 * - Aleatoriamente crea una cita de 30 o 60 min
                 * - Con 75% de probabilidad la agrega (el 25% restante = hueco)
                 * - Luego avanza el cursor al final de esa franja ± gap (0 o 30 min)
                 * Esto garantiza CERO solapamientos.
                 */
                $cursor = $horaMin;

                while ($cursor < $horaMax) {
                    // Duración: 66% → 30 min, 34% → 60 min
                    $duracion = (rand(1, 3) === 1) ? 60 : 30;
                    $fin_slot = $cursor + $duracion;

                    if ($fin_slot > $horaMax) break;

                    // 75 % de probabilidad de crear la cita (deja huecos naturales)
                    if (rand(1, 4) <= 3) {
                        $hIni = sprintf('%02d:%02d', intdiv($cursor,   60), $cursor   % 60);
                        $hFin = sprintf('%02d:%02d', intdiv($fin_slot, 60), $fin_slot % 60);

                        Cita::create([
                            'especialista_id' => $espId,
                            'paciente_id'     => $pacientes[array_rand($pacientes)],
                            'sucursal_id'     => $sucursales[array_rand($sucursales)],
                            'servicio_id'     => $servicios[array_rand($servicios)],
                            'fecha'           => $fecha->toDateString(),
                            'hora_inicio'     => $hIni,
                            'hora_fin'        => $hFin,
                            'estatus'         => $this->estatusPool[array_rand($this->estatusPool)],
                            'origen'          => $origenes[array_rand($origenes)],
                            'motivo'          => $motivos[array_rand($motivos)],
                            'observaciones'   => null,
                        ]);

                        $total++;
                    }

                    // Avanzar cursor: fin del slot + gap opcional (0 o 30 min)
                    $gaps   = [0, 0, 0, 30]; // 75% sin hueco, 25% hueco de 30 min
                    $cursor = $fin_slot + $gaps[array_rand($gaps)];
                }
            }

            $fecha->addDay();
        }

        $this->command->info("CitaSeeder finalizado. {$total} citas creadas sin solapamiento.");
    }
}
