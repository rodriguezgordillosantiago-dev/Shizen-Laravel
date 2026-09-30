@extends('layouts.app')
@section('content')
<main class="scroll">
  <h1 style="margin: 0 0 4px; font-size: 20px">Pedidos activos</h1>
  <p style="margin: 0 0 18px; color: #9ca3af; font-size: 12px">
    Gestiona las entregas en curso
  </p>
  <div id="orders"></div>
</main>
@endsection

@push('scripts')
<script>
  function fmt(n) { return '$' + Number(n).toLocaleString('es-CO'); }

  async function render() {
    try {
      const result = await cargarEntregas('active');
      const list = result.items || [];
      const ba = document.getElementById("badge-activos");
      if (ba) {
        ba.textContent = list.length > 9 ? "9+" : list.length;
        ba.style.display = list.length > 0 ? "flex" : "none";
      }
      document.querySelector('#orders').innerHTML = list.length
        ? list.map(p => {
          const moving = p.entrega_estado === 'En camino';
          const codigoR = escapeHtml(p.codigo_recogida || '');
          const id = Number(p.id_entrega);
          let card = `<article class="card order">
            <div class="order-head">
              <div class="order-main">
                <p class="name">${escapeHtml(p.cliente_nombre || 'Cliente')} ${escapeHtml(p.cliente_apellido || '')}</p>
                <p class="muted" style="color:${moving?'#f97316':'#4c9540'};font-weight:700">
                  ${moving ? '🛵 En camino al destino' : '⏳ Pedido aceptado'}
                </p>
              </div>
              <div class="earn"><strong>${fmt(p.total)}</strong></div>
            </div>
            <div class="route">📍 Recoger: ${escapeHtml(p.negocio_nombre || 'Restaurante')}<br>🏁 Entregar: ${escapeHtml(p.direccion_entrega || 'Dirección cliente')}</div>`;

          if (!moving) {
            const recogido = p.entrega_estado === 'Recogido en negocio';
            card += `
            ${
              p.pedido_estado === 'Preparado'
                ? `<div style="background:#ecfdf5;border:1px solid #a7f3d0;border-radius:8px;padding:8px 12px;margin:8px 0;font-size:12px;color:#047857;font-weight:700;display:flex;align-items:center;gap:6px">
                    🧑‍🍳 ¡El plato ya está PREPARADO por el restaurante!
                   </div>`
                : p.tiempo_preparacion
                  ? `<div style="background:#fff7ed;border:1px solid #fed7aa;border-radius:8px;padding:8px 12px;margin:8px 0;font-size:12px;color:#c2410c;font-weight:700;display:flex;align-items:center;gap:6px">
                      ⏱️ Tiempo prep: ~${p.minutos_restantes !== null && p.minutos_restantes > 0 ? p.minutos_restantes + ' min restantes' : p.tiempo_preparacion + ' min'}
                     </div>`
                  : ''
            }
            <div style="background:#eff6ff;border:1px solid #bfdbfe;border-radius:8px;padding:10px 14px;margin:10px 0;font-size:13px">
              <div style="font-weight:700;color:#1e40af;margin-bottom:4px">🔑 Tu código para el negocio:</div>
              <div style="font-size:22px;font-weight:900;letter-spacing:4px;color:#1d4ed8;text-align:center">${codigoR}</div>
              <div style="font-size:11px;color:#6b7280;margin-top:4px;text-align:center">Muéstraselo al negocio/cocina para que confirmen la recogida</div>
            </div>
            ${recogido
              ? ''
              : `<div style="background:#fef9c3;border:1px solid #fde047;border-radius:8px;padding:8px 12px;margin:8px 0;font-size:12px;color:#854d0e;text-align:center">
                  ⏳ Esperando que el negocio valide tu código antes de poder recoger
                 </div>`}
            <div class="actions">
              <button class="btn-green" onclick="pickUp('${id}')"
                ${recogido ? '' : 'disabled style="opacity:0.55;cursor:not-allowed;background:#94a3b8"'}>
                🛵 Recogí el pedido
              </button>
            </div>`;
          } else {
            card += `
            <div style="background:#ecfdf5;border:1.5px solid #a7f3d0;border-radius:12px;padding:14px;margin:10px 0;font-size:13px">
              <div style="font-weight:800;color:#047857;margin-bottom:8px">✅ Ingresa el código que te dio el cliente:</div>
              <div style="display:flex;flex-direction:column;gap:10px;width:100%">
                <input id="codigo-c-${id}" type="text" inputmode="numeric" maxlength="6" required pattern="[0-9]{6}" title="Ingresa un código de 6 dígitos."
                       style="width:100%;padding:10px;border:2px solid #059669;border-radius:10px;font-size:18px;font-weight:900;letter-spacing:3px;text-align:center;min-height:48px"
                       placeholder="Código cliente">
                <button class="btn-green" style="width:100%;min-height:48px;font-weight:800;font-size:15px;white-space:nowrap;border-radius:10px" onclick="deliverWithClientCode('${id}')">🏁 Confirmar entrega</button>
              </div>
              <div style="font-size:11px;color:#6b7280;margin-top:6px">Ingresa el código de 6 dígitos que te proporcione el cliente al momento de la entrega.</div>
            </div>`;
          }

          card += `</article>`;
          return card;
        }).join('')
      : `<div class="card" style="padding:32px;text-align:center;color:#9ca3af;font-size:13px">No tienes pedidos activos</div>`;
    } catch (err) {
      console.error('Error al renderizar pedidos activos:', err);
      document.querySelector('#orders').innerHTML = `<div class="card" style="padding:32px;text-align:center;color:#ef4444;font-size:13px">Error al cargar pedidos activos. Por favor recarga la página.</div>`;
    }
  }

  async function pickUp(id) {
    try {
      await actualizarEntrega('advance', id);
      await render();
      if (typeof updateNavBadges === 'function') updateNavBadges();
    } catch (e) { alert(e.message); }
  }

  async function deliverWithClientCode(id) {
    const input = document.getElementById('codigo-c-' + id);
    const codigo = (input ? input.value.trim() : '');
    if (!codigo) { alert('Ingresa el código que te dio el cliente.'); return; }
    if (!/^\d{6}$/.test(codigo)) { alert('El código del cliente debe tener exactamente 6 dígitos numéricos.'); return; }
    try {
      const body = new URLSearchParams({ action: 'deliver_client_code', id_entrega: String(id), codigo_cliente: codigo });
      const resp = await fetch(window.repartidorApiUrl || '/api/entregas', {
        method: 'POST',
        body,
        credentials: 'same-origin',
        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '', Accept: 'application/json' }
      });
      const result = await resp.json();
      if (!resp.ok || result.error) throw new Error(result.error || 'Error al finalizar.');
      alert('¡Entrega finalizada correctamente!');
      await render();
      if (typeof updateNavBadges === 'function') updateNavBadges();
    } catch (e) { alert(e.message); }
  }

  document.addEventListener('DOMContentLoaded', function() {
    render();
    if (typeof iniciarBotonEstadoRepartidor === 'function') iniciarBotonEstadoRepartidor();
    if (typeof updateNavBadges === 'function') updateNavBadges();
  });
</script>
@endpush
