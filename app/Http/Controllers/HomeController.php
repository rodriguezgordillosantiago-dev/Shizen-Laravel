<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Support\ImageUrl;

class HomeController extends Controller
{
    public function index()
    {
        $businesses = collect();
        $categories = collect();
        if (Schema::hasTable('categorias') && Schema::hasTable('menu_items')) {
            $categories = DB::table('categorias as c')
                ->leftJoin('menu_items as m', 'm.id_categoria', '=', 'c.id_categoria')
                ->select(
                    'c.id_categoria',
                    'c.nombre',
                    'c.icon',
                    DB::raw('COUNT(m.id_menu_item) as platos_count')
                )
                ->groupBy('c.id_categoria', 'c.nombre', 'c.icon')
                ->orderBy('c.nombre')
                ->get();
        }

        if (Schema::hasTable('negocios') && Schema::hasTable('menu_items') && Schema::hasTable('calificacion')) {
            $authUser = session('auth_user');
            $businesses = DB::table('negocios as n')
                ->join('menu_items as m', 'm.id_negocio', '=', 'n.id_negocio')
                ->leftJoin('calificacion as c', 'c.id_negocio', '=', 'n.id_negocio')
                ->select('n.id_negocio as id', 'n.nombre', 'n.logo_url', DB::raw('COALESCE(AVG(c.puntuacion), 0) as rating'))
                ->groupBy('n.id_negocio', 'n.nombre', 'n.logo_url')
                ->orderByDesc('rating')
                ->orderBy('n.nombre')
                ->get()
                ->map(function ($business) {
                    $business->rating = (float) $business->rating;
                    $business->logo_url = ImageUrl::resolve($business->logo_url);
                    $business->is_favorite = false;
                    return $business;
                });

            if ($authUser && Schema::hasTable('favorito')) {
                $favoriteIds = DB::table('favorito')
                    ->where('id_usuario', $authUser['id_usuario'])
                    ->whereNull('id_menu_item')
                    ->whereIn('id_negocio', $businesses->pluck('id'))
                    ->pluck('id_negocio')
                    ->all();

                $businesses->each(function ($business) use ($favoriteIds): void {
                    $business->is_favorite = in_array($business->id, $favoriteIds, true);
                });
            }
        }

        return view('index', compact('businesses', 'categories'));
    }

    public function menu(int $businessId)
    {
        $business = DB::table('negocios as n')
            ->leftJoin('calificacion as c', 'c.id_negocio', '=', 'n.id_negocio')
            ->where('n.id_negocio', $businessId)
            ->select('n.id_negocio as id', 'n.nombre', 'n.logo_url', DB::raw('COALESCE(AVG(c.puntuacion), 0) as rating'))
            ->groupBy('n.id_negocio', 'n.nombre', 'n.logo_url')
            ->first();
        abort_unless($business, 404, 'Negocio no encontrado.');

        $dishes = DB::table('menu_items as m')
            ->where('m.id_negocio', $businessId)
            ->leftJoin('categorias as c', 'c.id_categoria', '=', 'm.id_categoria')
            ->select('m.id_menu_item as id', 'm.nombre', 'm.descripcion', 'm.precio', 'm.stock', 'm.imagen_url', 'm.on_promo', 'm.precio_promocion', 'm.id_categoria as categoria_id', 'c.nombre as categoria_nombre')
            ->orderBy('m.nombre')
            ->get()
            ->map(function ($dish) {
                $dish->imagen_url = ImageUrl::resolve($dish->imagen_url);
                $authUser = session('auth_user');
                $dish->is_favorite = $authUser
                    ? DB::table('favorito')
                        ->where('id_usuario', $authUser['id_usuario'])
                        ->where('id_menu_item', $dish->id)
                        ->exists()
                    : false;
                return $dish;
            });

        $business->logo_url = ImageUrl::resolve($business->logo_url);
        $business->rating = (float) $business->rating;
        $authUser = session('auth_user');
        $business->is_favorite = $authUser
            ? DB::table('favorito')->where('id_usuario', $authUser['id_usuario'])->where('id_negocio', $businessId)->whereNull('id_menu_item')->exists()
            : false;

        return view('menu_negocio', compact('business', 'dishes'));
    }

    public function favorites()
    {
        $authUser = session('auth_user');
        if (!$authUser) {
            return redirect('/login?redirect=/favoritos');
        }
        if (!DB::table('usuario')->where('id_usuario', $authUser['id_usuario'])->exists()) {
            session()->forget('auth_user');

            return redirect()->route('login')->withErrors([
                'email' => 'Tu sesión expiró. Inicia sesión nuevamente para ver favoritos.',
            ]);
        }

        $businesses = DB::table('favorito as f')
            ->join('negocios as n', 'n.id_negocio', '=', 'f.id_negocio')
            ->leftJoin('calificacion as c', 'c.id_negocio', '=', 'n.id_negocio')
            ->where('f.id_usuario', $authUser['id_usuario'])
            ->whereNull('f.id_menu_item')
            ->select('n.id_negocio as id', 'n.nombre', 'n.logo_url', DB::raw('COALESCE(AVG(c.puntuacion), 0) as rating'))
            ->groupBy('n.id_negocio', 'n.nombre', 'n.logo_url')
            ->orderBy('n.nombre')
            ->get()
            ->map(function ($business) {
                $business->rating = (float) $business->rating;
                $business->logo_url = ImageUrl::resolve($business->logo_url);
                return $business;
            });

        $dishes = DB::table('favorito as f')
            ->join('menu_items as m', 'm.id_menu_item', '=', 'f.id_menu_item')
            ->leftJoin('negocios as n', 'n.id_negocio', '=', 'm.id_negocio')
            ->where('f.id_usuario', $authUser['id_usuario'])
            ->whereNotNull('f.id_menu_item')
            ->select(
                'm.id_menu_item as id',
                'm.nombre',
                'm.precio',
                'm.imagen_url',
                'm.id_categoria',
                'm.id_negocio',
                'n.nombre as negocio_nombre'
            )
            ->orderBy('m.nombre')
            ->get()
            ->map(function ($dish) {
                $dish->imagen_url = ImageUrl::resolve($dish->imagen_url);
                return $dish;
            });

        return view('favoritos', compact('businesses', 'dishes'));
    }

    public function toggleFavorite(int $businessId)
    {
        $authUser = session('auth_user');
        if (!$authUser) {
            return redirect('/login?redirect=' . urlencode('/negocios/' . $businessId . '/menu'));
        }
        if (!DB::table('usuario')->where('id_usuario', $authUser['id_usuario'])->exists()) {
            session()->forget('auth_user');

            return redirect()->route('login')->withErrors([
                'email' => 'Tu sesión expiró. Inicia sesión nuevamente para usar favoritos.',
            ]);
        }
        abort_unless(DB::table('negocios')->where('id_negocio', $businessId)->exists(), 404);

        $favorite = DB::table('favorito')
            ->where('id_usuario', $authUser['id_usuario'])
            ->where('id_negocio', $businessId)
            ->whereNull('id_menu_item')
            ->first();

        if ($favorite) {
            DB::table('favorito')->where('id_favorito', $favorite->id_favorito)->delete();
            $isFavorite = false;
        } else {
            DB::table('favorito')->insert([
                'id_usuario' => $authUser['id_usuario'],
                'id_negocio' => $businessId,
                'id_menu_item' => null,
                'fecha_reg' => now()->toDateString(),
            ]);
            $isFavorite = true;
        }

        return back()->with('favorite_message', $isFavorite ? 'Agregado a favoritos.' : 'Eliminado de favoritos.');
    }

}
