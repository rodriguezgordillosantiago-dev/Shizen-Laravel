<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\RepartidorController;
use App\Support\ImageUrl;

// Página principal
Route::get('/', [HomeController::class, 'index']);
Route::get('/negocios/{businessId}/menu', [HomeController::class, 'menu'])->name('business.menu');
Route::get('/favoritos', [HomeController::class, 'favorites'])->name('favorites');
Route::post('/negocios/{businessId}/favorito', [HomeController::class, 'toggleFavorite'])->name('business.favorite');
Route::post('/favoritos/toggle', [FavoriteController::class, 'toggle'])->name('favorites.toggle');

// Redirecciones de "Volver al inicio"
Route::get('/index.php', function () { return redirect('/'); });
Route::get('/index.html', function () { return redirect('/'); });
Route::get('/php/index.html', function () { return redirect('/'); });
Route::get('/php/index.php', function () { return redirect('/'); });
Route::get('/pages/index.html', function () { return redirect('/'); });
Route::get('/pages/index.php', function () { return redirect('/'); });
Route::get('/forms/home.html', fn () => redirect('/'));
Route::get('/forms/navegacion.html', fn () => redirect('/'));
Route::get('/forms/registro_usuario.html', fn () => redirect('/registro-usuario'));
Route::get('/forms/registro_negocio.html', fn () => redirect('/registro-negocio'));
Route::get('/forms/registro_repartidor.html', fn () => redirect('/registro-repartidor'));
Route::get('/forms/documentos_negocio.html', [AuthController::class, 'showDocumentosNegocio']);
Route::get('/forms/registro_exitoso.html', fn () => redirect()->route('registro.exitoso'));
Route::get('/forms/documentos_repartidor.php', [AuthController::class, 'showDocumentosRepartidor']);
Route::post('/forms/documentos_repartidor.php', [AuthController::class, 'finishRegistroRepartidor']);
Route::get('/forms/home.php', fn () => redirect('/'));
Route::get('/forms/navegacion.php', fn () => redirect('/'));
Route::get('/forms/categorias.php', [CategoriaController::class, 'index']);
Route::get('/forms/promociones.php', fn () => redirect()->route('promotions'));

// Categorías
Route::get('/categorias', [CategoriaController::class, 'index'])->name('categories');
Route::get('/php/categorias.php', [CategoriaController::class, 'index']);
Route::get('/pages/categorias.html', [CategoriaController::class, 'index']);

// Promociones
Route::get('/promociones', function () {
    $promotions = collect();
    $categories = collect();
    if (
        \Illuminate\Support\Facades\Schema::hasTable('promociones') &&
        \Illuminate\Support\Facades\Schema::hasTable('menu_items')
    ) {
        $promotions = \Illuminate\Support\Facades\DB::table('promociones as p')
            ->join('menu_items as m', 'p.id_menu_item', '=', 'm.id_menu_item')
            ->leftJoin('negocios as n', 'n.id_negocio', '=', 'p.id_negocio')
            ->leftJoin('categorias as c', 'c.id_categoria', '=', 'm.id_categoria')
            ->where('p.activo', 1)
            ->where(function ($query) {
                $query->whereNull('p.fecha_inicio')->orWhereDate('p.fecha_inicio', '<=', now()->toDateString());
            })
            ->where(function ($query) {
                $query->whereNull('p.fecha_fin')->orWhereDate('p.fecha_fin', '>=', now()->toDateString());
            })
            ->select([
                'p.id_promocion as id',
                \Illuminate\Support\Facades\DB::raw('COALESCE(p.id_negocio, m.id_negocio) as id_negocio'),
                'p.id_menu_item',
                'p.fecha_fin',
                \Illuminate\Support\Facades\DB::raw('COALESCE(p.nombre, m.nombre) as promo_nombre'),
                \Illuminate\Support\Facades\DB::raw('COALESCE(p.descripcion, m.descripcion) as promo_desc'),
                'm.precio',
                'm.precio_promocion',
                'm.on_promo',
                \Illuminate\Support\Facades\DB::raw('COALESCE(p.imagen_url, m.imagen_url) as imagen_url'),
                'm.id_categoria',
                'c.nombre as categoria_nombre',
                \Illuminate\Support\Facades\DB::raw("COALESCE(n.nombre, 'Restaurante Shizen') as negocio_nombre"),
            ])
            ->orderByDesc('p.id_promocion')
            ->get()
            ->map(function ($item) {
                $item->imagen_url = \App\Support\ImageUrl::resolve($item->imagen_url);
                return $item;
            });
    }
    if (\Illuminate\Support\Facades\Schema::hasTable('categorias')) {
        $categories = \Illuminate\Support\Facades\DB::table('categorias')->orderBy('nombre')->get();
    }

    return view('promociones', compact('promotions', 'categories'));
})->name('promotions');
Route::get('/php/promociones.php', function () {
    return redirect('/promociones');
});
Route::get('/pages/promociones.html', fn () => redirect('/promociones'));

// Formularios de Registro
Route::get('/registro-usuario', [AuthController::class, 'showRegistroUsuario']);
Route::get('/php/registro_usuario.php', [AuthController::class, 'showRegistroUsuario']);
Route::post('/registro-usuario', [AuthController::class, 'registerUser'])->name('registro.usuario');
Route::post('/php/registro_usuario.php', [AuthController::class, 'registerUser']);

Route::view('/registro-exitoso', 'registro_exitoso')->name('registro.exitoso');
Route::post('/registro-negocio/documentos', [AuthController::class, 'finishRegistroNegocio'])->name('registro.negocio.documentos');
Route::post('/registro-repartidor/documentos', [AuthController::class, 'finishRegistroRepartidor'])->name('registro.repartidor.documentos');

Route::get('/registro-negocio', [AuthController::class, 'showRegistroNegocio'])->name('registro.negocio');
Route::get('/php/registro_negocio.php', [AuthController::class, 'showRegistroNegocio']);
Route::post('/registro-negocio', [AuthController::class, 'continueRegistroNegocio']);
Route::post('/php/registro_negocio.php', [AuthController::class, 'continueRegistroNegocio']);

Route::get('/registro-repartidor', [AuthController::class, 'showRegistroRepartidor'])->name('registro.repartidor');
Route::get('/php/registro_repartidor.php', [AuthController::class, 'showRegistroRepartidor']);
Route::post('/registro-repartidor', [AuthController::class, 'continueRegistroRepartidor']);
Route::post('/php/registro_repartidor.php', [AuthController::class, 'continueRegistroRepartidor']);

// Login / Modal de Ingreso
Route::get('/login', function () {
    return view('login');
});
Route::get('/php/login.php', function () {
    return view('login');
});
Route::get('/auth/login.php', fn () => redirect()->route('login'));
Route::get('/php/login_negocio.php', fn () => redirect()->away(
    rtrim((string) config('services.shizen.business_url'), '/') . '/login'
));
Route::get('/php/login_repartidor.php', fn () => redirect()->away(
    rtrim((string) config('services.shizen.courier_url'), '/') . '/'
));

Route::post('/auth/login.php', [AuthController::class, 'login']);
Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/perfil', [AuthController::class, 'updateProfile'])->name('profile.update');
Route::get('/perfil', [AuthController::class, 'profile'])->name('profile');
Route::get('/php/perfil.php', [AuthController::class, 'profile']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/auth/logout.php', fn () => redirect('/'));
Route::post('/auth/logout.php', [AuthController::class, 'logout']);
Route::post('/php/logout.php', [AuthController::class, 'logout']);
Route::get('/repartidor/{section?}', [RepartidorController::class, 'index'])
    ->where('section', 'inicio|activos|pedidos|historial|perfil')
    ->name('courier.dashboard');
Route::post('/repartidor/estado', [RepartidorController::class, 'toggleStatus'])->name('courier.status');
Route::post('/repartidor/entregas/{deliveryId}/aceptar', [RepartidorController::class, 'accept'])->name('courier.delivery.accept');
Route::post('/repartidor/entregas/{deliveryId}/avanzar', [RepartidorController::class, 'advance'])->name('courier.delivery.advance');
Route::post('/repartidor/perfil', [RepartidorController::class, 'updateProfile'])->name('courier.profile.update');
Route::get('/pedidos', [OrderController::class, 'index'])->name('orders');
Route::post('/pedidos', [OrderController::class, 'store'])->name('orders.store');
Route::get('/pedidos/{orderId}', [OrderController::class, 'show'])->name('orders.show');
Route::post('/pedidos/{orderId}/confirmar', [OrderController::class, 'confirm'])->name('orders.confirm');
Route::post('/pedidos/{orderId}/calificar', [OrderController::class, 'rate'])->name('orders.rate');
Route::post('/pedidos/{orderId}/calificar-negocio', [OrderController::class, 'rateBusiness'])->name('orders.rate-business');

Route::get('/buscar', function (\Illuminate\Http\Request $request) {
    $validated = $request->validate(['q' => ['nullable', 'string', 'max:100']]);
    $term = trim((string) ($validated['q'] ?? ''));
    $categories = collect();
    $dishes = collect();

    if ($term !== '' && \Illuminate\Support\Facades\Schema::hasTable('categorias')) {
        $categories = \Illuminate\Support\Facades\DB::table('categorias')
            ->where('nombre', 'like', '%' . $term . '%')
            ->orderBy('nombre')
            ->get();
    }
    if ($term !== '' && \Illuminate\Support\Facades\Schema::hasTable('menu_items')) {
        $dishes = \Illuminate\Support\Facades\DB::table('menu_items as m')
            ->leftJoin('negocios as n', 'm.id_negocio', '=', 'n.id_negocio')
            ->where(function ($search) use ($term) {
                $search->where('m.nombre', 'like', '%' . $term . '%')
                    ->orWhere('m.descripcion', 'like', '%' . $term . '%');
            })
            ->select(
                'm.id_menu_item as id',
                'm.id_negocio as negocio_id',
                'm.nombre',
                'm.descripcion',
                'm.precio',
                'm.precio_promocion',
                'm.on_promo',
                'm.imagen_url',
                'm.id_categoria as categoria_id',
                'n.nombre as negocio_nombre'
            )
            ->orderBy('m.nombre')
            ->get()
            ->map(function ($dish) {
                $dish->imagen_url = ImageUrl::resolve($dish->imagen_url);
                return $dish;
            });
    }

    return view('buscar', compact('term', 'categories', 'dishes'));
})->name('search');
Route::get('/php/buscar.php', fn (\Illuminate\Http\Request $request) => redirect()->route('search', $request->query()));
Route::get('/php/negocio.php', function (\Illuminate\Http\Request $request) {
    $businessId = $request->integer('id');
    abort_unless($businessId > 0, 404);

    return redirect()->route('business.menu', $businessId);
});
Route::get('/php/favoritos.php', fn () => redirect()->route('favorites'));
Route::post('/php/favorito.php', [FavoriteController::class, 'toggle']);
Route::get('/php/pedidos.php', fn () => redirect()->route('orders'));
Route::get('/php/pedido.php', function (\Illuminate\Http\Request $request) {
    $orderId = $request->integer('id');
    abort_unless($orderId > 0, 404);

    return redirect()->route('orders.show', $orderId);
});
Route::post('/php/registrar_pedido.php', [OrderController::class, 'store']);
Route::post('/php/confirmar_entrega.php', [OrderController::class, 'legacyConfirm']);
Route::post('/php/calificar_negocio.php', [OrderController::class, 'legacyRateBusiness']);
Route::post('/php/calificar_repartidor.php', [OrderController::class, 'legacyRate']);
Route::get('/php/registro_exitoso.php', fn () => redirect()->route('registro.exitoso'));
Route::get('/php/documentos_negocio.php', [AuthController::class, 'showDocumentosNegocio']);
Route::get('/php/documentos_repartidor.php', [AuthController::class, 'showDocumentosRepartidor']);
Route::post('/php/documentos_negocio.php', [AuthController::class, 'finishRegistroNegocio']);
Route::post('/php/documentos_repartidor.php', [AuthController::class, 'finishRegistroRepartidor']);
Route::get('/notificaciones', [NotificationController::class, 'index'])->name('notifications');
Route::get('/php/notificaciones.php', [NotificationController::class, 'index']);
Route::post('/php/notificaciones.php', [NotificationController::class, 'markRead']);

// Sincronización de Carrito con BD
Route::match(['get', 'post'], '/php/sync_carrito.php', [CartController::class, 'sync'])->name('cart.sync');
Route::match(['get', 'post'], '/sync_carrito', [CartController::class, 'sync']);
