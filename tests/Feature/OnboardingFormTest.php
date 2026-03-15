<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Empresa;
use App\Models\Usuario;
use App\Models\Rol;

class OnboardingFormTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function puede_completar_onboarding_con_todos_los_datos_empresa_y_superusuario()
    {
        $postData = [
            'empresa_nombre' => 'GlobalFeet',
            'empresa_tipo_identificacion' => 'RUC',
            'empresa_identificacion' => '1234567890',
            'empresa_direccion' => 'Av. Principal 123',
            'empresa_telefono' => '555-1234',
            'empresa_email' => 'empresa@globalfeet.com',
            'empresa_pagina_web' => 'https://globalfeet.com',
            'empresa_redes_sociales' => '["https://facebook.com/globalfeet","https://twitter.com/globalfeet"]',
            'usuario_nombre' => 'Super Admin',
            'usuario_email' => 'admin@globalfeet.com',
            'usuario_password' => 'password123',
            'usuario_password_confirmation' => 'password123',
        ];

        $response = $this->post(route('onboarding.store'), $postData);
        $response->assertRedirect(route('dashboard'));
        $this->assertDatabaseHas('empresas', [
            'nombre' => 'GlobalFeet',
            'tipo_identificacion' => 'RUC',
            'identificacion' => '1234567890',
            'direccion' => 'Av. Principal 123',
            'telefono' => '555-1234',
            'email' => 'empresa@globalfeet.com',
            'pagina_web' => 'https://globalfeet.com',
        ]);
        $this->assertDatabaseHas('usuarios', [
            'nombre' => 'Super Admin',
            'email' => 'admin@globalfeet.com',
        ]);
    }
}
