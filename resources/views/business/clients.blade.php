<x-business-layout title="Clientes">
    <div class="page-header">
        <div>
            <h1 class="page-header-title">Clientes del Negocio</h1>
            <p class="page-header-sub">{{ $clients->count() }} clientes que han comprado en tu negocio</p>
        </div>
        <div class="search-wrap" style="max-width:280px">
            <i class="bx bx-search"></i>
            <input type="text" class="search-input" placeholder="Buscar cliente..." oninput="filterClients(this.value)"><script>(function(c){if(!c||c.dataset.validacionCampo)return;c.dataset.validacionCampo='1';var e=c.nextElementSibling&&c.nextElementSibling.classList.contains('field-error-inline')?c.nextElementSibling:document.createElement('small');e.className='field-error-inline';e.style.cssText='display:block;color:#c62828;font-size:12px;margin-top:4px;min-height:1em;';if(!e.parentNode)c.insertAdjacentElement('afterend',e);function v(){var x=c.required&&!c.value.trim(),i=x||!c.validity.valid;e.textContent=i?(x?'Este campo es obligatorio.':c.validationMessage):'';c.setAttribute('aria-invalid',i?'true':'false');return!i}['input','change','blur'].forEach(function(t){c.addEventListener(t,v)});if(c.form)c.form.addEventListener('submit',function(a){if(!v())a.preventDefault()})})(document.currentScript.previousElementSibling);</script>
        </div>
    </div>

    <div class="card">
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Cliente</th>
                        <th>Correo electrónico</th>
                        <th>Dirección de Entrega</th>
                        <th>Pedidos Realizados</th>
                        <th>Total Gastado (COP)</th>
                        <th>Última Compra</th>
                    </tr>
                </thead>
                <tbody id="clientsBody">
                @forelse ($clients as $client)
                    @php
                        $nombreCompleto = trim($client->nombre . ' ' . $client->apellido);
                        $initial = strtoupper(substr($client->nombre ?? 'C', 0, 1));
                        $fechaUltima = !empty($client->ultima_compra)
                            ? \Carbon\Carbon::parse($client->ultima_compra)->format('d/m/Y H:i')
                            : (!empty($client->fecha_registro) ? \Carbon\Carbon::parse($client->fecha_registro)->format('d/m/Y') : 'Sin fecha');
                    @endphp
                    <tr data-name="{{ strtolower(htmlspecialchars($nombreCompleto . ' ' . $client->email, ENT_QUOTES, 'UTF-8')) }}">
                        <td>
                            <div style="display:flex;align-items:center;gap:10px">
                                <div style="width:38px;height:38px;border-radius:50%;background:#d1fae5;color:#047857;display:flex;align-items:center;justify-content:center;font-weight:800;font-size:15px;flex-shrink:0">
                                    {{ $initial }}
                                </div>
                                <div style="font-weight:700;color:#111827">
                                    {{ $nombreCompleto }}
                                </div>
                            </div>
                        </td>
                        <td style="font-size:13px;color:#374151">{{ $client->email }}</td>
                        <td style="font-size:13px;color:#4b5563">{{ $client->direccion ?? ($client->localidad ?? 'Dirección en pedido') }}</td>
                        <td><strong style="font-size:14px;color:#111827">{{ $client->total_pedidos }}</strong></td>
                        <td><strong style="color:#059669;font-size:14px">$ {{ number_format($client->total_gastado, 0, ',', '.') }}</strong></td>
                        <td style="color:#6b7280;font-size:12px">{{ $fechaUltima }}</td>
                    </tr>
                @empty
                    <tr><td colspan="6" style="text-align:center;color:#9ca3af;padding:28px">No hay clientes que hayan comprado en tu negocio aún.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <script>
    function filterClients(q) {
        const term = q.toLowerCase();
        document.querySelectorAll('#clientsBody tr[data-name]').forEach(row => {
            row.style.display = row.dataset.name.includes(term) ? '' : 'none';
        });
    }
    </script>
</x-business-layout>
