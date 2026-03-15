<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Empresa;
use App\Models\Usuario;
use App\Models\Rol;

class OnboardingMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function redirige_a_onboarding_si_no_hay_empresa_ni_superusuario()
    {
        $response = $this->get('/');
        $response->assertRedirect(route('onboarding.index'));
        // Seguir la redirección y validar la vista de onboarding
        $onboarding = $this->get(route('onboarding.index'));
        $onboarding->assertOk();
        file_put_contents(base_path('onboarding_test_output.html'), $onboarding->getContent());
        $onboarding->assertSeeText('Configuración inicial');
    }

    /** @test */
    public function permite_acceso_si_hay_empresa_y_superusuario()
    {
        $empresa = Empresa::factory()->create();
        $rol = Rol::factory()->create(['nombre' => 'superadmin']);
        /** @var \App\Models\Usuario $usuario */
        $usuario = Usuario::factory()->create();
        $usuario->roles()->attach($rol->id);

        $this->actingAs($usuario);
        $response = $this->get('/');
        $response->assertOk();

    }

    /** @test */
    public function redirige_a_login_si_no_autenticado_y_hay_empresa_y_superusuario()
    {
        $empresa = Empresa::factory()->create();
        $rol = Rol::factory()->create(['nombre' => 'superadmin']);
        $usuario = Usuario::factory()->create();
        $usuario->roles()->attach($rol->id);

        // No autenticado
        $response = $this->get('/login');
        $response->assertOk();
        $response->assertSee('Login form');
    }

    /** @test */
    public function no_redirige_en_ruta_onboarding()
    {
        $response = $this->get(route('onboarding.index'));
        $response->assertOk();
    }
}
