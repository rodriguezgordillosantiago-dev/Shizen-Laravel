<?php

namespace App\Http\Controllers;

use App\Support\ImageUrl;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class CartController extends Controller
{
    public function sync(Request $request): JsonResponse
    {
        $user = $request->session()->get('auth_user');
        $userId = is_array($user) ? (int) ($user['id_usuario'] ?? 0) : 0;

        if ($userId < 1) {
            return response()->json(['logged_in' => false, 'items' => []], $request->isMethod('post') ? 401 : 200);
        }

        if ($request->isMethod('get')) {
            $items = DB::table('carrito as c')
                ->join('menu_items as m', 'm.id_menu_item', '=', 'c.id_menu_item')
                ->join('negocios as n', 'n.id_negocio', '=', 'c.id_negocio')
                ->where('c.id_usuario', $userId)
                ->orderByDesc('c.fecha_actualizacion')
                ->orderByDesc('c.id_carrito')
                ->select([
                    'c.id_menu_item as id',
                    'c.id_negocio as businessId',
                    'c.cantidad as quantity',
                    'c.precio_unitario as price',
                    'c.fecha_actualizacion',
                    'm.nombre as name',
                    'm.imagen_url as image',
                    'n.nombre as restaurant',
                ])
                ->get()
                ->map(fn (object $item): array => [
                    'id' => (int) $item->id,
                    'businessId' => (int) $item->businessId,
                    'quantity' => (int) $item->quantity,
                    'price' => (float) $item->price,
                    'name' => $item->name,
                    'image' => ImageUrl::resolve($item->image),
                    'restaurant' => $item->restaurant,
                    'addedAt' => strtotime((string) $item->fecha_actualizacion) * 1000,
                ]);

            return response()->json(['logged_in' => true, 'items' => $items]);
        }

        $payload = $request->all();
        if (array_is_list($payload)) {
            $payload = ['items' => $payload];
        }

        $data = Validator::make($payload, [
            'items' => ['present', 'array', 'max:100'],
            'items.*.id' => ['required', 'integer', 'min:1'],
            'items.*.quantity' => ['required', 'integer', 'between:1,99'],
        ])->validate();

        DB::transaction(function () use ($userId, $data): void {
            $items = collect($data['items'])->groupBy(fn (array $item): int => (int) $item['id'])
                ->map(fn ($group): array => [
                    'id' => (int) $group->first()['id'],
                    'quantity' => $group->sum(fn (array $item): int => (int) $item['quantity']),
                ]);
            if ($items->contains(fn (array $item): bool => $item['quantity'] > 99)) {
                throw ValidationException::withMessages(['items' => 'La cantidad de un plato no es válida.']);
            }

            $ids = $items->pluck('id')->map(fn ($id): int => (int) $id)->unique()->values();
            $products = DB::table('menu_items')
                ->whereIn('id_menu_item', $ids)
                ->lockForUpdate()
                ->get(['id_menu_item', 'id_negocio', 'precio', 'stock', 'on_promo', 'precio_promocion'])
                ->keyBy('id_menu_item');

            if ($products->count() !== $ids->count()) {
                throw ValidationException::withMessages(['items' => 'Uno o más platos ya no están disponibles.']);
            }

            DB::table('carrito')->where('id_usuario', $userId)->delete();

            foreach ($items as $item) {
                $product = $products[(int) $item['id']];
                if (!$product->id_negocio) {
                    throw ValidationException::withMessages(['items' => 'Este plato no tiene un negocio disponible.']);
                }
                if ($item['quantity'] > (int) $product->stock) {
                    throw ValidationException::withMessages(['items' => 'No hay suficientes existencias del plato solicitado.']);
                }

                $price = (bool) $product->on_promo && (float) $product->precio_promocion > 0
                    ? $product->precio_promocion
                    : $product->precio;

                DB::table('carrito')->insert([
                    'id_usuario' => $userId,
                    'id_negocio' => $product->id_negocio,
                    'id_menu_item' => $product->id_menu_item,
                    'cantidad' => (int) $item['quantity'],
                    'precio_unitario' => $price,
                    'fecha_actualizacion' => now(),
                ]);
            }
        });

        return response()->json(['success' => true, 'count' => count($data['items'])]);
    }
}
