<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Support\ImageUrl;

class CategoriaController extends Controller
{
    public function index(Request $request)
    {
        $categoryId = max(1, (int) $request->query('categoria', 1));
        $category = DB::table('categorias')->where('id_categoria', $categoryId)->first()
            ?? DB::table('categorias')->orderBy('id_categoria')->first();

        abort_unless($category, 404, 'No hay categorías disponibles.');

        $dishes = DB::table('menu_items as m')
            ->leftJoin('negocios as n', 'm.id_negocio', '=', 'n.id_negocio')
            ->where('m.id_categoria', $category->id_categoria)
            ->orderBy('m.nombre')
            ->select([
                'm.id_menu_item as id',
                'm.nombre as plato_nombre',
                'm.descripcion as plato_desc',
                'm.precio',
                'm.stock',
                'm.imagen_url',
                'm.on_promo',
                'm.precio_promocion',
                'm.id_negocio',
                'n.id_negocio as negocio_id',
                DB::raw("COALESCE(n.nombre, 'Restaurante Shizen') as negocio_nombre"),
            ])
            ->get()
            ->map(function ($dish) {
                $dish->imagen_url = ImageUrl::resolve($dish->imagen_url);
                return $dish;
            });

        $authUser = $request->session()->get('auth_user');
        $favoriteDishIds = $authUser
            ? DB::table('favorito')
                ->where('id_usuario', $authUser['id_usuario'])
                ->whereIn('id_menu_item', $dishes->pluck('id'))
                ->pluck('id_menu_item')
                ->map(fn ($id): int => (int) $id)
                ->all()
            : [];
        $dishes->each(function ($dish) use ($favoriteDishIds): void {
            $dish->is_favorite = in_array((int) $dish->id, $favoriteDishIds, true);
        });

        $category->cover_img = ImageUrl::resolve($category->cover_img);

        return view('categorias', compact('category', 'dishes'));
    }
}
