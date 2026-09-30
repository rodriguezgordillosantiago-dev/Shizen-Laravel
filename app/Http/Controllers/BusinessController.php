<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class BusinessController extends Controller
{
    public function showLogin(Request $request): View|RedirectResponse
    {
        if ($request->session()->has('business_user')) {
            return redirect()->route('business.dashboard');
        }

        return view('business.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $request->merge([
            'email' => strtolower(trim((string) $request->input('email', ''))),
        ]);

        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string', 'min:8', 'max:255'],
        ]);

        $user = DB::table('usuario')
            ->where('email', strtolower(trim($credentials['email'])))
            ->first();

        if (
            ! $user
            || strtolower((string) $user->rol) !== 'negocio'
            || ! is_string($user->password_hash ?? null)
            || ! Hash::check($credentials['password'], $user->password_hash)
        ) {
            return back()->withInput($request->only('email'))->withErrors([
                'email' => 'Correo o contraseña incorrectos.',
            ]);
        }

        $business = DB::table('negocios')
            ->where('id_usuario', $user->id_usuario)
            ->first();

        if (! $business) {
            return back()->withInput($request->only('email'))->withErrors([
                'email' => 'Esta cuenta no está asociada a un negocio.',
            ]);
        }

        $request->session()->regenerate();
        $request->session()->put('business_user', [
            'id_usuario' => (int) $user->id_usuario,
            'nombre' => trim($user->nombre . ' ' . $user->apellido),
            'email' => $user->email,
            'rol' => strtolower((string) $user->rol),
            'id_negocio' => (int) $business->id_negocio,
            'negocio' => $business->nombre,
        ]);

        return redirect()->route('business.dashboard');
    }

    public function logout(Request $request): RedirectResponse
    {
        $request->session()->forget('business_user');
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    public function dashboard(Request $request): View
    {
        $businessId = $this->businessId($request);

        $prodCount = DB::table('menu_items')->where('id_negocio', $businessId)->count();
        $pedidosCount = DB::table('pedido')->where('id_negocio', $businessId)->count();
        $ingresos = (float) DB::table('detalle_pedido as d')
            ->join('pedido as p', 'p.id_pedido', '=', 'd.id_pedido')
            ->where('p.id_negocio', $businessId)
            ->selectRaw('COALESCE(SUM(d.valor * d.cantidad), 0) as total')
            ->value('total');
        $userCount = DB::table('pedido')->where('id_negocio', $businessId)->distinct('id_usuario')->count('id_usuario');

        $recientes = DB::table('pedido as p')
            ->leftJoin('usuario as u', 'u.id_usuario', '=', 'p.id_usuario')
            ->leftJoin('detalle_pedido as d', 'd.id_pedido', '=', 'p.id_pedido')
            ->where('p.id_negocio', $businessId)
            ->select('p.id_pedido', 'p.estado', 'p.fecha_creacion', 'p.direccion_entrega', 'u.nombre', 'u.apellido')
            ->selectRaw('COALESCE(SUM(d.valor * d.cantidad), 0) as total_pedido')
            ->groupBy('p.id_pedido', 'p.estado', 'p.fecha_creacion', 'p.direccion_entrega', 'u.nombre', 'u.apellido')
            ->orderByDesc('p.id_pedido')
            ->limit(5)
            ->get();

        $topProducts = DB::table('menu_items as m')
            ->leftJoin('detalle_pedido as d', 'd.id_menu_item', '=', 'm.id_menu_item')
            ->where('m.id_negocio', $businessId)
            ->select('m.id_menu_item as id', 'm.nombre', 'm.precio', 'm.imagen_url', 'm.id_categoria as categoria')
            ->selectRaw('COALESCE(SUM(d.cantidad), 0) as total_vendidos')
            ->groupBy('m.id_menu_item', 'm.nombre', 'm.precio', 'm.imagen_url', 'm.id_categoria')
            ->orderByDesc('total_vendidos')
            ->orderByDesc('m.id_menu_item')
            ->limit(5)
            ->get();

        foreach ($topProducts as $product) {
            $product->imagen_url = $this->publicImageUrl($product->imagen_url);
        }

        return view('business.dashboard', compact(
            'prodCount', 'pedidosCount', 'ingresos', 'userCount', 'recientes', 'topProducts'
        ));
    }

    public function products(Request $request): View
    {
        $businessId = $this->businessId($request);
        $this->ensureProductPromotionColumns();

        $products = DB::table('menu_items as m')
            ->leftJoin('categorias as c', 'c.id_categoria', '=', 'm.id_categoria')
            ->where('m.id_negocio', $businessId)
            ->select('m.*', 'c.nombre as categoria_nombre')
            ->orderByDesc('m.id_menu_item')
            ->get();

        foreach ($products as $product) {
            $product->imagen_display_url = $this->publicImageUrl($product->imagen_url);
        }

        return view('business.products', [
            'products' => $products,
            'categories' => DB::table('categorias')->orderBy('nombre')->get(),
        ]);
    }

    public function orders(Request $request): View
    {
        $businessId = $this->businessId($request);
        $orders = DB::table('pedido as p')
            ->leftJoin('usuario as u', 'u.id_usuario', '=', 'p.id_usuario')
            ->leftJoin('compra as c', 'c.id_pedido', '=', 'p.id_pedido')
            ->leftJoin('entrega as e', 'e.id_compra', '=', 'c.id_compra')
            ->leftJoin('repartidor as r', 'r.id_repartidor', '=', 'e.id_repartidor')
            ->where('p.id_negocio', $businessId)
            ->select(
                'p.*',
                'p.id_usuario as cliente_usuario_id',
                'u.nombre as cliente_nombre',
                'u.apellido as cliente_apellido',
                'u.email as cliente_email',
                'e.id_entrega',
                'e.id_repartidor',
                'e.codigo_entrega',
                'e.estado as entrega_estado',
                'r.id_usuario as repartidor_usuario_id'
            )
            ->orderByDesc('p.id_pedido')
            ->get();

        foreach ($orders as $order) {
            $order->items = DB::table('detalle_pedido as d')
                ->join('menu_items as m', 'm.id_menu_item', '=', 'd.id_menu_item')
                ->where('d.id_pedido', $order->id_pedido)
                ->select('d.id_detalle', 'd.cantidad', 'd.valor', 'm.nombre as plato_nombre', 'm.imagen_url')
                ->get();
            $order->total = $order->items->sum(fn ($item) => (float) $item->valor * (int) $item->cantidad);
            foreach ($order->items as $item) {
                $item->imagen_url = $this->publicImageUrl($item->imagen_url);
            }
        }

        return view('business.orders', compact('orders'));
    }

    public function updateOrderStatus(Request $request, int $orderId): RedirectResponse
    {
        $businessId = $this->businessId($request);
        $order = DB::table('pedido as p')
            ->leftJoin('compra as c', 'c.id_pedido', '=', 'p.id_pedido')
            ->leftJoin('entrega as e', 'e.id_compra', '=', 'c.id_compra')
            ->leftJoin('repartidor as r', 'r.id_repartidor', '=', 'e.id_repartidor')
            ->where('p.id_pedido', $orderId)
            ->where('p.id_negocio', $businessId)
            ->select(
                'p.*',
                'p.id_usuario as cliente_usuario_id',
                'e.id_entrega',
                'e.id_repartidor',
                'e.codigo_entrega',
                'e.estado as entrega_estado',
                'r.id_usuario as repartidor_usuario_id'
            )
            ->first();
        abort_unless($order, 404);

        $action = $request->validate([
            'action' => ['nullable', 'string', Rule::in([
                'set_prep_time', 'mark_prepared', 'deliver_with_code', 'cancel_order', 'update_status',
            ])],
        ])['action'] ?? 'update_status';

        if ($action === 'set_prep_time') {
            $minutos = (int) $request->validate([
                'minutos' => ['required', 'integer', 'min:1', 'max:180'],
            ])['minutos'];
            abort_if(in_array(strtolower($order->estado), ['entregado', 'cancelado', 'recogido en negocio', 'en camino'], true), 422);
            $horaEstimada = now()->addMinutes($minutos)->format('Y-m-d H:i:s');

            $newEstado = in_array($order->estado, ['Pendiente', 'Recibido', 'pendiente', 'recibido'], true)
                ? 'En preparacion'
                : $order->estado;

            DB::transaction(function () use ($order, $orderId, $minutos, $horaEstimada, $newEstado): void {
                DB::table('pedido')->where('id_pedido', $orderId)->update([
                    'tiempo_preparacion' => $minutos,
                    'hora_estimada_listo' => $horaEstimada,
                    'estado' => $newEstado,
                ]);

                if (! empty($order->repartidor_usuario_id)) {
                    DB::table('notificacion')->insert([
                        'id_usuario' => (int) $order->repartidor_usuario_id,
                        'audiencia' => 'repartidor',
                        'id_pedido' => $orderId,
                        'tipo' => 'tiempo_preparacion',
                        'titulo' => '⏱️ Preparación: ' . $minutos . ' min',
                        'mensaje' => 'El pedido #' . $orderId . ' estará listo en aproximadamente ' . $minutos . ' minutos.',
                    ]);
                }

                if (! empty($order->cliente_usuario_id)) {
                    DB::table('notificacion')->insert([
                        'id_usuario' => (int) $order->cliente_usuario_id,
                        'audiencia' => 'cliente',
                        'id_pedido' => $orderId,
                        'tipo' => 'tiempo_preparacion',
                        'titulo' => '⏱️ Pedido en preparación',
                        'mensaje' => 'El restaurante estima que tu pedido #' . $orderId . ' estará listo en ' . $minutos . ' minutos.',
                    ]);
                }
            });

            return back()->with('success', '⏱️ Tiempo de preparación fijado en ' . $minutos . ' minutos. ¡Notificación enviada!');
        }

        if ($action === 'mark_prepared') {
            abort_if(in_array(strtolower($order->estado), ['entregado', 'cancelado', 'recogido en negocio', 'en camino'], true), 422);

            if (strtolower($order->estado) !== 'preparado') {
                DB::transaction(function () use ($order, $orderId): void {
                    DB::table('pedido')->where('id_pedido', $orderId)->update(['estado' => 'Preparado']);

                    if (! empty($order->repartidor_usuario_id)) {
                        DB::table('notificacion')->insert([
                            'id_usuario' => (int) $order->repartidor_usuario_id,
                            'audiencia' => 'repartidor',
                            'id_pedido' => $orderId,
                            'tipo' => 'pedido_preparado',
                            'titulo' => '✅ ¡Pedido listo para recoger!',
                            'mensaje' => 'El pedido #' . $orderId . ' ya está preparado en el restaurante. ¡Puedes pasar a recogerlo!',
                        ]);
                    }

                    if (! empty($order->cliente_usuario_id)) {
                        DB::table('notificacion')->insert([
                            'id_usuario' => (int) $order->cliente_usuario_id,
                            'audiencia' => 'cliente',
                            'id_pedido' => $orderId,
                            'tipo' => 'pedido_preparado',
                            'titulo' => '✅ Tu pedido está preparado',
                            'mensaje' => 'Tu pedido #' . $orderId . ' ya fue preparado y está listo para que el repartidor lo recoja.',
                        ]);
                    }
                });
            }

            return back()->with('success', '🧑‍🍳 Pedido #' . $orderId . ' marcado como PREPARADO.');
        }

        if ($action === 'deliver_with_code') {
            $code = trim((string) $request->validate([
                'codigo_repartidor' => ['required', 'string', 'max:20'],
            ])['codigo_repartidor']);
            abort_unless(strtolower($order->estado) === 'preparado', 422);

            $expectedCode = ! empty($order->codigo_entrega)
                ? (string) $order->codigo_entrega
                : sprintf('%06d', ((int) ($order->id_entrega ?: $orderId) * 265443) % 900000 + 100000);

            if (! hash_equals($expectedCode, $code) && (string) $orderId !== $code) {
                return back()->withErrors(['codigo_repartidor' => 'El código no coincide con el del repartidor.']);
            }

            DB::transaction(function () use ($order, $orderId): void {
                DB::table('pedido')->where('id_pedido', $orderId)->update(['estado' => 'Recogido en negocio']);
                if (! empty($order->id_entrega)) {
                    DB::table('entrega')->where('id_entrega', $order->id_entrega)->update([
                        'estado' => 'Recogido en negocio',
                    ]);
                }
            });

            return back()->with('success', '✅ ¡Código validado! Pedido #' . $orderId . ' entregado al repartidor. El repartidor completará la entrega al cliente.');
        }

        if ($action === 'cancel_order') {
            abort_if(in_array(strtolower($order->estado), ['entregado', 'cancelado'], true), 422);

            DB::transaction(fn () => DB::table('pedido')->where('id_pedido', $orderId)->update(['estado' => 'Cancelado']));
            return back()->with('success', 'Pedido #' . $orderId . ' cancelado.');
        }

        $data = $request->validate([
            'estado' => ['required', 'string', Rule::in([
                'Pendiente', 'Recibido', 'En preparacion', 'Preparado',
                'Recogido en negocio', 'En camino', 'Entregado', 'Cancelado',
                'pendiente', 'recibido', 'en preparacion', 'preparado',
                'recogido en negocio', 'en camino', 'entregado', 'cancelado',
            ])],
        ]);
        DB::table('pedido')->where('id_pedido', $orderId)->update(['estado' => $data['estado']]);

        return back()->with('success', 'Estado del pedido actualizado.');
    }

    public function clients(Request $request): View
    {
        $businessId = $this->businessId($request);
        $clients = DB::table('usuario as u')
            ->join('pedido as p', 'p.id_usuario', '=', 'u.id_usuario')
            ->join('detalle_pedido as d', 'd.id_pedido', '=', 'p.id_pedido')
            ->where('p.id_negocio', $businessId)
            ->select('u.id_usuario', 'u.nombre', 'u.apellido', 'u.email', 'u.direccion', 'u.localidad', 'u.fecha_registro')
            ->selectRaw('COUNT(DISTINCT p.id_pedido) as total_pedidos')
            ->selectRaw('COALESCE(SUM(d.valor * d.cantidad), 0) as total_gastado')
            ->selectRaw('MAX(p.fecha_creacion) as ultima_compra')
            ->groupBy('u.id_usuario', 'u.nombre', 'u.apellido', 'u.email', 'u.direccion', 'u.localidad', 'u.fecha_registro')
            ->orderByDesc('ultima_compra')
            ->get();

        return view('business.clients', compact('clients'));
    }

    public function notifications(Request $request): View|RedirectResponse
    {
        $userId = (int) $request->session()->get('business_user.id_usuario');

        if ($request->isMethod('post')) {
            DB::table('notificacion')
                ->where('id_usuario', $userId)
                ->where('audiencia', 'negocio')
                ->update(['leida' => 1]);
            return back()->with('success', 'Todas las notificaciones han sido marcadas como leídas.');
        }

        $notifications = DB::table('notificacion')
            ->where('id_usuario', $userId)
            ->where('audiencia', 'negocio')
            ->orderByDesc('fecha_creacion')
            ->limit(50)
            ->get();

        $unread = $notifications->where('leida', 0)->count();

        return view('business.notifications', compact('notifications', 'unread'));
    }

    public function settings(Request $request): View
    {
        $businessId = $this->businessId($request);
        $this->ensureSettingsTable();
        $business = DB::table('negocios')->where('id_negocio', $businessId)->first();
        abort_unless($business, 404);

        $defaults = [
            'min_order' => 15000,
            'radius' => 5,
            'prep_time' => 25,
            'max_orders' => 12,
            'cash_enabled' => 1,
            'digital_enabled' => 1,
            'card_enabled' => 1,
        ];

        $config = DB::table('negocio_configuracion')->where('id_negocio', $businessId)->first();
        $settings = array_merge($defaults, (array) ($config ?: []));

        return view('business.settings', compact('business', 'settings'));
    }

    public function updateSettings(Request $request): RedirectResponse
    {
        $businessId = $this->businessId($request);
        $this->ensureSettingsTable();

        $validated = $request->validate([
            'minOrder' => ['required', 'integer', 'min:0', 'max:100000000'],
            'radius' => ['required', 'numeric', 'gt:0', 'max:500'],
            'prepTime' => ['required', 'integer', 'min:5', 'max:120'],
            'maxOrders' => ['required', 'integer', 'min:1', 'max:1000'],
            'cash_enabled' => ['required', 'in:0,1'],
            'digital_enabled' => ['required', 'in:0,1'],
            'card_enabled' => ['required', 'in:0,1'],
            'nombre' => ['sometimes', 'required', 'string', 'min:2', 'max:100'],
            'direccion' => ['sometimes', 'required', 'string', 'min:5', 'max:255'],
            'gmail_negocio' => ['nullable', 'email', 'max:150'],
        ]);

        $settings = [
            'min_order' => (int) $validated['minOrder'],
            'radius' => (float) $validated['radius'],
            'prep_time' => (int) $validated['prepTime'],
            'max_orders' => (int) $validated['maxOrders'],
            'cash_enabled' => (int) $validated['cash_enabled'],
            'digital_enabled' => (int) $validated['digital_enabled'],
            'card_enabled' => (int) $validated['card_enabled'],
            'updated_at' => now(),
        ];

        DB::table('negocio_configuracion')->updateOrInsert(
            ['id_negocio' => $businessId],
            $settings
        );

        $businessData = [];
        foreach (['nombre', 'direccion', 'gmail_negocio'] as $field) {
            if (array_key_exists($field, $validated)) {
                $businessData[$field] = trim((string) ($validated[$field] ?? ''));
            }
        }
        if (! empty($businessData)) {
            DB::table('negocios')->where('id_negocio', $businessId)->update($businessData);
            if (isset($businessData['nombre'])) {
                $request->session()->put('business_user.negocio', $businessData['nombre']);
            }
        }

        return back()->with('success', 'Configuración guardada exitosamente.');
    }

    public function profile(Request $request): View
    {
        $userId = $request->session()->get('business_user.id_usuario');
        $businessId = $this->businessId($request);
        $this->ensureSettingsTable();

        $user = DB::table('usuario')->where('id_usuario', $userId)->first();
        abort_unless($user, 404);

        $business = DB::table('negocios')->where('id_negocio', $businessId)->first();
        if ($business) {
            $business->logo_display_url = $this->publicImageUrl($business->logo_url ?? null, 'assets/vegano_central_logo.jpg');
        }

        $ratingRow = DB::table('calificacion')
            ->where('id_negocio', $businessId)
            ->selectRaw('AVG(puntuacion) as avg_rating')
            ->first();
        $ratingAvg = ($ratingRow && $ratingRow->avg_rating !== null) ? round((float) $ratingRow->avg_rating, 1) : null;

        $businessConfig = DB::table('negocio_configuracion')->where('id_negocio', $businessId)->first();

        return view('business.profile', compact('user', 'business', 'ratingAvg', 'businessConfig'));
    }

    public function updateProfile(Request $request): RedirectResponse
    {
        $businessId = $this->businessId($request);
        $userId = $request->session()->get('business_user.id_usuario');

        if ($request->input('action') === 'update_business_profile') {
            $data = $request->validate([
                'nombre_negocio' => ['required', 'string', 'min:2', 'max:100'],
                'descripcion_negocio' => ['required', 'string', 'min:5', 'max:1000'],
                'logo_imagen' => ['nullable', 'file', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:5120'],
            ]);

            $updateData = [
                'nombre' => trim($data['nombre_negocio']),
                'descripcion' => trim($data['descripcion_negocio']),
            ];
            if ($request->hasFile('logo_imagen')) {
                $updateData['logo_url'] = $this->storeBusinessImage($request->file('logo_imagen'));
            }

            DB::table('negocios')->where('id_negocio', $businessId)->update($updateData);
            $request->session()->put('business_user.negocio', $updateData['nombre']);

            return back()->with('success', 'Perfil del negocio actualizado correctamente.');
        }

        // Actualización de datos personales del usuario
        $data = $request->validate([
            'nombre' => ['required', 'string', 'min:2', 'max:100'],
            'apellido' => ['required', 'string', 'min:2', 'max:100'],
            'email' => ['required', 'email', 'max:150', Rule::unique('usuario', 'email')->ignore($userId, 'id_usuario')],
        ]);
        DB::table('usuario')->where('id_usuario', $userId)->update($data);
        $request->session()->put('business_user.nombre', trim($data['nombre'] . ' ' . $data['apellido']));

        return back()->with('success', 'Perfil actualizado correctamente.');
    }

    public function storeProduct(Request $request): RedirectResponse
    {
        $this->ensureProductPromotionColumns();
        $data = $this->validatedProduct($request);
        $businessId = $this->businessId($request);
        $data['id_negocio'] = $businessId;
        $data['imagen_url'] = $this->handleImageUpload($request, null);
        $productId = DB::table('menu_items')->insertGetId($data);

        if (!empty($data['on_promo'])) {
            DB::table('promociones')->insert([
                'id_negocio'   => $businessId,
                'id_menu_item' => $productId,
                'nombre'       => $data['nombre'],
                'descripcion'  => $data['descripcion'] ?? null,
                'imagen_url'   => $data['imagen_url'] ?? null,
                'activo'       => 1,
            ]);
        }

        return back()->with('success', 'Producto creado correctamente.');
    }

    public function updateProduct(Request $request, int $productId): RedirectResponse
    {
        $this->ensureProductPromotionColumns();
        $data = $this->validatedProduct($request);
        $businessId = $this->businessId($request);
        $product = DB::table('menu_items')
            ->where('id_menu_item', $productId)
            ->where('id_negocio', $businessId)
            ->first(['imagen_url']);
        abort_unless($product, 404);

        $data['imagen_url'] = $this->handleImageUpload($request, $product->imagen_url);

        DB::table('menu_items')
            ->where('id_menu_item', $productId)
            ->where('id_negocio', $businessId)
            ->update($data);

        if (!empty($data['on_promo'])) {
            DB::table('promociones')->updateOrInsert(
                ['id_menu_item' => $productId],
                [
                    'id_negocio'  => $businessId,
                    'nombre'      => $data['nombre'],
                    'descripcion' => $data['descripcion'] ?? null,
                    'imagen_url'  => $data['imagen_url'] ?? null,
                    'activo'      => 1,
                ]
            );
        } else {
            DB::table('promociones')->where('id_menu_item', $productId)->delete();
        }

        return back()->with('success', 'Producto actualizado correctamente.');
    }

    public function deleteProduct(Request $request, int $productId): RedirectResponse
    {
        $businessId = $this->businessId($request);
        $product = DB::table('menu_items')
            ->where('id_menu_item', $productId)
            ->where('id_negocio', $businessId)
            ->first(['id_menu_item']);
        abort_unless($product, 404);

        DB::table('promociones')
            ->where('id_menu_item', $productId)
            ->where('id_negocio', $businessId)
            ->delete();
        DB::table('menu_items')
            ->where('id_menu_item', $productId)
            ->where('id_negocio', $businessId)
            ->delete();

        return back()->with('success', 'Producto eliminado correctamente.');
    }

    public function togglePromotion(Request $request, int $productId): RedirectResponse
    {
        $this->ensureProductPromotionColumns();
        $product = DB::table('menu_items')
            ->where('id_menu_item', $productId)
            ->where('id_negocio', $this->businessId($request))
            ->first();
        abort_unless($product, 404);

        $active = ! (bool) $product->on_promo;
        DB::table('menu_items')->where('id_menu_item', $productId)->update([
            'on_promo' => $active,
            'precio_promocion' => $active ? ($product->precio_promocion ?: $product->precio) : null,
        ]);

        return back()->with('success', $active ? 'Promoción activada.' : 'Promoción desactivada.');
    }

    private function validatedProduct(Request $request): array
    {
        $validated = $request->validate([
            'nombre'       => ['required', 'string', 'min:2', 'max:100'],
            'descripcion'  => ['nullable', 'string', 'max:1000'],
            'precio'       => ['required', 'integer', 'min:1000', 'max:99999999'],
            'stock'        => ['required', 'integer', 'min:0', 'max:1000000'],
            'id_categoria' => ['nullable', 'integer', 'exists:categorias,id_categoria'],
            'descuento_pct'=> ['nullable', 'integer', 'min:0', 'max:50'],
            'imagen'       => ['nullable', 'file', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:5120'],
        ]);

        $pct   = (int) ($validated['descuento_pct'] ?? 0);
        $precio = (int) $validated['precio'];

        $validated['on_promo']        = $pct > 0 ? 1 : 0;
        $validated['precio_promocion'] = $pct > 0 ? (int) round($precio * (1 - $pct / 100)) : null;

        unset($validated['descuento_pct'], $validated['imagen']);

        return $validated;
    }

    private function handleImageUpload(Request $request, ?string $currentUrl): ?string
    {
        if ($request->hasFile('imagen')) {
            return $this->storeBusinessImage($request->file('imagen'), 'producto_');
        }

        return $currentUrl ?: 'assets/vegano_central_logo.jpg';
    }

    private function storeBusinessImage(\Illuminate\Http\UploadedFile $file, string $prefix = 'logo_'): string
    {
        $extensions = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
            'image/gif' => 'gif',
        ];
        $extension = $extensions[$file->getMimeType()] ?? null;
        abort_unless($extension !== null, 422, 'Tipo de imagen no permitido.');

        $directory = public_path('assets/Imagenes_prueba');
        if (! is_dir($directory) && ! mkdir($directory, 0755, true) && ! is_dir($directory)) {
            abort(500, 'No se pudo preparar el directorio de imágenes.');
        }

        $filename = $prefix . now()->timestamp . '_' . bin2hex(random_bytes(8)) . '.' . $extension;
        $file->move($directory, $filename);

        $basePath = trim((string) parse_url((string) config('app.url'), PHP_URL_PATH), '/');
        $assetPath = 'assets/Imagenes_prueba/' . $filename;

        return ($basePath !== '' ? '/' . $basePath : '') . '/' . $assetPath;
    }

    private function publicImageUrl(?string $url, string $fallback = 'assets/vegano_central_logo.jpg'): string
    {
        $url = trim((string) $url);
        if ($url === '') {
            return asset($fallback);
        }

        if (preg_match('#^https?://#i', $url) && filter_var($url, FILTER_VALIDATE_URL)) {
            return $url;
        }

        if (str_contains($url, 'Imagenes_prueba/')) {
            return asset('assets/Imagenes_prueba/' . rawurlencode(basename($url)));
        }

        if (preg_match('#^[A-Za-z0-9/_\-.]+$#', $url) && ! str_contains($url, '..')) {
            return asset(ltrim($url, '/'));
        }

        return asset($fallback);
    }

    private function ensureSettingsTable(): void
    {
        if (Schema::hasTable('negocio_configuracion')) {
            return;
        }

        Schema::create('negocio_configuracion', function ($table): void {
            $table->unsignedInteger('id_negocio')->primary();
            $table->unsignedInteger('min_order')->default(15000);
            $table->decimal('radius', 6, 2)->default(5);
            $table->unsignedSmallInteger('prep_time')->default(25);
            $table->unsignedSmallInteger('max_orders')->default(12);
            $table->boolean('cash_enabled')->default(true);
            $table->boolean('digital_enabled')->default(true);
            $table->boolean('card_enabled')->default(true);
            $table->timestamp('updated_at')->useCurrent();
        });
    }

    private function ensureProductPromotionColumns(): void
    {
        if (! Schema::hasColumn('menu_items', 'on_promo')) {
            Schema::table('menu_items', function ($table): void {
                $table->boolean('on_promo')->default(false);
            });
        }

        if (! Schema::hasColumn('menu_items', 'precio_promocion')) {
            Schema::table('menu_items', function ($table): void {
                $table->decimal('precio_promocion', 10, 2)->nullable();
            });
        }
    }

    private function businessId(Request $request): int
    {
        return (int) $request->session()->get('business_user.id_negocio');
    }
}
