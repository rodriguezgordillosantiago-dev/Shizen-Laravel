<?php

namespace Tests\Feature;

use Tests\TestCase;

class CourierRoutesTest extends TestCase
{
    public function test_courier_pages_require_a_courier_session(): void
    {
        $this->get('/inicio')->assertRedirect('/');
        $this->get('/pages/activos.html')->assertRedirect('/');
    }

    public function test_login_screen_links_to_laravel_courier_registration(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee(rtrim(config('services.shizen.home_url'), '/') . '/registro-repartidor', false)
            ->assertDontSee('/shizenhome/#registro');
    }

    public function test_legacy_page_urls_render_through_laravel_after_login(): void
    {
        $this->withSession([
            'usuario_rol' => 'repartidor',
            'repartidor' => [
                'id_usuario' => 11,
                'id_repartidor' => 22,
                'nombre' => 'Ana',
                'apellido' => 'Prueba',
            ],
        ])->get('/pages/inicio.html')->assertOk();

        $this->get('/pages/pedidos.html')->assertOk();
        $this->get('/pages/activos.html')->assertOk();
        $this->get('/pages/historial.html')->assertOk();
        $this->get('/pages/configuracion.html')->assertOk();
    }

    public function test_json_endpoints_reject_an_unauthenticated_session(): void
    {
        $this->get('/php/entregas.php?type=active', ['Accept' => 'application/json'])
            ->assertUnauthorized()
            ->assertJson(['error' => 'Debes iniciar sesión como repartidor.']);
    }

    public function test_logout_invalidates_the_courier_session(): void
    {
        $this->withSession([
            'usuario_rol' => 'repartidor',
            'repartidor' => ['id_usuario' => 11, 'id_repartidor' => 22],
        ])->post('/logout')->assertRedirect('/');

        $this->get('/inicio')->assertRedirect('/');
    }
}
