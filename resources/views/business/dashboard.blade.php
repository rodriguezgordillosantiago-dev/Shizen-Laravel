<x-business-layout title="Dashboard">
    @php
        $statusBadges = [
            'recibido' => 'badge-orange',
            'pendiente' => 'badge-orange',
            'en preparacion' => 'badge-yellow',
            'preparado' => 'badge-blue',
            'recogido en negocio' => 'badge-blue',
            'en camino' => 'badge-blue',
            'entregado' => 'badge-green',
            'cancelado' => 'badge-red',
        ];
    @endphp

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon green"><i class="bx bx-package"></i></div>
            <div>
                <div class="stat-value">{{ number_format($prodCount) }}</div>
                <div class="stat-label">Productos del negocio</div>
                <div class="stat-change up"><i class="bx bx-check-shield"></i> Tu catálogo</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon orange"><i class="bx bx-receipt"></i></div>
            <div>
                <div class="stat-value">{{ number_format($pedidosCount) }}</div>
                <div class="stat-label">Pedidos recibidos</div>
                <div class="stat-change up"><i class="bx bx-check-shield"></i> Tu negocio</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon blue"><i class="bx bx-dollar-circle"></i></div>
            <div>
                <div class="stat-value" style="font-size:22px">$ {{ number_format($ingresos, 0, ',', '.') }}</div>
                <div class="stat-label">Ventas acumuladas (COP)</div>
                <div class="stat-change up"><i class="bx bx-trending-up"></i> Total de tu negocio</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon yellow"><i class="bx bx-group"></i></div>
            <div>
                <div class="stat-value">{{ number_format($userCount) }}</div>
                <div class="stat-label">Clientes atendidos</div>
                <div class="stat-change up"><i class="bx bx-user-check"></i> Tus clientes</div>
            </div>
        </div>
    </div>

    <div style="display:grid;grid-template-columns:1.4fr 1fr;gap:20px">
        {{-- Tabla de Pedidos Recientes --}}
        <div class="card">
            <div class="card-header">
                <div>
                    <div class="card-title">Pedidos Recientes de Tu Negocio</div>
                    <div class="card-subtitle">Últimos pedidos recibidos</div>
                </div>
                <a href="{{ route('business.orders') }}" class="btn btn-secondary btn-sm">Ver pedidos</a>
            </div>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>#Pedido</th>
                            <th>Cliente</th>
                            <th>Total (COP)</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($recientes as $r)
                            @php
                                $stKey = strtolower(trim($r->estado));
                                $badgeClass = $statusBadges[$stKey] ?? 'badge-gray';
                                $nombreCliente = trim(($r->nombre ?? '') . ' ' . ($r->apellido ?? '')) ?: 'Cliente #' . $r->id_pedido;
                            @endphp
                            <tr>
                                <td><strong>#{{ $r->id_pedido }}</strong></td>
                                <td>{{ $nombreCliente }}</td>
                                <td><strong style="color:#059669">$ {{ number_format($r->total_pedido, 0, ',', '.') }}</strong></td>
                                <td><span class="badge {{ $badgeClass }}">{{ $r->estado }}</span></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" style="text-align:center;color:#9ca3af;padding:24px">No hay pedidos registrados para tu negocio aún.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Productos Más Vendidos --}}
        <div class="card">
            <div class="card-header">
                <div>
                    <div class="card-title">Productos Más Vendidos</div>
                    <div class="card-subtitle">Ranking de tus platos</div>
                </div>
            </div>
            <div class="card-body" style="padding:0">
                @forelse ($topProducts as $tp)
                    @php
                        $imgUrl = $tp->imagen_url ?? asset('assets/vegano_central_logo.jpg');
                    @endphp
                    <div style="display:flex;align-items:center;gap:12px;padding:14px 20px;border-bottom:{{ !$loop->last ? '1px solid #e5e7eb' : 'none' }}">
                        <div style="width:40px;height:40px;border-radius:8px;overflow:hidden;background:#f3f4f6;flex-shrink:0">
                            <img src="{{ $imgUrl }}"
                                 alt="{{ $tp->nombre }}"
                                 style="width:100%;height:100%;object-fit:cover"
                                 onerror="this.onerror=null;this.src='https://images.unsplash.com/photo-1512621776951-a57141f2eefd?w=400'">
                        </div>
                        <div style="flex:1">
                            <div style="font-size:14px;font-weight:600">{{ $tp->nombre }}</div>
                            <div style="font-size:12px;color:#6b7280">{{ (int)$tp->total_vendidos }} unidades vendidas</div>
                        </div>
                        <div style="font-size:13px;font-weight:700;color:#059669">$ {{ number_format($tp->precio, 0, ',', '.') }}</div>
                    </div>
                @empty
                    <div style="padding:24px;text-align:center;color:#9ca3af">Sin registros de venta en tu negocio.</div>
                @endforelse
            </div>
        </div>
    </div>
</x-business-layout>
