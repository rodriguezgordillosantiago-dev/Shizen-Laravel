@extends('layouts.app')
@section('content')
<div class="scroll" id="content">
  <!-- Header filled by JS -->
</div>
@endsection

@push('scripts')
<script>
  function fmt(n) { return '$' + Number(n || 0).toLocaleString('es-CO'); }

  async function render() {
    const perfil = {
      nombre: "",
      apellido: "",
      entregasHoy: 0,
      gananciasHoy: 0,
      calificacion: 0,
    };
    try {
      Object.assign(perfil, await cargarEstadisticas());
    } catch (error) {
      console.error(error);
    }

    let activos = [];
    let disponibles = [];
    try {
      const [activeResult, availableResult] = await Promise.all([
        cargarEntregas("active"),
        cargarEntregas("available")
      ]);
      activos = (activeResult && activeResult.items) || [];
      disponibles = (availableResult && availableResult.items) || [];
    } catch (e) {
      console.error(e);
    }

    const ca = activos.length;
    const lleno = ca >= 4;

    document.getElementById("content").innerHTML = `
      <!-- Green header -->
      <div style="background:linear-gradient(135deg,#4c9540,#2d6b22);padding:40px 20px 24px">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px">
          <div>
            <p style="margin:0 0 2px;font-size:13px;color:rgba(255,255,255,.75);font-weight:600">Bienvenido de vuelta</p>
            <h1 style="margin:0;font-size:22px;font-weight:800;color:white">${escapeHtml(perfil.nombre)} ${escapeHtml(perfil.apellido)}</h1>
          </div>
          <img src="{{ asset('assets/logo_blanco.png') }}" alt="Shizen"
            style="height:64px;width:128px;object-fit:contain;filter:brightness(0) invert(1)">
        </div>
        <!-- Stats row -->
        <div style="display:flex;gap:10px">
          <div style="flex:1;background:white;border-radius:14px;padding:10px;text-align:center">
            <p style="margin:0 0 2px;font-size:10px;color:#9ca3af;font-weight:600">Entregas hoy</p>
            <p style="margin:0;font-size:14px;font-weight:800;color:#4c9540">${perfil.entregasHoy}</p>
          </div>
          <div style="flex:1;background:white;border-radius:14px;padding:10px;text-align:center">
            <p style="margin:0 0 2px;font-size:10px;color:#9ca3af;font-weight:600">Ganancias</p>
            <p style="margin:0;font-size:14px;font-weight:800;color:#f97316">${fmt(perfil.gananciasHoy)}</p>
          </div>
          <div style="flex:1;background:white;border-radius:14px;padding:10px;text-align:center">
            <p style="margin:0 0 2px;font-size:10px;color:#9ca3af;font-weight:600">Calificación</p>
            <p style="margin:0;font-size:14px;font-weight:800;color:#4c9540">⭐ ${perfil.calificacion}</p>
          </div>
        </div>
      </div>

      <!-- Cupo banner -->
      <div style="margin:16px 16px 0">
        <div style="border-radius:14px;padding:12px 14px;display:flex;align-items:center;gap:10px;
          background:${lleno ? "#fef2f2" : "#f0fdf4"};border:1px solid ${lleno ? "#fecaca" : "#bbf7d0"}">
          <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="${lleno ? "#ef4444" : "#4c9540"}" stroke-width="2" style="flex-shrink:0">
            <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
          </svg>
          <div>
            <p style="margin:0 0 2px;font-size:13px;font-weight:700;color:${lleno ? "#dc2626" : "#166534"}">
              ${lleno ? "Cupo lleno — máx. 4 pedidos" : `Pedidos activos: ${ca} / 4`}
            </p>
            <p style="margin:0;font-size:11px;color:#6b7280">
              ${lleno ? "Entrega los actuales para aceptar más" : `Puedes aceptar ${4 - ca} más`}
            </p>
          </div>
        </div>
      </div>

      <!-- Available orders preview -->
      <div style="padding:20px 16px 0">
        <h2 style="margin:0 0 12px;font-size:15px;font-weight:800;color:#1a1a1a">Nuevos pedidos</h2>
        ${
          disponibles.length === 0
            ? `
          <div style="background:white;border-radius:16px;border:1px solid rgba(0,0,0,.07);padding:24px;text-align:center">
            <p style="margin:0;color:#9ca3af;font-size:13px">No hay pedidos disponibles ahora</p>
          </div>`
            : disponibles
                .slice(0, 2)
                .map(
                  (p) => `
          <article class="new-order-card">
            <div class="new-order-card__top">
              <div class="new-order-card__summary">
                <div class="new-order-card__business">
                  <span class="new-order-card__badge">NUEVO PEDIDO</span>
                  <span class="new-order-card__eyebrow">Recoger en</span>
                  <h3>${escapeHtml(p.negocio_nombre || "Negocio sin nombre")}</h3>
                </div>
                <div class="new-order-card__amount">
                  <strong>${fmt(p.total)}</strong>
                  <span>Pedido #${Number(p.id_pedido)}</span>
                </div>
              </div>
            </div>
            <div class="new-order-card__dishes">
              <span class="new-order-card__label">Platos por recoger</span>
              <ul>
                ${
                  Array.isArray(p.detalles) && p.detalles.length
                    ? p.detalles
                        .map(
                          (detalle) =>
                            `<li><strong>${Number(detalle.cantidad)}×</strong> ${escapeHtml(detalle.nombre)}</li>`,
                        )
                        .join("")
                    : "<li>Detalle no disponible</li>"
                }
              </ul>
            </div>
            ${
              p.pedido_estado === "Preparado"
                ? `<div style="background:#ecfdf5;border:1px solid #a7f3d0;color:#047857;padding:6px 10px;border-radius:8px;font-size:12px;font-weight:800;margin:6px 0;display:flex;align-items:center;gap:6px">
                    🧑‍🍳 ¡Plato PREPARADO y listo para recoger!
                   </div>`
                : p.tiempo_preparacion
                  ? `<div style="background:#fff7ed;border:1px solid #fed7aa;color:#c2410c;padding:6px 10px;border-radius:8px;font-size:12px;font-weight:800;margin:6px 0;display:flex;align-items:center;gap:6px">
                      ⏱️ Tiempo prep: ~${p.minutos_restantes !== null && p.minutos_restantes > 0 ? p.minutos_restantes + " min restantes" : p.tiempo_preparacion + " min"}
                     </div>`
                  : ""
            }
            <div class="new-order-card__details">
              <div class="new-order-card__detail">
                <span class="new-order-card__detail-icon" aria-hidden="true">⌖</span>
                <div>
                  <span class="new-order-card__label">Entregar a</span>
                  <strong>${escapeHtml(p.cliente_nombre || "")} ${escapeHtml(p.cliente_apellido || "")}</strong>
                </div>
              </div>
              <div class="new-order-card__detail">
                <span class="new-order-card__detail-icon" aria-hidden="true">↗</span>
                <div>
                  <span class="new-order-card__label">Dirección de entrega</span>
                  <strong>${escapeHtml(p.direccion_entrega || "Dirección por confirmar")}</strong>
                </div>
              </div>
            </div>
            <button class="new-order-card__accept btn-green" type="button"
              ${lleno ? "disabled style='opacity:.55;cursor:not-allowed'" : ""} onclick="aceptar('${Number(p.id_entrega)}')">
              ${lleno ? "Cupo lleno" : "Aceptar pedido"}
              <span aria-hidden="true">→</span>
            </button>
          </article>`,
                )
                .join("")
        }
      </div>
    `;
  }

  async function aceptar(id) {
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
