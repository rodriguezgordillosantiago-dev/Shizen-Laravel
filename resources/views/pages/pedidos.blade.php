@extends('layouts.app')
@section('content')
<main class="scroll">
  <h1 style="margin: 0 0 4px; font-size: 20px">Pedidos disponibles</h1>
  <p style="margin: 0 0 18px; color: #9ca3af; font-size: 12px">Acepta los pedidos que quieras entregar</p>
  <div id="orders"></div>
</main>
@endsection

@push('scripts')
<script>
  function fmt(n) { return '$' + Number(n || 0).toLocaleString('es-CO'); }

  async function render() {
    try {
      const [availableResult, activeResult] = await Promise.all([
        cargarEntregas("available"),
        cargarEntregas("active")
      ]);
      const list = availableResult.items || [];
      const activeCount = (activeResult.items || []).length;
      const full = activeCount >= 4;

      document.querySelector("#orders").innerHTML = list.length
        ? list.map(p => `
          <article class="card order">
            <div class="order-head">
              <div class="order-main">
                <p class="name">${escapeHtml(p.cliente_nombre || 'Cliente')} ${escapeHtml(p.cliente_apellido || '')}</p>
                <p class="muted">Pedido #${Number(p.id_pedido)}</p>
              </div>
              <div class="earn"><strong>${fmt(p.total)}</strong></div>
            </div>
            <div class="route">📍 Recoger: ${escapeHtml(p.negocio_nombre || 'Restaurante')}<br>🏁 Entregar: ${escapeHtml(p.direccion_entrega || 'Dirección')}</div>
            ${
              p.pedido_estado === "Preparado"
                ? `<div style="background:#ecfdf5;border:1px solid #a7f3d0;color:#047857;padding:6px 10px;border-radius:8px;font-size:12px;font-weight:800;margin:8px 0">
                    🧑‍🍳 ¡Plato PREPARADO y listo para recoger!
                   </div>`
                : p.tiempo_preparacion
                  ? `<div style="background:#fff7ed;border:1px solid #fed7aa;color:#c2410c;padding:6px 10px;border-radius:8px;font-size:12px;font-weight:800;margin:8px 0">
                      ⏱️ Tiempo prep: ~${p.minutos_restantes !== null && p.minutos_restantes > 0 ? p.minutos_restantes + " min restantes" : p.tiempo_preparacion + " min"}
                     </div>`
                  : ""
            }
            <div class="actions">
              <button class="btn-green" ${full ? 'disabled style="opacity:.55;cursor:not-allowed"' : ""} onclick="accept('${Number(p.id_entrega)}')">
                ${full ? 'Cupo lleno (máx 4)' : 'Aceptar pedido'}
              </button>
            </div>
          </article>`
        ).join("")
        : `<div class="card" style="padding:32px;text-align:center;color:#9ca3af;font-size:13px">No hay pedidos disponibles ahora</div>`;
    } catch (error) {
      console.error(error);
      document.querySelector("#orders").innerHTML = `<div class="card" style="padding:32px;text-align:center;color:#ef4444;font-size:13px">Error al cargar pedidos.</div>`;
    }
  }

  async function accept(id) {
    try {
      await actualizarEntrega("accept", id);
      window.location.href = "{{ route('activos') }}";
    } catch (error) {
      alert(error.message);
    }
  }

  document.addEventListener('DOMContentLoaded', function() {
    render();
    if (typeof iniciarBotonEstadoRepartidor === 'function') iniciarBotonEstadoRepartidor();
    if (typeof updateNavBadges === 'function') updateNavBadges();
  });
</script>
@endpush
