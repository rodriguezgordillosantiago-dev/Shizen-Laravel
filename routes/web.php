<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InicioController;
use App\Http\Controllers\PedidoController;
use App\Http\Controllers\PerfilController;
use App\Http\Controllers\EntregaController;
use App\Http\Controllers\AuthController;

// Inicio
Route::get('/', function() {
    return view('auth.login');
})->name('login');
Route::get('/login', function() { return view('auth.login'); });
Route::get('/auth/login.php', function() { return view('auth.login'); });
Route::get('/php/login.php', function() { return view('auth.login'); });
Route::get('/index.html', function() { return redirect('/'); });
Route::get('/index.php', function() { return redirect('/'); });
Route::get('/php/index.html', function() { return redirect('/'); });
Route::get('/php/index.php', function() { return redirect('/'); });
Route::get('/pages/index.html', function() { return redirect('/'); });
Route::get('/pages/index.php', function() { return redirect('/'); });

// Formularios y Login
Route::post('/auth/login.php', [AuthController::class, 'login']);
Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/php/login.php', [AuthController::class, 'login']);
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');

Route::middleware('courier')->group(function (): void {
    Route::get('/inicio', [InicioController::class, 'index'])->name('inicio');
    Route::get('/pages/inicio.html', [InicioController::class, 'index']);
    Route::get('/php/inicio.php', [InicioController::class, 'index']);

    Route::get('/pedidos', [PedidoController::class, 'pedidos'])->name('pedidos');
    Route::get('/pages/pedidos.html', [PedidoController::class, 'pedidos']);
    Route::get('/activos', [PedidoController::class, 'activos'])->name('activos');
    Route::get('/pages/activos.html', [PedidoController::class, 'activos']);
    Route::get('/historial', [PedidoController::class, 'historial'])->name('historial');
    Route::get('/pages/historial.html', [PedidoController::class, 'historial']);

    Route::match(['get', 'post'], '/notificaciones', [PedidoController::class, 'notificaciones'])->name('notificaciones');
    Route::match(['get', 'post'], '/pages/notificaciones.php', [PedidoController::class, 'notificaciones']);
    Route::match(['get', 'post'], '/php/notificaciones.php', [PedidoController::class, 'notificaciones']);

    Route::get('/perfil', [PerfilController::class, 'index'])->name('perfil');
    Route::get('/pages/perfil.html', [PerfilController::class, 'index']);
    Route::get('/php/perfil.php', [PerfilController::class, 'index']);
    Route::post('/php/perfil.php', [PerfilController::class, 'update']);
    Route::get('/pages/perfil.php', [PerfilController::class, 'index']);
    Route::post('/pages/perfil.php', [PerfilController::class, 'update']);
    Route::get('/configuracion', [PerfilController::class, 'configuracion'])->name('configuracion');
    Route::get('/pages/configuracion.html', [PerfilController::class, 'configuracion']);
    Route::post('/configuracion', [PerfilController::class, 'guardarConfiguracion'])->name('configuracion.guardar');
    Route::post('/php/configuracion.php', [PerfilController::class, 'guardarConfiguracion']);
    Route::post('/pages/configuracion.php', [PerfilController::class, 'guardarConfiguracion']);
    Route::post('/perfil', [PerfilController::class, 'update'])->name('perfil.update');

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::post('/php/logout.php', [AuthController::class, 'logout']);

    Route::get('/api/entregas', [EntregaController::class, 'index']);
    Route::post('/api/entregas', [EntregaController::class, 'update']);
    Route::get('/php/entregas.php', [EntregaController::class, 'index']);
    Route::post('/php/entregas.php', [EntregaController::class, 'update']);
});
