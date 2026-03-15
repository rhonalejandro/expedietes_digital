<?php

namespace Database\Seeders;

use App\Models\Servicio;
use Illuminate\Database\Seeder;

class ServicioSeeder extends Seeder
{
    public function run(): void
    {
        $servicios = [
            // Consultas
            ['nombre' => 'Consulta Podológica General',       'descripcion' => 'Evaluación general del pie y diagnóstico inicial.',                        'precio' => 40.00],
            ['nombre' => 'Consulta de Seguimiento',           'descripcion' => 'Revisión y control de evolución del tratamiento.',                          'precio' => 25.00],

            // Tratamientos de uñas
            ['nombre' => 'Tratamiento de Uña Encarnada',      'descripcion' => 'Corrección y tratamiento de onicocriptosis (uña encarnada).',               'precio' => 50.00],
            ['nombre' => 'Tratamiento de Onicomicosis',       'descripcion' => 'Tratamiento de hongos en uñas del pie.',                                    'precio' => 45.00],
            ['nombre' => 'Quiropodia (Podología Preventiva)', 'descripcion' => 'Limpieza, corte y cuidado integral de uñas y piel del pie.',                'precio' => 35.00],
            ['nombre' => 'Ortoniquia (Corrección de Uña)',    'descripcion' => 'Colocación de ortesis para corrección de curvatura de uñas.',                'precio' => 60.00],

            // Piel y callosidades
            ['nombre' => 'Eliminación de Callosidades',       'descripcion' => 'Eliminación de callosidades y durezas plantares.',                          'precio' => 30.00],
            ['nombre' => 'Tratamiento de Helomas (Ojos de Gallo)', 'descripcion' => 'Eliminación y tratamiento de helomas plantares e interdigitales.',      'precio' => 30.00],
            ['nombre' => 'Tratamiento de Verruga Plantar',    'descripcion' => 'Tratamiento de papilomas o verrugas en la planta del pie.',                  'precio' => 55.00],
            ['nombre' => 'Hidratación y Peeling Podal',       'descripcion' => 'Tratamiento hidratante e exfoliación profunda del pie.',                    'precio' => 40.00],

            // Biomecánica y ortopedia
            ['nombre' => 'Estudio Biomecánico del Pie',       'descripcion' => 'Análisis de la pisada, postura y distribución de presiones.',               'precio' => 80.00],
            ['nombre' => 'Plantillas Ortopédicas a Medida',   'descripcion' => 'Elaboración de plantillas personalizadas según estudio biomecánico.',        'precio' => 120.00],
            ['nombre' => 'Vendaje Funcional / Tape',          'descripcion' => 'Aplicación de vendaje funcional para soporte y corrección postural.',        'precio' => 25.00],

            // Pie diabético
            ['nombre' => 'Atención Pie Diabético',            'descripcion' => 'Cuidado especializado del pie en pacientes con diabetes.',                  'precio' => 55.00],
            ['nombre' => 'Evaluación Vascular y Neurológica', 'descripcion' => 'Exploración de la circulación y sensibilidad del pie.',                     'precio' => 45.00],

            // Procedimientos menores
            ['nombre' => 'Infiltración Local',                'descripcion' => 'Aplicación de medicamento local para alivio de dolor o inflamación.',        'precio' => 50.00],
            ['nombre' => 'Extirpación de Fibroma Plantar',    'descripcion' => 'Procedimiento quirúrgico menor para extirpación de fibromas.',               'precio' => 150.00],
        ];

        foreach ($servicios as $s) {
            Servicio::firstOrCreate(
                ['nombre' => $s['nombre']],
                array_merge($s, ['estado' => true])
            );
        }
    }
}
