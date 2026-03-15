<?php

namespace Database\Seeders;

use App\Models\Paciente;
use App\Models\Persona;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PacienteSeeder extends Seeder
{
    public function run(): void
    {
        $pacientes = [
            ['nombre' => 'María',    'apellido' => 'González Ruiz',    'identificacion' => '8-123-456',  'tipo' => 'Cédula', 'genero' => 'femenino',   'fecha_nac' => '1985-03-22', 'contacto' => '+507 6201-1122', 'email' => 'maria.gonzalez@email.com',   'seguro' => 'AXA Seguros'],
            ['nombre' => 'José',     'apellido' => 'Martínez Pérez',   'identificacion' => '4-567-890',  'tipo' => 'Cédula', 'genero' => 'masculino',  'fecha_nac' => '1972-07-15', 'contacto' => '+507 6345-9900', 'email' => 'jose.martinez@email.com',    'seguro' => 'ASSA Compañía de Seguros'],
            ['nombre' => 'Luisa',    'apellido' => 'Herrera Castro',   'identificacion' => '2-890-123',  'tipo' => 'Cédula', 'genero' => 'femenino',   'fecha_nac' => '1990-11-08', 'contacto' => '+507 6412-3344', 'email' => 'luisa.herrera@email.com',    'seguro' => null],
            ['nombre' => 'Ricardo',  'apellido' => 'Vásquez Torres',   'identificacion' => '6-234-789',  'tipo' => 'Cédula', 'genero' => 'masculino',  'fecha_nac' => '1968-01-30', 'contacto' => '+507 6534-7788', 'email' => null,                         'seguro' => 'Blue Cross Blue Shield'],
            ['nombre' => 'Ana',      'apellido' => 'Rodríguez Lima',   'identificacion' => '9-345-678',  'tipo' => 'Cédula', 'genero' => 'femenino',   'fecha_nac' => '1995-06-14', 'contacto' => '+507 6678-2211', 'email' => 'ana.rodriguez@email.com',    'seguro' => null],
            ['nombre' => 'Pedro',    'apellido' => 'Morales Sánchez',  'identificacion' => '3-456-901',  'tipo' => 'Cédula', 'genero' => 'masculino',  'fecha_nac' => '1960-09-25', 'contacto' => '+507 6723-5566', 'email' => 'pedro.morales@email.com',    'seguro' => 'Seguro Social (CSS)'],
            ['nombre' => 'Carmen',   'apellido' => 'Núñez Palacios',   'identificacion' => '7-678-234',  'tipo' => 'Cédula', 'genero' => 'femenino',   'fecha_nac' => '1978-04-03', 'contacto' => '+507 6812-4433', 'email' => 'carmen.nunez@email.com',     'seguro' => 'Seguro Social (CSS)'],
            ['nombre' => 'Fernando', 'apellido' => 'Jiménez Ramos',    'identificacion' => '5-789-345',  'tipo' => 'Cédula', 'genero' => 'masculino',  'fecha_nac' => '1988-12-19', 'contacto' => '+507 6956-1100', 'email' => 'fernando.jimenez@email.com', 'seguro' => null],
            ['nombre' => 'Patricia', 'apellido' => 'Flores Mendoza',   'identificacion' => '8-901-456',  'tipo' => 'Cédula', 'genero' => 'femenino',   'fecha_nac' => '2000-02-28', 'contacto' => '+507 6034-8877', 'email' => 'patricia.flores@email.com',  'seguro' => 'AXA Seguros'],
            ['nombre' => 'Miguel',   'apellido' => 'Castillo Vargas',  'identificacion' => 'E-456-789',  'tipo' => 'Cédula', 'genero' => 'masculino',  'fecha_nac' => '1975-08-11', 'contacto' => '+507 6187-6655', 'email' => null,                         'seguro' => null],
            ['nombre' => 'Diana',    'apellido' => 'Torres Espinoza',  'identificacion' => '4-123-007',  'tipo' => 'Cédula', 'genero' => 'femenino',   'fecha_nac' => '1993-05-17', 'contacto' => '+507 6290-3322', 'email' => 'diana.torres@email.com',     'seguro' => 'ASSA Compañía de Seguros'],
            ['nombre' => 'Roberto',  'apellido' => 'Gutiérrez Mora',   'identificacion' => '6-789-012',  'tipo' => 'Cédula', 'genero' => 'masculino',  'fecha_nac' => '1955-10-04', 'contacto' => '+507 6401-7744', 'email' => 'roberto.gutierrez@email.com','seguro' => 'Seguro Social (CSS)'],
            ['nombre' => 'Isabel',   'apellido' => 'Reyes Domínguez',  'identificacion' => 'N-98765432', 'tipo' => 'Pasaporte', 'genero' => 'femenino', 'fecha_nac' => '1982-07-22', 'contacto' => '+507 6543-8899', 'email' => 'isabel.reyes@email.com',    'seguro' => 'Blue Cross Blue Shield'],
            ['nombre' => 'Arturo',   'apellido' => 'Lara Bustamante',  'identificacion' => '3-234-890',  'tipo' => 'Cédula', 'genero' => 'masculino',  'fecha_nac' => '1970-03-09', 'contacto' => '+507 6654-2200', 'email' => null,                         'seguro' => null],
            ['nombre' => 'Mónica',   'apellido' => 'Aguilar Pinto',    'identificacion' => '2-345-901',  'tipo' => 'Cédula', 'genero' => 'femenino',   'fecha_nac' => '1998-09-30', 'contacto' => '+507 6765-3311', 'email' => 'monica.aguilar@email.com',   'seguro' => null],
            ['nombre' => 'Héctor',   'apellido' => 'Sandoval Ureña',   'identificacion' => '9-456-012',  'tipo' => 'Cédula', 'genero' => 'masculino',  'fecha_nac' => '1965-01-15', 'contacto' => '+507 6876-4422', 'email' => 'hector.sandoval@email.com',  'seguro' => 'Seguro Social (CSS)'],
            ['nombre' => 'Valeria',  'apellido' => 'Serrano Obaldia',  'identificacion' => '7-567-123',  'tipo' => 'Cédula', 'genero' => 'femenino',   'fecha_nac' => '2002-04-20', 'contacto' => '+507 6987-5533', 'email' => 'valeria.serrano@email.com',  'seguro' => null],
            ['nombre' => 'Ernesto',  'apellido' => 'Fuentes Delgado',  'identificacion' => '5-678-234',  'tipo' => 'Cédula', 'genero' => 'masculino',  'fecha_nac' => '1980-11-27', 'contacto' => '+507 6098-6644', 'email' => null,                         'seguro' => 'AXA Seguros'],
            ['nombre' => 'Gabriela', 'apellido' => 'Medina Quirós',    'identificacion' => '8-789-345',  'tipo' => 'Cédula', 'genero' => 'femenino',   'fecha_nac' => '1987-06-05', 'contacto' => '+507 6109-7755', 'email' => 'gabriela.medina@email.com',  'seguro' => 'ASSA Compañía de Seguros'],
            ['nombre' => 'Luis',     'apellido' => 'Campos Araúz',     'identificacion' => '3-890-456',  'tipo' => 'Cédula', 'genero' => 'masculino',  'fecha_nac' => '1991-08-18', 'contacto' => '+507 6210-8866', 'email' => 'luis.campos@email.com',      'seguro' => null],
            // ── 20 pacientes adicionales ──────────────────────────────────────────
            ['nombre' => 'Natalia',  'apellido' => 'Ponce Villarreal',  'identificacion' => '4-901-567',  'tipo' => 'Cédula', 'genero' => 'femenino',   'fecha_nac' => '1994-03-11', 'contacto' => '+507 6321-4455', 'email' => 'natalia.ponce@email.com',    'seguro' => 'AXA Seguros'],
            ['nombre' => 'Oswaldo',  'apellido' => 'Barría Pittí',      'identificacion' => '7-012-678',  'tipo' => 'Cédula', 'genero' => 'masculino',  'fecha_nac' => '1969-07-28', 'contacto' => '+507 6432-5566', 'email' => null,                         'seguro' => 'Seguro Social (CSS)'],
            ['nombre' => 'Cristina', 'apellido' => 'Araúz Montenegro',  'identificacion' => '5-123-789',  'tipo' => 'Cédula', 'genero' => 'femenino',   'fecha_nac' => '1985-12-04', 'contacto' => '+507 6543-6677', 'email' => 'cristina.arauz@email.com',   'seguro' => 'ASSA Compañía de Seguros'],
            ['nombre' => 'Javier',   'apellido' => 'Ríos Samaniego',    'identificacion' => '9-234-890',  'tipo' => 'Cédula', 'genero' => 'masculino',  'fecha_nac' => '1977-05-19', 'contacto' => '+507 6654-7788', 'email' => 'javier.rios@email.com',      'seguro' => null],
            ['nombre' => 'Lorena',   'apellido' => 'Villalobos Chen',   'identificacion' => '6-345-901',  'tipo' => 'Cédula', 'genero' => 'femenino',   'fecha_nac' => '2001-09-08', 'contacto' => '+507 6765-8899', 'email' => 'lorena.villalobos@email.com','seguro' => 'Blue Cross Blue Shield'],
            ['nombre' => 'Óscar',    'apellido' => 'Muñoz Jaramillo',   'identificacion' => '3-456-012',  'tipo' => 'Cédula', 'genero' => 'masculino',  'fecha_nac' => '1962-02-14', 'contacto' => '+507 6876-9900', 'email' => null,                         'seguro' => 'Seguro Social (CSS)'],
            ['nombre' => 'Verónica', 'apellido' => 'Soto Ballesteros',  'identificacion' => '8-567-123',  'tipo' => 'Cédula', 'genero' => 'femenino',   'fecha_nac' => '1989-06-25', 'contacto' => '+507 6987-1122', 'email' => 'veronica.soto@email.com',    'seguro' => null],
            ['nombre' => 'Rubén',    'apellido' => 'Trejos Espino',     'identificacion' => '4-678-234',  'tipo' => 'Cédula', 'genero' => 'masculino',  'fecha_nac' => '1974-11-30', 'contacto' => '+507 6098-2233', 'email' => 'ruben.trejos@email.com',     'seguro' => 'AXA Seguros'],
            ['nombre' => 'Susana',   'apellido' => 'Chávez Londoño',    'identificacion' => '2-789-345',  'tipo' => 'Cédula', 'genero' => 'femenino',   'fecha_nac' => '1996-04-17', 'contacto' => '+507 6109-3344', 'email' => 'susana.chavez@email.com',    'seguro' => null],
            ['nombre' => 'Felipe',   'apellido' => 'Coronado Núñez',    'identificacion' => 'E-789-012',  'tipo' => 'Cédula', 'genero' => 'masculino',  'fecha_nac' => '1983-08-22', 'contacto' => '+507 6210-4455', 'email' => 'felipe.coronado@email.com',  'seguro' => 'ASSA Compañía de Seguros'],
            ['nombre' => 'Alejandra','apellido' => 'Mora Lezcano',      'identificacion' => '7-890-456',  'tipo' => 'Cédula', 'genero' => 'femenino',   'fecha_nac' => '1992-01-03', 'contacto' => '+507 6321-5566', 'email' => 'alejandra.mora@email.com',   'seguro' => null],
            ['nombre' => 'Tomás',    'apellido' => 'Guerrero Aparicio',  'identificacion' => '5-901-567',  'tipo' => 'Cédula', 'genero' => 'masculino',  'fecha_nac' => '1958-10-16', 'contacto' => '+507 6432-6677', 'email' => null,                         'seguro' => 'Seguro Social (CSS)'],
            ['nombre' => 'Paola',    'apellido' => 'Herrera Juárez',    'identificacion' => '9-012-678',  'tipo' => 'Cédula', 'genero' => 'femenino',   'fecha_nac' => '1999-07-09', 'contacto' => '+507 6543-7788', 'email' => 'paola.herrera@email.com',    'seguro' => 'Blue Cross Blue Shield'],
            ['nombre' => 'Andrés',   'apellido' => 'Valderrama Quirós', 'identificacion' => '6-123-789',  'tipo' => 'Cédula', 'genero' => 'masculino',  'fecha_nac' => '1986-03-27', 'contacto' => '+507 6654-8899', 'email' => 'andres.valderrama@email.com','seguro' => null],
            ['nombre' => 'Melissa',  'apellido' => 'Ibarra Zamora',     'identificacion' => '3-234-890',  'tipo' => 'Cédula', 'genero' => 'femenino',   'fecha_nac' => '2003-11-14', 'contacto' => '+507 6765-9900', 'email' => 'melissa.ibarra@email.com',   'seguro' => null],
            ['nombre' => 'Rodrigo',  'apellido' => 'Palma Cisneros',    'identificacion' => '8-345-901',  'tipo' => 'Cédula', 'genero' => 'masculino',  'fecha_nac' => '1971-06-05', 'contacto' => '+507 6876-1122', 'email' => 'rodrigo.palma@email.com',    'seguro' => 'AXA Seguros'],
            ['nombre' => 'Daniela',  'apellido' => 'Espino Brathwaite', 'identificacion' => '4-456-012',  'tipo' => 'Cédula', 'genero' => 'femenino',   'fecha_nac' => '1997-09-21', 'contacto' => '+507 6987-2233', 'email' => 'daniela.espino@email.com',   'seguro' => 'ASSA Compañía de Seguros'],
            ['nombre' => 'Mauricio', 'apellido' => 'Ávila Santamaría',  'identificacion' => '2-567-123',  'tipo' => 'Cédula', 'genero' => 'masculino',  'fecha_nac' => '1967-02-18', 'contacto' => '+507 6098-3344', 'email' => null,                         'seguro' => 'Seguro Social (CSS)'],
            ['nombre' => 'Xiomara',  'apellido' => 'De Gracia Pitti',   'identificacion' => '7-678-345',  'tipo' => 'Cédula', 'genero' => 'femenino',   'fecha_nac' => '1993-05-30', 'contacto' => '+507 6109-4455', 'email' => 'xiomara.degracia@email.com', 'seguro' => null],
            ['nombre' => 'Iván',     'apellido' => 'Quintero Batista',  'identificacion' => 'P-AB123456', 'tipo' => 'Pasaporte', 'genero' => 'masculino','fecha_nac' => '1981-12-07', 'contacto' => '+507 6210-5566', 'email' => 'ivan.quintero@email.com',    'seguro' => 'Blue Cross Blue Shield'],
        ];

        foreach ($pacientes as $data) {
            DB::transaction(function () use ($data) {
                if (Persona::where('identificacion', $data['identificacion'])->exists()) {
                    return;
                }

                $persona = Persona::create([
                    'nombre'              => $data['nombre'],
                    'apellido'            => $data['apellido'],
                    'tipo_identificacion' => $data['tipo'],
                    'identificacion'      => $data['identificacion'],
                    'fecha_nacimiento'    => $data['fecha_nac'],
                    'genero'              => $data['genero'],
                    'contacto'            => $data['contacto'],
                    'email'               => $data['email'],
                    'seguro_medico'       => $data['seguro'],
                    'nacionalidad'        => 'Panameña',
                    'estado'              => true,
                ]);

                Paciente::create([
                    'persona_id' => $persona->id,
                    'estado'     => true,
                ]);
            });
        }
    }
}
