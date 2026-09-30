<?php

namespace Tests\Feature;

use App\Http\Middleware\BusinessAuth;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class BusinessAuthenticationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

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
            $table->string('nombre');
        });

        Route::middleware(['web', BusinessAuth::class])->get('/__test/business-auth', fn () => response()->noContent());
    }

    public function test_business_login_authenticates_only_business_accounts(): void
    {
        $userId = $this->createUser('negocio');
        $this->createBusiness($userId);

        $this->post('/login', [
            'email' => ' OWNER@example.com ',
            'password' => 'correct-password',
        ])->assertRedirect(route('business.dashboard'))
            ->assertSessionHas('business_user.id_usuario', $userId)
            ->assertSessionHas('business_user.rol', 'negocio');
    }

    public function test_login_screen_is_rendered_by_laravel(): void
    {
        $this->get('/login')
            ->assertOk()
            ->assertSee('Iniciar sesión')
            ->assertSee(route('business.registration'), false);
    }

    public function test_non_business_role_cannot_login_even_when_a_business_record_exists(): void
    {
        $userId = $this->createUser('repartidor');
        $this->createBusiness($userId);

        $this->post('/login', [
            'email' => 'owner@example.com',
            'password' => 'correct-password',
        ])->assertSessionHasErrors('email');
    }

    public function test_business_middleware_rejects_incomplete_or_wrong_role_sessions(): void
    {
        $this->withSession([
            'business_user' => [
                'id_usuario' => 2,
                'id_negocio' => 3,
                'rol' => 'repartidor',
            ],
        ])->get('/__test/business-auth')
            ->assertRedirect(route('login'))
            ->assertSessionMissing('business_user');
    }

    public function test_legacy_logout_urls_only_invalidate_sessions_on_post(): void
    {
        $session = [
            'business_user' => [
                'id_usuario' => 8,
                'id_negocio' => 4,
                'rol' => 'negocio',
            ],
        ];

        $this->withSession($session)->get('/php/logout.php')
            ->assertRedirect(route('login'))
            ->assertSessionHas('business_user.id_usuario', 8);

        $this->post('/php/logout.php')
            ->assertRedirect(route('login'))
            ->assertSessionMissing('business_user');
    }

    public function test_business_registration_link_routes_to_the_shared_registration_form(): void
    {
        $this->get(route('business.registration'))
            ->assertRedirect(rtrim(config('services.shizen.home_url'), '/') . '/registro-negocio');
    }

    public function test_settings_are_created_and_validated_without_migrating_existing_business_data(): void
    {
        $userId = $this->createUser('negocio');
        $businessId = $this->createBusiness($userId);

        $session = [
            'business_user' => [
                'id_usuario' => $userId,
                'id_negocio' => $businessId,
                'rol' => 'negocio',
            ],
        ];

        $this->withSession($session)->get('/configuracion')->assertOk();
        $this->assertTrue(Schema::hasTable('negocio_configuracion'));

        $this->withSession($session)->post('/configuracion', [
            'minOrder' => 20000,
            'radius' => 8.5,
            'prepTime' => 35,
            'maxOrders' => 20,
            'cash_enabled' => 1,
            'digital_enabled' => 1,
            'card_enabled' => 0,
        ])->assertRedirect('/configuracion');

        $this->assertDatabaseHas('negocio_configuracion', [
            'id_negocio' => $businessId,
            'min_order' => 20000,
            'radius' => 8.5,
            'prep_time' => 35,
            'max_orders' => 20,
            'card_enabled' => 0,
        ]);
    }

    public function test_product_page_adds_only_missing_promotion_columns(): void
    {
        Schema::create('categorias', function (Blueprint $table): void {
            $table->increments('id_categoria');
            $table->string('nombre');
        });
        Schema::create('menu_items', function (Blueprint $table): void {
            $table->increments('id_menu_item');
            $table->unsignedInteger('id_negocio');
            $table->unsignedInteger('id_categoria')->nullable();
            $table->string('nombre');
            $table->text('descripcion')->nullable();
            $table->decimal('precio', 10, 2);
            $table->unsignedInteger('stock')->default(0);
            $table->string('imagen_url')->nullable();
        });

        $userId = $this->createUser('negocio');
        $businessId = $this->createBusiness($userId);
        $this->withSession([
            'business_user' => [
                'id_usuario' => $userId,
                'id_negocio' => $businessId,
                'rol' => 'negocio',
            ],
        ])->get('/productos')->assertOk();

        $this->assertTrue(Schema::hasColumn('menu_items', 'on_promo'));
        $this->assertTrue(Schema::hasColumn('menu_items', 'precio_promocion'));
    }

    private function createUser(string $role): int
    {
        return (int) \DB::table('usuario')->insertGetId([
            'nombre' => 'Test',
            'apellido' => 'Owner',
            'email' => 'owner@example.com',
            'password_hash' => Hash::make('correct-password'),
            'rol' => $role,
        ], 'id_usuario');
    }

    private function createBusiness(int $userId): int
    {
        return (int) \DB::table('negocios')->insertGetId([
            'id_usuario' => $userId,
            'nombre' => 'Test Business',
        ], 'id_negocio');
    }
}
