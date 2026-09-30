<x-business-layout title="Pedidos Recibidos">
    @php
        $statusMap = [
            'recibido'            => ['label' => 'Recibido',             'badge' => 'badge-orange'],
            'pendiente'           => ['label' => 'Pendiente',            'badge' => 'badge-orange'],
            'en preparacion'      => ['label' => 'En preparación',       'badge' => 'badge-yellow'],
            'preparado'           => ['label' => 'Plato Preparado',       'badge' => 'badge-blue'],
            'recogido en negocio' => ['label' => 'Recogido por repartidor','badge' => 'badge-blue'],
            'en camino'           => ['label' => 'En camino',             'badge' => 'badge-blue'],
            'entregado'           => ['label' => 'Entregado',             'badge' => 'badge-green'],
            'cancelado'           => ['label' => 'Cancelado',             'badge' => 'badge-red'],
        ];
    @endphp

    <div class="page-header">
        <div>
            <h1 class="page-header-title">📦 Pedidos Recibidos</h1>
            <p class="page-header-sub">
                {{ $orders->count() }} pedidos en tu negocio. Haz clic en un pedido para gestionar tiempo y código.
            </p>
        </div>
        <div class="search-wrap" style="max-width:300px">
            <i class="bx bx-search"></i>
            <input type="text" class="search-input" placeholder="Buscar pedido o cliente..." oninput="filterOrders(this.value)">
        </div>
    </div>

    {{-- Rejilla de Pedidos distribuida con el mismo estilo de las tarjetas de los platos --}}
    <div id="ordersGrid" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(310px,1fr));gap:20px;width:100%">
        @forelse ($orders as $order)
            @php
                $stKey = strtolower(trim($order->estado));
                $stInfo = $statusMap[$stKey] ?? ['label' => $order->estado, 'badge' => 'badge-gray'];
                $cliente = trim(($order->cliente_nombre ?? '') . ' ' . ($order->cliente_apellido ?? '')) ?: 'Cliente #' . $order->id_pedido;
                $firstItem = $order->items->first();
                $imgUrl = $firstItem->imagen_url ?? asset('assets/vegano_central_logo.jpg');
            @endphp
            <div class="card order-dish-card"
                 data-search="{{ strtolower(htmlspecialchars($cliente . ' ' . $order->id_pedido . ' ' . $order->direccion_entrega, ENT_QUOTES, 'UTF-8')) }}"
                 onclick='openOrderModal(@json($order))'
                 style="cursor:pointer;display:flex;flex-direction:column;border-radius:16px;overflow:hidden;transition:transform 0.15s ease, box-shadow 0.15s ease;border:1px solid #e5e7eb"
                 onmouseover="this.style.transform='translateY(-3px)';this.style.boxShadow='0 10px 25px rgba(0,0,0,0.08)'"
                 onmouseout="this.style.transform='none';this.style.boxShadow='none'">
                
                <div style="position:relative;width:100%;height:150px;background:#f3f4f6;overflow:hidden">
                    <img src="{{ $imgUrl }}"
                         alt="Pedido #{{ $order->id_pedido }}"
                         style="width:100%;height:100%;object-fit:cover"
                         onerror="this.onerror=null;this.src='https://images.unsplash.com/photo-1512621776951-a57141f2eefd?w=400'">
                    
                    <div style="position:absolute;top:10px;left:10px;background:rgba(0,0,0,0.65);color:#fff;padding:4px 10px;border-radius:20px;font-size:12px;font-weight:800;backdrop-filter:blur(4px)">
                        #{{ $order->id_pedido }}
                    </div>

                    <div style="position:absolute;top:10px;right:10px">
                        <span class="badge {{ $stInfo['badge'] }}" style="font-size:11px;padding:5px 10px;font-weight:700">
                            {{ $stInfo['label'] }}
                        </span>
                    </div>
                </div>

                <div style="padding:16px;display:flex;flex-direction:column;flex:1">
                    <div style="font-size:15px;font-weight:800;color:#111827;margin-bottom:2px">
                        {{ $cliente }}
                    </div>
                    <div style="font-size:12px;color:#6b7280;margin-bottom:8px;display:flex;align-items:center;gap:4px">
                        <i class="bx bx-map-pin" style="color:#ef4444"></i> {{ $order->direccion_entrega }}
                    </div>

                    @if (!empty($order->tiempo_preparacion))
                        <div style="display:inline-flex;align-items:center;gap:4px;background:#fff7ed;color:#c2410c;border:1px solid #fed7aa;padding:3px 8px;border-radius:6px;font-size:11px;font-weight:700;margin-bottom:8px">
                            ⏱️ Prep: {{ (int) $order->tiempo_preparacion }} min
                        </div>
                    @endif

                    <div style="background:#f9fafb;border-radius:10px;padding:10px;margin-bottom:12px;font-size:12px;color:#374151">
                        @foreach ($order->items->take(2) as $it)
                            <div style="display:flex;justify-content:space-between;margin:3px 0">
                                <span>{{ $it->plato_nombre ?? 'Plato' }}</span>
                                <strong>x{{ (int) $it->cantidad }}</strong>
                            </div>
                        @endforeach
                        @if ($order->items->count() > 2)
                            <div style="font-size:11px;color:#6b7280;margin-top:4px">+ {{ $order->items->count() - 2 }} plato(s) más</div>
                        @endif
                    </div>

                    <div style="display:flex;align-items:center;justify-content:space-between;margin-top:auto;padding-top:10px;border-top:1px dashed #e5e7eb">
                        <div>
                            <div style="font-size:10px;color:#6b7280;text-transform:uppercase;font-weight:700">Total (COP)</div>
                            <div style="font-size:18px;font-weight:900;color:#059669">$ {{ number_format($order->total, 0, ',', '.') }}</div>
                        </div>
                        <button type="button" class="btn btn-primary btn-sm" style="font-weight:700;padding:8px 12px;border-radius:8px;justify-content:center;text-align:center;display:inline-flex;align-items:center">
                            Gestionar Pedido
                        </button>
                    </div>

                </div>

            </div>
        @empty
            <div class="card empty-state" style="grid-column:1/-1;padding:40px;text-align:center;color:#9ca3af">No hay pedidos para este negocio.</div>
        @endforelse
    </div>

    {{-- MODAL DE GESTIÓN DE PEDIDO --}}
    <div id="orderModal" style="display:none;position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,.6);z-index:9999;align-items:center;justify-content:center;padding:20px;backdrop-filter:blur(3px)">
        <div class="card" style="width:100%;max-width:540px;max-height:92vh;overflow-y:auto;border-radius:20px;box-shadow:0 20px 40px rgba(0,0,0,0.25)">
            <div class="card-header" style="background:#f9fafb;border-bottom:1px solid #e5e7eb;padding:16px 20px">
                <div class="card-title" id="mOrderTitle" style="font-size:18px;font-weight:800;color:#111827">Gestión de Pedido</div>
                <button type="button" onclick="closeOrderModal()" class="btn btn-secondary btn-sm" style="padding:4px 10px;border-radius:8px;font-weight:700">✕</button>
            </div>
            <div class="card-body" style="padding:20px">
                
                <div style="background:#f9fafb;padding:14px;border-radius:12px;border:1px solid #e5e7eb;margin-bottom:16px">
                    <div style="font-size:14px;font-weight:800;color:#111827" id="mCliente"></div>
                    <div style="font-size:12px;color:#4b5563;margin-top:4px" id="mDireccion"></div>
                    <div style="font-size:12px;color:#6b7280;margin-top:2px" id="mFecha"></div>
                </div>

                <div style="margin-bottom:16px">
                    <div style="font-size:11px;font-weight:800;color:#6b7280;text-transform:uppercase;letter-spacing:0.5px;margin-bottom:8px">Platos ordenados</div>
                    <div id="mItemsList" style="display:flex;flex-direction:column;gap:6px"></div>
                </div>

                <div style="display:flex;justify-content:space-between;align-items:center;padding:12px 14px;background:#ecfdf5;border-radius:10px;border:1px solid #a7f3d0;margin-bottom:16px">
                    <span style="font-size:13px;font-weight:700;color:#065f46">Total (COP):</span>
                    <span style="font-size:20px;font-weight:900;color:#059669" id="mTotal"></span>
                </div>

                {{-- SECCIÓN TIEMPO DE PREPARACIÓN --}}
                <div id="mPrepTimeArea" style="margin-bottom:16px"></div>

                {{-- SECCIÓN ACCIÓN CÓDIGO REPARTIDOR / ESTADOS --}}
                <div id="mActionArea"></div>

            </div>
        </div>
    </div>

    <script>
    const csrfToken = @json(csrf_token());

    function setPrepMinutes(val) {
        const inp = document.getElementById('inputMinutosPrep');
        if (inp) inp.value = val;
    }

    function openOrderModal(order) {
        document.getElementById('mOrderTitle').textContent = 'Pedido #' + order.id_pedido + ' — Estado: ' + order.estado;
        const clienteNombre = (order.cliente_nombre || '') + ' ' + (order.cliente_apellido || '');
        document.getElementById('mCliente').textContent = '👤 ' + (clienteNombre.trim() || ('Cliente #' + order.id_pedido));
        document.getElementById('mDireccion').textContent = '📍 ' + (order.direccion_entrega || 'Sin dirección');
        document.getElementById('mFecha').textContent = '🕒 Fecha: ' + (order.fecha_creacion || '');
        document.getElementById('mTotal').textContent = '$ ' + Number(order.total).toLocaleString('es-CO') + ' COP';

        const itemsList = document.getElementById('mItemsList');
        itemsList.replaceChildren();
        (order.items || []).forEach(it => {
            const row = document.createElement('div');
            row.style.cssText = 'display:flex;justify-content:space-between;padding:8px 10px;background:#f8fafc;border-radius:8px;border:1px solid #e2e8f0;font-size:13px';

            const name = document.createElement('span');
            const quantity = document.createElement('strong');
            quantity.textContent = `${Number(it.cantidad) || 0}x `;
            name.append(quantity, document.createTextNode(it.plato_nombre || it.nombre || 'Plato'));

            const price = document.createElement('strong');
            price.style.color = '#059669';
            price.textContent = '$ ' + Number((Number(it.valor) || 0) * (Number(it.cantidad) || 0)).toLocaleString('es-CO');

            row.append(name, price);
            itemsList.appendChild(row);
        });
        if (!itemsList.childElementCount) {
            const empty = document.createElement('div');
            empty.style.cssText = 'color:#9ca3af;font-size:12px';
            empty.textContent = 'Sin ítems';
            itemsList.appendChild(empty);
        }

        const st = (order.estado || '').toLowerCase();
        const prepArea = document.getElementById('mPrepTimeArea');
        const actionArea = document.getElementById('mActionArea');
        const actionUrl = '{{ route("business.orders.status", ":id") }}'.replace(':id', order.id_pedido);

        // 1. Renderizar sección de tiempo de preparación
        if (st !== 'entregado' && st !== 'cancelado') {
            const currentPrep = order.tiempo_preparacion ? Number(order.tiempo_preparacion) : null;
            const isPrep = (st === 'preparado');

            prepArea.innerHTML = `
                <div style="background:#fff7ed;border:1.5px solid #fed7aa;border-radius:14px;padding:14px">
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px">
                        <div style="font-size:13px;font-weight:800;color:#c2410c">⏱️ Tiempo Estimado de Preparación:</div>
                        ${currentPrep ? `<span style="background:#ea580c;color:#fff;font-size:11px;font-weight:800;padding:2px 8px;border-radius:12px">${currentPrep} min fijados</span>` : ''}
                    </div>
                    
                    <form method="post" action="${actionUrl}" style="display:flex;flex-direction:column;gap:8px">
                        <input type="hidden" name="_token" value="${csrfToken}">
                        <input type="hidden" name="action" value="set_prep_time">
                        
                        <div style="display:flex;gap:6px;flex-wrap:wrap">
                            <button type="button" class="btn btn-secondary btn-sm" onclick="setPrepMinutes(10)" style="font-size:12px;padding:4px 8px">10 min</button>
                            <button type="button" class="btn btn-secondary btn-sm" onclick="setPrepMinutes(15)" style="font-size:12px;padding:4px 8px">15 min</button>
                            <button type="button" class="btn btn-secondary btn-sm" onclick="setPrepMinutes(20)" style="font-size:12px;padding:4px 8px">20 min</button>
                            <button type="button" class="btn btn-secondary btn-sm" onclick="setPrepMinutes(30)" style="font-size:12px;padding:4px 8px">30 min</button>
                            <button type="button" class="btn btn-secondary btn-sm" onclick="setPrepMinutes(45)" style="font-size:12px;padding:4px 8px">45 min</button>
                        </div>

                        <div style="display:flex;gap:8px;margin-top:4px">
                            <input type="number" id="inputMinutosPrep" name="minutos" min="1" max="180" value="${currentPrep || 15}" required
                                   placeholder="Minutos" class="form-control"
                                   style="width:90px;text-align:center;font-weight:800;font-size:15px;border:1.5px solid #fb923c;border-radius:8px">
                            <button type="submit" class="btn" style="flex:1;background:#ea580c;color:#fff;font-weight:800;font-size:13px;border-radius:8px;padding:8px">
                                ⏱️ Definir Tiempo y Notificar al Repartidor
                            </button>
                        </div>
                    </form>

                    <!-- Botón para marcar como preparado cuando el plato esté listo -->
                    <div style="margin-top:10px;padding-top:10px;border-top:1px dashed #fed7aa">
                        ${isPrep ? `
                            <div style="background:#ecfdf5;border:1px solid #a7f3d0;color:#047857;padding:8px 12px;border-radius:8px;font-weight:800;font-size:13px;text-align:center">
                                🧑‍🍳 ¡Plato marcado como PREPARADO y listo para recoger!
                            </div>
                        ` : `
                            <form method="post" action="${actionUrl}">
                                <input type="hidden" name="_token" value="${csrfToken}">
                                <input type="hidden" name="action" value="mark_prepared">
                                <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;text-align:center;display:flex;align-items:center;font-weight:800;padding:10px;font-size:14px;border-radius:8px">
                                    🧑‍🍳 Marcar Plato como LISTO / PREPARADO (Notificar)
                                </button>
                            </form>
                        `}
                    </div>

                </div>`;
        } else {
            prepArea.innerHTML = '';
        }

        // 2. Renderizar sección de entrega o estado final
        if (st === 'entregado') {
            actionArea.innerHTML = `
                <div style="background:#ecfdf5;border:1px solid #a7f3d0;color:#059669;padding:12px;border-radius:10px;font-weight:800;text-align:center">
                    ✅ Pedido Entregado al Cliente
                </div>`;
        } else if (st === 'cancelado') {
            actionArea.innerHTML = `
                <div style="background:#fef2f2;border:1px solid #fecaca;color:#dc2626;padding:12px;border-radius:10px;font-weight:800;text-align:center">
                    ❌ Pedido Cancelado
                </div>`;
        } else if (st === 'recogido en negocio' || st === 'en camino') {
            actionArea.innerHTML = `
                <div style="background:#eff6ff;border:1px solid #bfdbfe;color:#1e40af;padding:12px;border-radius:10px;font-weight:800;text-align:center">
                    🛵 Pedido recogido — el repartidor está en camino al cliente
                </div>`;
        } else if (st !== 'preparado') {
            actionArea.innerHTML = `
                <div style="background:#f8fafc;border:1px solid #e2e8f0;color:#475569;padding:12px;border-radius:10px;font-weight:700;text-align:center">
                    Marca el pedido como preparado para habilitar la validación del código de recogida.
                </div>`;
        } else {
            // Para Negocio y Cocina: Sección de validar código de recogida del repartidor
            actionArea.innerHTML = `
                <div style="background:#ecfdf5;border:1.5px solid #a7f3d0;border-radius:12px;padding:14px">
                    <div style="font-size:13px;color:#047857;font-weight:800;margin-bottom:8px">
                        🛵 Ingresa el Código proporcionado por el Repartidor:
                    </div>
                    <form method="post" action="${actionUrl}" style="display:flex;flex-direction:column;gap:10px">
                        <input type="hidden" name="_token" value="${csrfToken}">
                        <input type="hidden" name="action" value="deliver_with_code">
                        <input type="text" name="codigo_repartidor" class="form-control" placeholder="Código repartidor" required maxlength="10"
                               style="font-weight:900;letter-spacing:3px;font-size:18px;text-align:center;padding:10px;border:2px solid #059669;border-radius:10px">
                        <button type="submit" class="btn btn-primary" style="font-weight:800;padding:12px;font-size:15px;width:100%;justify-content:center;text-align:center;display:flex;align-items:center">
                            🛵 Validar Código y Entregar al Repartidor
                        </button>
                    </form>
                </div>`;
        }

        document.getElementById('orderModal').style.display = 'flex';
    }

    function closeOrderModal() {
        document.getElementById('orderModal').style.display = 'none';
    }

    function filterOrders(q) {
        const term = q.toLowerCase();
        document.querySelectorAll('.order-dish-card').forEach(card => {
            card.style.display = card.dataset.search.includes(term) ? '' : 'none';
        });
    }
    </script>
</x-business-layout>
