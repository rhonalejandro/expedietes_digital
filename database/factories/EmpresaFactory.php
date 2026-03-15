<?php

namespace Database\Factories;

use App\Models\Empresa;
use Illuminate\Database\Eloquent\Factories\Factory;

class EmpresaFactory extends Factory
{
    protected $model = Empresa::class;

    public function definition(): array
    {
        return [
            'nombre' => $this->faker->company,
            'tipo_identificacion' => 'RFC',
            'identificacion' => $this->faker->unique()->numerify('##########'),
            'telefono' => $this->faker->phoneNumber,
            'email' => $this->faker->unique()->companyEmail,
            'pagina_web' => $this->faker->url,
            'redes_sociales' => null,
            'direccion' => $this->faker->address,
            'estado' => true,
        ];
    }
}
