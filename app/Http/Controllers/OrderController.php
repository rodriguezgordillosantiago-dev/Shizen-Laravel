<?php

namespace App\Http\Controllers;

use App\Services\Pedido;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function __construct(private readonly Pedido $pedidos)
    {
    }

    public function index(Request $request)
    {
        $user = $this->user($request);
        $orders = $this->pedidos->forUser($user['id_usuario']);

        return view('pedidos', compact('orders'));
    }

    public function store(Request $request)
    {
        $user = $this->user($request);
        $data = $request->validate([
            'items' => ['required', 'json'],
            'nombre' => ['required', 'string', 'max:60'],
            'apellido' => ['required', 'string', 'max:60'],
            'numero_documento' => ['required', 'digits_between:5,20'],
            'correo' => ['required', 'email', 'max:120'],
            'direccion' => ['required', 'string', 'max:255'],
            'localidad' => ['nullable', 'string', 'max:100'],
            'metodo_pago' => ['required', 'in:Contraentrega,Transferencia,Nequi,Daviplata'],
        ]);
        $items = json_decode($data['items'], true);
        if (!is_array($items) || !$items) {
            return back()->withErrors(['items' => 'El carrito está vacío.']);
        }

        try {
            $orderId = $this->pedidos->create($user['id_usuario'], $items, $data);
        } catch (\InvalidArgumentException | \RuntimeException $exception) {
            return back()->withErrors(['items' => $exception->getMessage()]);
        }

        $orderedItemIds = collect($items)->pluck('id')->map(fn ($id): int => (int) $id)->unique()->values();

        return redirect()->route('orders.show', $orderId)
            ->with('order_created', true)
            ->with('ordered_item_ids', $orderedItemIds);
    }

    public function show(Request $request, int $orderId)
    {
        $user = $this->user($request);
        $order = $this->pedidos->findForUser($user['id_usuario'], $orderId);
        abort_unless($order, 404);
        $rated = $this->pedidos->isRated($user['id_usuario'], $orderId);
        $businessRated = $this->pedidos->isBusinessRated($user['id_usuario'], (int) $order->id_negocio);

        return view('pedido_detalle', compact('order', 'rated', 'businessRated'));
    }

    public function confirm(Request $request, int $orderId)
    {
        $user = $this->user($request);
        $data = $request->validate(['codigo' => ['required', 'digits:6']]);
        if (!$this->pedidos->confirmDelivery($user['id_usuario'], $orderId, $data['codigo'])) {
            return back()->withErrors(['codigo' => 'El código no es válido o el pedido ya fue confirmado.']);
        }

        return back()->with('order_success', 'Pedido recibido correctamente. Ya puedes calificar al repartidor.');
    }

    public function rate(Request $request, int $orderId)
    {
        $user = $this->user($request);
        $data = $request->validate(['puntuacion' => ['required', 'integer', 'between:1,5'], 'comentario' => ['nullable', 'string', 'max:500']]);
        abort_unless($this->pedidos->rate(
            $user['id_usuario'],
            $orderId,
            $data['puntuacion'],
            $data['comentario'] ?? null
        ), 404);

        return back()->with('order_success', 'Gracias por calificar tu entrega.');
    }

    public function rateBusiness(Request $request, int $orderId)
    {
        $user = $this->user($request);
        $data = $request->validate([
            'puntuacion' => ['required', 'integer', 'between:1,5'],
            'comentario' => ['nullable', 'string', 'max:500'],
        ]);

        abort_unless($this->pedidos->rateBusiness(
            $user['id_usuario'],
            $orderId,
            $data['puntuacion'],
            $data['comentario'] ?? null
        ), 404);

        return back()->with('order_success', 'Gracias por calificar el negocio.');
    }

    public function legacyConfirm(Request $request)
    {
        $data = $request->validate([
            'id' => ['required', 'integer', 'min:1'],
            'codigo' => ['required_without:codigo_entrega', 'digits:6'],
            'codigo_entrega' => ['required_without:codigo', 'digits:6'],
        ]);
        $request->merge(['codigo' => $data['codigo'] ?? $data['codigo_entrega']]);

        return $this->confirm($request, (int) $data['id']);
    }

    public function legacyRate(Request $request)
    {
        $data = $request->validate(['id' => ['required', 'integer', 'min:1']]);

        return $this->rate($request, (int) $data['id']);
    }

    public function legacyRateBusiness(Request $request)
    {
        $data = $request->validate(['id' => ['required', 'integer', 'min:1']]);

        return $this->rateBusiness($request, (int) $data['id']);
    }

    private function user(Request $request): array
    {
        $user = $request->session()->get('auth_user');
        abort_unless(is_array($user) && !empty($user['id_usuario']), 403);
        abort_unless(DB::table('usuario')->where('id_usuario', $user['id_usuario'])->exists(), 403);
        return $user;
    }
}
