<?php

namespace Database\Factories;

use App\Models\Persona;
use Illuminate\Database\Eloquent\Factories\Factory;

class PersonaFactory extends Factory
{
    protected $model = Persona::class;

    public function definition(): array
    {
        return [
            'nombre' => $this->faker->firstName,
            'apellido' => $this->faker->lastName,
            'tipo_identificacion' => 'DNI',
            'identificacion' => $this->faker->unique()->numerify('########'),
            'fecha_nacimiento' => $this->faker->date(),
            'contacto' => $this->faker->phoneNumber,
            'direccion' => $this->faker->address,
            'email' => $this->faker->unique()->safeEmail,
            'genero' => $this->faker->randomElement(['masculino', 'femenino', 'otro']),
            'estado' => true,
        ];
    }
}
