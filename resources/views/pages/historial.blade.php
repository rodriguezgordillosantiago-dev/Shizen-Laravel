@extends('layouts.app')
@section('content')
<div class="scroll">
  <div style="padding: 40px 16px 0">
    <h1 style="margin: 0 0 4px; font-size: 18px; font-weight: 800; color: #1a1a1a;">Historial</h1>
    <p style="margin: 0 0 16px; font-size: 12px; color: #9ca3af;">Tus últimas entregas</p>
    <div id="list"></div>
  </div>
</div>
@endsection

@push('scripts')
<script>
  function fmt(n) { return '$' + Number(n || 0).toLocaleString('es-CO'); }

  async function render() {
    try {
      const result = await cargarEntregas("history");
      const historial = result.items || [];
      const list = document.getElementById("list");

      if (historial.length === 0) {
        list.innerHTML = `
          <div style="display:flex;flex-direction:column;align-items:center;justify-content:center;padding:60px 0;text-align:center">
            <svg width="56" height="56" fill="none" viewBox="0 0 24 24" stroke="#e5e7eb" stroke-width="1.5" style="margin-bottom:12px">
              <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            <p style="margin:0 0 4px;font-size:14px;font-weight:700;color:#9ca3af">Sin historial aún</p>
          </div>`;
        return;
      }

      list.innerHTML = historial
        .map((p) => {
          const ok = p.entrega_estado === "Entregado";
          return `
            <div style="display:flex;align-items:center;gap:12px;padding:14px 0;border-bottom:1px solid #f3f4f6">
              <div style="width:44px;height:44px;border-radius:50%;background:#e8f5e9;color:#2d6a31;display:flex;align-items:center;justify-content:center;font-weight:800;flex-shrink:0">
                ${(p.cliente_nombre || "?").slice(0, 1)}
              </div>
              <div style="flex:1;min-width:0">
                <div style="display:flex;align-items:center;gap:6px;margin-bottom:2px">
                  <p style="margin:0;font-size:13px;font-weight:700;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">
                    ${escapeHtml(p.cliente_nombre || '')} ${escapeHtml(p.cliente_apellido || '')}
                  </p>
                  <span style="flex-shrink:0;font-size:10px;font-weight:700;padding:2px 8px;border-radius:999px;
                    background:${ok ? "#f0fdf4" : "#fef2f2"};color:${ok ? "#166534" : "#dc2626"}">
                    ${ok ? "Entregado" : "Cancelado"}
                  </span>
                </div>
                <p style="margin:0 0 1px;font-size:11px;color:#9ca3af;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">
                  Pedido #${Number(p.id_pedido)} · ${escapeHtml(p.negocio_nombre || 'Restaurante')}
                </p>
                <p style="margin:0;font-size:11px;color:#d1d5db">${escapeHtml(p.fecha_entrega || "")}</p>
              </div>
              <div style="text-align:right;flex-shrink:0">
                <p style="margin:0 0 1px;font-size:14px;font-weight:800;color:${ok ? "#4c9540" : "#d1d5db"};${!ok ? "text-decoration:line-through" : ""}">
                  ${fmt(p.total)}
                </p>
              </div>
            </div>`;
        })
        .join("");
    } catch (e) {
      console.error(e);
      document.getElementById("list").innerHTML = `<div class="card" style="padding:32px;text-align:center;color:#ef4444;font-size:13px">Error al cargar el historial.</div>`;
    }
  }

  document.addEventListener('DOMContentLoaded', function() {
    render();
    if (typeof iniciarBotonEstadoRepartidor === 'function') iniciarBotonEstadoRepartidor();
    if (typeof updateNavBadges === 'function') updateNavBadges();
  });
</script>
@endpush
