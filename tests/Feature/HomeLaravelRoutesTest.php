<?php

namespace Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class HomeLaravelRoutesTest extends TestCase
{
    public function test_legacy_login_and_registration_urls_render_laravel_views(): void
    {
        $this->get('/login')->assertOk();

        $this->get('/php/login.php')
            ->assertOk()
            ->assertViewIs('login')
            ->assertDontSee('<?php');

        $this->get('/php/registro_usuario.php')
            ->assertOk()
            ->assertViewIs('registro_usuario')
            ->assertDontSee('<?php');
    }

    public function test_search_and_promotions_are_rendered_by_laravel_without_static_data(): void
    {
        $this->get('/buscar?q=vegano')
            ->assertOk()
            ->assertSee('Resultados de búsqueda');

        $this->get('/promociones')
            ->assertOk()
            ->assertSee('Promociones')
            ->assertDontSee('window.DB_PROMOS');
    }

    public function test_homepage_preserves_the_customer_navigation_and_modal_flows(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('loginModal')
            ->assertSee('Términos y Condiciones')
            ->assertDontSee('registerModal')
            ->assertSee('filterTrack')
            ->assertSee('btn-modal-primary');

        $this->withSession([
            'auth_user' => [
                'id_usuario' => 7,
                'nombre' => 'Cliente',
                'apellido' => 'Prueba',
                'email' => 'cliente@example.com',
            ],
        ])->get('/')
            ->assertOk()
            ->assertSee('profileModal')
            ->assertSee('notificationModal')
            ->assertSee('cartCountMenu');
    }

    public function test_cart_and_notification_apis_require_a_customer_session(): void
    {
        $this->json('POST', '/php/sync_carrito.php', [])
            ->assertUnauthorized()
            ->assertJson(['logged_in' => false]);

        $this->getJson('/php/notificaciones.php?json=1')
            ->assertUnauthorized()
            ->assertJson(['success' => false]);
    }

    public function test_business_and_courier_logins_continue_in_their_laravel_apps(): void
    {
        Schema::create('usuario', function (Blueprint $table): void {
            $table->increments('id_usuario');
            $table->string('nombre');
            $table->string('apellido');
            $table->string('email')->unique();
            $table->string('password_hash');
            $table->string('rol');
        });
        Schema::create('negocios', function (Blueprint $table): void {
            $table->increments('id_negocio');
            $table->unsignedInteger('id_usuario');
        });

        $passwordHash = Hash::make('correct-password');
        DB::table('usuario')->insert([
            [
                'id_usuario' => 1,
                'nombre' => 'Negocio',
                'apellido' => 'Prueba',
                'email' => 'negocio@example.com',
                'password_hash' => $passwordHash,
                'rol' => 'Negocio',
            ],
            [
                'id_usuario' => 2,
                'nombre' => 'Repartidor',
                'apellido' => 'Prueba',
                'email' => 'repartidor@example.com',
                'password_hash' => $passwordHash,
                'rol' => 'Repartidor',
            ],
        ]);
        DB::table('negocios')->insert(['id_usuario' => 1]);

        $this->post('/login', [
            'email' => 'negocio@example.com',
            'password' => 'correct-password',
        ])->assertRedirect('http://localhost/shizen-negocio/public/login');

        $this->post('/login', [
            'email' => 'repartidor@example.com',
            'password' => 'correct-password',
        ])->assertRedirect('http://localhost/shizen-repartidor/public/');
    }

    public function test_legacy_business_and_courier_login_links_open_laravel_apps(): void
    {
        $this->get('/auth/login.php')->assertRedirect(route('login'));

        $this->get('/php/login_negocio.php')
            ->assertRedirect('http://localhost/shizen-negocio/public/login');

        $this->get('/php/login_repartidor.php')
            ->assertRedirect('http://localhost/shizen-repartidor/public/');
    }

    public function test_customer_profile_keeps_account_activity_sections_empty(): void
    {
        Schema::create('usuario', function (Blueprint $table): void {
            $table->unsignedInteger('id_usuario')->primary();
            $table->string('nombre');
            $table->string('apellido');
            $table->string('direccion')->nullable();
            $table->string('email');
            $table->string('ciudad')->nullable();
        });

        DB::table('usuario')->insert([
            'id_usuario' => 7,
            'nombre' => 'Cliente',
            'apellido' => 'Prueba',
            'direccion' => 'Calle 1',
            'email' => 'cliente@example.com',
            'ciudad' => 'Chapinero',
        ]);

        $this->withSession([
            'auth_user' => [
                'id_usuario' => 7,
                'nombre' => 'Cliente',
                'apellido' => 'Prueba',
                'email' => 'cliente@example.com',
            ],
        ])->get('/perfil')
            ->assertOk()
            ->assertSee('profile-banner--user')
            ->assertSee('editProfileModal')
            ->assertSee('profile-tab-purchases')
            ->assertSee('btn-uprofile-logout')
            ->assertSee('logout-button-text')
            ->assertSee('Aún no has realizado pedidos.')
            ->assertSee('Aún no tienes calificaciones.')
            ->assertSee('Aún no tienes favoritos.')
            ->assertSee('id="ustatPurchases">0', false)
            ->assertSee('id="ustatReviews">0', false)
            ->assertSee('id="ustatFollowing">0', false)
            ->assertDontSee('Pedido #')
            ->assertDontSee('Excelente')
            ->assertDontSee('ufollowingGrid');
    }

    public function test_customer_profile_updates_persisted_fields(): void
    {
        Schema::create('usuario', function (Blueprint $table): void {
            $table->unsignedInteger('id_usuario')->primary();
            $table->string('nombre');
            $table->string('apellido');
            $table->string('direccion')->nullable();
            $table->string('email');
            $table->string('ciudad')->nullable();
        });
        DB::table('usuario')->insert([
            'id_usuario' => 7,
            'nombre' => 'Cliente',
            'apellido' => 'Prueba',
            'direccion' => 'Calle 1',
            'email' => 'cliente@example.com',
            'ciudad' => 'Chapinero',
        ]);

        $this->withSession([
            'auth_user' => [
                'id_usuario' => 7,
                'nombre' => 'Cliente',
                'apellido' => 'Prueba',
                'email' => 'cliente@example.com',
            ],
        ])->post('/perfil', [
            'nombre' => 'Ana',
            'apellido' => 'Verde',
            'direccion' => 'Carrera 2',
            'localidad' => 'Usaquén',
        ])->assertRedirect(route('profile'))
            ->assertSessionHas('profile_success');

        $this->assertDatabaseHas('usuario', [
            'id_usuario' => 7,
            'nombre' => 'Ana',
            'apellido' => 'Verde',
            'direccion' => 'Carrera 2',
            'ciudad' => 'Usaquén',
        ]);
    }
}
