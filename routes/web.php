<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BusinessController;

Route::get('/', function () {
    return redirect()->route('business.dashboard');
});
Route::get('/index.php', fn () => redirect()->route('business.dashboard'));

Route::get('/login', [BusinessController::class, 'showLogin'])->name('login');
Route::post('/login', [BusinessController::class, 'login'])->name('login.store');
Route::post('/logout', [BusinessController::class, 'logout'])->name('logout');
Route::get('/auth/login.php', fn () => redirect()->route('login'));
Route::get('/php/login.php', fn () => redirect()->route('login'));
Route::get('/auth/logout.php', fn () => redirect()->route('login'));
Route::get('/php/logout.php', fn () => redirect()->route('login'));
Route::post('/auth/logout.php', [BusinessController::class, 'logout']);
Route::post('/php/logout.php', [BusinessController::class, 'logout']);
Route::get('/forms/registro_negocio.php', fn () => redirect()->away(
    rtrim((string) config('services.shizen.home_url'), '/') . '/registro-negocio'
))
    ->name('business.registration');
Route::get('/registro', fn () => redirect()->route('business.registration'));

Route::middleware('business.auth')->group(function (): void {
    Route::get('/dashboard', [BusinessController::class, 'dashboard'])->name('business.dashboard');
    Route::get('/productos', [BusinessController::class, 'products'])->name('business.products');
    Route::get('/pedidos', [BusinessController::class, 'orders'])->name('business.orders');
    Route::post('/pedidos/{orderId}/estado', [BusinessController::class, 'updateOrderStatus'])->name('business.orders.status');
    Route::get('/clientes', [BusinessController::class, 'clients'])->name('business.clients');
    Route::get('/notificaciones', [BusinessController::class, 'notifications'])->name('business.notifications');
    Route::post('/notificaciones', [BusinessController::class, 'notifications'])->name('business.notifications.read');
    Route::get('/configuracion', [BusinessController::class, 'settings'])->name('business.settings');
    Route::post('/configuracion', [BusinessController::class, 'updateSettings'])->name('business.settings.update');
    Route::get('/perfil', [BusinessController::class, 'profile'])->name('business.profile');
    Route::post('/perfil', [BusinessController::class, 'updateProfile'])->name('business.profile.update');
    Route::post('/productos', [BusinessController::class, 'storeProduct'])->name('business.products.store');
    Route::put('/productos/{productId}', [BusinessController::class, 'updateProduct'])->name('business.products.update');
    Route::delete('/productos/{productId}', [BusinessController::class, 'deleteProduct'])->name('business.products.delete');
    Route::post('/productos/{productId}/promocion', [BusinessController::class, 'togglePromotion'])->name('business.products.promotion');

    Route::get('/pages/{page}.php', function (string $page) {
        $legacyPages = [
            'dashboard' => 'business.dashboard',
            'productos' => 'business.products',
            'pedidos' => 'business.orders',
            'clientes' => 'business.clients',
            'notificaciones' => 'business.notifications',
            'configuracion' => 'business.settings',
            'perfil' => 'business.profile',
        ];

        abort_unless(isset($legacyPages[$page]), 404);

        return redirect()->route($legacyPages[$page]);
    })->where('page', 'dashboard|productos|pedidos|clientes|notificaciones|configuracion|perfil');
});
