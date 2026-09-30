<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FavoriteController extends Controller
{
    public function toggle(Request $request)
    {
        $user = $request->session()->get('auth_user');
        abort_unless(is_array($user) && !empty($user['id_usuario']), 403);
        abort_unless(DB::table('usuario')->where('id_usuario', $user['id_usuario'])->exists(), 403);

        $data = $request->validate([
            'id_negocio' => ['nullable', 'integer', 'min:1'],
            'id_menu_item' => ['nullable', 'integer', 'min:1', 'required_without:id_negocio'],
            'redirect' => ['nullable', 'string', 'max:512'],
        ]);

        $businessId = isset($data['id_negocio']) ? (int) $data['id_negocio'] : null;
        $dishId = isset($data['id_menu_item']) ? (int) $data['id_menu_item'] : null;

        if ($dishId) {
            $dishBusinessId = DB::table('menu_items')->where('id_menu_item', $dishId)->value('id_negocio');
            abort_unless($dishBusinessId, 404, 'Plato no encontrado.');
            if ($businessId && $businessId !== (int) $dishBusinessId) {
                abort(422, 'El plato no pertenece al negocio indicado.');
            }
            $businessId = (int) $dishBusinessId;
        } else {
            abort_unless($businessId && DB::table('negocios')->where('id_negocio', $businessId)->exists(), 404);
        }

        $favoriteQuery = DB::table('favorito')->where('id_usuario', $user['id_usuario']);
        $favoriteQuery = $dishId
            ? $favoriteQuery->where('id_menu_item', $dishId)
            : $favoriteQuery->where('id_negocio', $businessId)->whereNull('id_menu_item');
        $favorite = $favoriteQuery->first();

        if ($favorite) {
            DB::table('favorito')->where('id_favorito', $favorite->id_favorito)->delete();
        } else {
            DB::table('favorito')->insert([
                'id_usuario' => $user['id_usuario'],
                'id_negocio' => $businessId,
                'id_menu_item' => $dishId,
                'fecha_reg' => now()->toDateString(),
            ]);
        }

        $target = (string) ($data['redirect'] ?? '');
        if ($target === 'favoritos.php') {
            $target = route('favorites');
        } elseif ($target !== '' && (
            !str_starts_with($target, '/') ||
            str_starts_with($target, '//') ||
            str_contains($target, '\\') ||
            preg_match('/[\r\n]/', $target)
        )) {
            $target = '';
        }

        return $target !== '' ? redirect()->to($target) : back();
    }
}
