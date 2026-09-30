@extends('layouts.app')
@section('content')
<main class="scroll">
  @if(session('status'))
    <div style="margin:16px 16px 0;padding:12px 16px;background:#f0fdf4;border:1px solid #bbf7d0;border-radius:10px;color:#166534;font-size:14px;font-weight:600">
      {{ session('status') }}
    </div>
  @endif

  <!-- Encabezado verde -->
  <div class="green-header" style="background:linear-gradient(135deg,#f0faf0,#ffffff);border-bottom:1px solid #c8e6c9;padding:38px 24px 28px;position:relative;text-align:center">
    <img src="{{ asset('assets/logo_color.png') }}" alt="Shizen" style="position:absolute;top:30px;right:24px;height:32px;object-fit:contain;opacity:0.9">
    <div class="profile-avatar" style="display:flex;flex-direction:column;align-items:center">
      <div style="width:88px;height:88px;background:white;border-radius:50%;display:flex;align-items:center;justify-content:center;margin-bottom:12px;box-shadow:0 4px 16px rgba(0,0,0,0.15);font-size:32px;font-weight:bold;color:#4c9540;overflow:hidden">
        @if(!empty($repartidor['foto_url']))
          <img src="{{ str_starts_with($repartidor['foto_url'], 'http') ? $repartidor['foto_url'] : asset($repartidor['foto_url']) }}" alt="Foto" style="width:100%;height:100%;object-fit:cover">
        @else
          {{ strtoupper(substr($repartidor['nombre'] ?? 'S', 0, 1) . substr($repartidor['apellido'] ?? 'V', 0, 1)) }}
        @endif
      </div>
      <h1 style="color:#1b3a1d;font-size:22px;font-weight:700;margin:0 0 2px 0">
        {{ $repartidor['nombre'] ?? '' }} {{ $repartidor['apellido'] ?? '' }}
      </h1>
      <p style="color:#555;font-size:13px;margin:0">
        {{ $repartidor['email'] ?? '' }} · {{ $repartidor['vehiculo'] ?? '' }}
      </p>
      <div style="margin-top:12px;display:inline-flex;align-items:center;background:#e8f5e9;padding:4px 12px;border-radius:16px">
        <span style="width:8px;height:8px;background:#5cb85c;border-radius:50%;margin-right:6px"></span>
        <span style="color:#2d6a31;font-size:12px;font-weight:500">En línea</span>
      </div>
    </div>
  </div>

  <!-- Información personal -->
  <div style="padding:20px 16px 0">
    <div class="card-container" style="background:white;border-radius:12px;border:1px solid #e8f5e9;overflow:hidden;position:relative">
      <div style="padding:14px 16px;border-bottom:1px solid #f3f4f6;display:flex;align-items:center;justify-content:space-between">
        <h2 style="font-size:17px;font-weight:700;margin:0">Datos del Repartidor</h2>
        <button type="button" id="btn-toggle-edit" onclick="toggleEdicionPerfil()" style="display:inline-flex;align-items:center;gap:6px;background:#f0fdf4;border:1px solid #bbf7d0;color:#166534;padding:6px 12px;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;font-family:'Inter',sans-serif">
          <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
          </svg>
          <span id="btn-toggle-edit-text">Editar</span>
        </button>
      </div>

      <form id="form-perfil" action="{{ route('perfil.update') }}" method="post" style="padding:16px">
        @csrf
        <label class="field-group" style="display:block;margin-bottom:12px">
          <small style="display:block;margin-bottom:4px;color:#6b7280;font-size:13px">Nombre</small>
          <input id="campo-nombre" class="field" type="text" name="nombre" value="{{ $repartidor['nombre'] ?? '' }}" required readonly style="width:100%;padding:10px;border:1px solid #e5e7eb;border-radius:8px;background:#f9fafb;color:#374151;font-family:'Inter',sans-serif;font-size:15px;transition:all 0.2s">
          <script>
            (function () {
              const field = document.currentScript.previousElementSibling;
              const message = document.createElement('small');
              message.className = 'field-error-inline';
              message.style.cssText = 'display:block;color:#dc2626;font-size:12px;margin-top:6px';
              field.insertAdjacentElement('afterend', message);
              function validate() {
                const valid = /^[A-Za-zÁÉÍÓÚÜÑáéíóúüñ .'-]{2,}$/.test(field.value.trim());
                field.setCustomValidity(valid ? '' : 'Ingresa un nombre válido.');
                message.textContent = field.dataset.touched === 'true' && !valid ? 'Ingresa un nombre válido.' : '';
              }
              field.addEventListener('input', function () { field.dataset.touched = 'true'; validate(); });
              field.addEventListener('blur', function () { field.dataset.touched = 'true'; validate(); });
              field.form.addEventListener('submit', function () { field.dataset.touched = 'true'; validate(); });
            })();
          </script>
        </label>
        <label class="field-group" style="display:block;margin-bottom:12px">
          <small style="display:block;margin-bottom:4px;color:#6b7280;font-size:13px">Apellido</small>
          <input id="campo-apellido" class="field" type="text" name="apellido" value="{{ $repartidor['apellido'] ?? '' }}" required readonly style="width:100%;padding:10px;border:1px solid #e5e7eb;border-radius:8px;background:#f9fafb;color:#374151;font-family:'Inter',sans-serif;font-size:15px;transition:all 0.2s">
          <script>
            (function () {
              const field = document.currentScript.previousElementSibling;
              const message = document.createElement('small');
              message.className = 'field-error-inline';
              message.style.cssText = 'display:block;color:#dc2626;font-size:12px;margin-top:6px';
              field.insertAdjacentElement('afterend', message);
              function validate() {
                const valid = /^[A-Za-zÁÉÍÓÚÜÑáéíóúüñ .'-]{2,}$/.test(field.value.trim());
                field.setCustomValidity(valid ? '' : 'Ingresa un apellido válido.');
                message.textContent = field.dataset.touched === 'true' && !valid ? 'Ingresa un apellido válido.' : '';
              }
              field.addEventListener('input', function () { field.dataset.touched = 'true'; validate(); });
              field.addEventListener('blur', function () { field.dataset.touched = 'true'; validate(); });
              field.form.addEventListener('submit', function () { field.dataset.touched = 'true'; validate(); });
            })();
          </script>
        </label>
        <label class="field-group" style="display:block;margin-bottom:12px">
          <small style="display:block;margin-bottom:4px;color:#6b7280;font-size:13px">Correo electrónico (no editable)</small>
          <input class="field" type="email" value="{{ $repartidor['email'] ?? '' }}" readonly style="width:100%;padding:10px;border:1px solid #e5e7eb;border-radius:8px;background:#f9fafb;color:#6b7280;font-family:'Inter',sans-serif;font-size:15px">
        </label>
        <input type="hidden" name="vehiculo" value="{{ $repartidor['vehiculo'] ?? 'Moto' }}">
        <button id="btn-guardar-perfil" type="submit" class="btn-green" style="display:none;align-items:center;justify-content:center;background:#4c9540;color:white;padding:12px;border-radius:10px;font-weight:700;cursor:pointer;width:100%;border:none;font-family:'Inter',sans-serif;font-size:15px;margin-top:8px">
          Guardar datos personales
        </button>
      </form>
    </div>
  </div>

  <!-- Caja: Vehículo y Documentos -->
  <div style="padding:16px 16px 0">
    <div class="card-container" style="background:white;border-radius:12px;border:1px solid #e8f5e9;overflow:hidden">
      <div style="padding:14px 16px;border-bottom:1px solid #f3f4f6;display:flex;align-items:center;justify-content:space-between">
        <h2 style="font-size:16px;font-weight:700;margin:0;display:flex;align-items:center;gap:8px">
          🛵 Vehículo y Documentos
        </h2>
        <span style="font-size:12px;color:#166534;background:#dcfce7;font-weight:700;padding:2px 8px;border-radius:99px">En regla</span>
      </div>

      <div style="padding:14px 16px">
        <div style="display:flex;justify-content:space-between;align-items:center;padding:10px 12px;background:#f8fafc;border-radius:8px;border:1px solid #e2e8f0;margin-bottom:10px">
          <div>
            <small style="display:block;color:#6b7280;font-size:11px;font-weight:600">Tipo de vehículo registrado</small>
            <span style="font-size:15px;font-weight:700;color:#0f172a">
              @php
                $v = $repartidor['vehiculo'] ?? '';
                $emoji = match(strtolower($v)) {
                  'moto' => '🏍️',
                  'bicicleta' => '🚲',
                  'carro' => '🚗',
                  'monopatín', 'monopatin' => '🛴',
                  default => '🛵'
                };
              @endphp
              {{ $emoji }} {{ $v }}
            </span>
          </div>
        </div>

        <!-- Documentos asociados -->
        <div style="display:flex;flex-direction:column;gap:6px">
          <div style="display:flex;justify-content:space-between;align-items:center;padding:8px 10px;background:#f8fafc;border-radius:6px;border:1px solid #f1f5f9;font-size:12px">
            <span style="color:#334155;font-weight:600">🪪 Licencia de conducción</span>
            <span style="color:#16a34a;font-weight:700">✓ Validada</span>
          </div>
          <div style="display:flex;justify-content:space-between;align-items:center;padding:8px 10px;background:#f8fafc;border-radius:6px;border:1px solid #f1f5f9;font-size:12px">
            <span style="color:#334155;font-weight:600">📄 SOAT</span>
            <span style="color:#16a34a;font-weight:700">✓ Vigente</span>
          </div>
          <div style="display:flex;justify-content:space-between;align-items:center;padding:8px 10px;background:#f8fafc;border-radius:6px;border:1px solid #f1f5f9;font-size:12px">
            <span style="color:#334155;font-weight:600">📋 Tarjeta de propiedad</span>
            <span style="color:#16a34a;font-weight:700">✓ Registrada</span>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Estadísticas y Opciones -->
  <div style="padding:16px 16px 0">
    <h2 style="font-size:16px;font-weight:700;margin-bottom:10px">Actividad</h2>
    <div class="card-container" style="background:white;border-radius:12px;border:1px solid #e8f5e9;overflow:hidden">
      <!-- Entregas hoy -->
      <div style="width:100%;display:flex;align-items:center;justify-content:space-between;padding:12px 16px;border-bottom:1px solid #f3f4f6">
        <div style="display:flex;align-items:center;gap:12px">
          <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="#6b7280" stroke-width="2">
            <path d="M5 8h14M5 8a2 2 0 1 0 0-4h14a2 2 0 1 0 0 4M5 8v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V8m-9 4h4"/>
          </svg>
          <p style="font-size:14px;font-weight:600;margin:0">Entregas hoy</p>
        </div>
        <span style="font-size:15px;font-weight:700;color:#4c9540" id="profile-entregas-count">{{ $entregasHoy ?? 0 }}</span>
      </div>

      <!-- Ganancias -->
      <div style="width:100%;display:flex;align-items:center;justify-content:space-between;padding:12px 16px;border-bottom:1px solid #f3f4f6">
        <div style="display:flex;align-items:center;gap:12px">
          <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="#6b7280" stroke-width="2">
            <circle cx="12" cy="12" r="10"/>
            <path d="M12 6v6l4 2"/>
          </svg>
          <p style="font-size:14px;font-weight:600;margin:0">Ganancias</p>
        </div>
        <span style="font-size:15px;font-weight:700;color:#4c9540" id="profile-ganancias-count">{{ $gananciasHoy ?? '$0' }}</span>
      </div>

      <!-- Configuración link -->
      <a href="{{ route('configuracion') }}" style="width:100%;display:flex;align-items:center;justify-content:space-between;padding:12px 16px;text-decoration:none;color:inherit;transition:background 0.15s">
        <div style="display:flex;align-items:center;gap:12px">
          <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="#6b7280" stroke-width="2">
            <circle cx="12" cy="12" r="3"/>
            <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/>
          </svg>
          <p style="font-size:14px;font-weight:600;margin:0">Configuración</p>
        </div>
        <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="#9ca3af" stroke-width="2">
          <polyline points="9,18 15,12 9,6"/>
        </svg>
      </a>
    </div>
  </div>

  <!-- Cerrar sesión -->
  <div style="padding:20px 16px 32px">
    <form action="{{ route('logout') }}" method="post">
      @csrf
      <button type="submit" style="display:flex;align-items:center;justify-content:center;gap:6px;background:#fff;border:1px solid #fee2e2;color:#ef4444;padding:12px;border-radius:10px;font-weight:700;cursor:pointer;width:100%;font-family:&#39;Inter&#39;,sans-serif;font-size:15px">
        <svg class="logout-icon" width="18" height="18" fill="currentColor" viewBox="0 0 512 512" aria-hidden="true"><path d="M377.9 105.9L500.7 228.7c7.2 7.2 11.3 17.1 11.3 27.3s-4.1 20.1-11.3 27.3L377.9 406.1c-6.4 6.4-15 9.9-24 9.9c-18.7 0-33.9-15.2-33.9-33.9l0-62.1-128 0c-17.7 0-32-14.3-32-32l0-64c0-17.7 14.3-32 32-32l128 0 0-62.1c0-18.7 15.2-33.9 33.9-33.9c9 0 17.6 3.6 24 9.9zM160 96L96 96c-17.7 0-32 14.3-32 32l0 256c0 17.7 14.3 32 32 32l64 0c17.7 0 32 14.3 32 32s-14.3 32-32 32l-64 0c-53 0-96-43-96-96L0 128C0 75 43 32 96 32l64 0c17.7 0 32 14.3 32 32s-14.3 32-32 32z"></path></svg> Cerrar sesión
      </button>
    </form>
  </div>
</main>
@endsection

@push('scripts')
<script>
let editando = false;
function toggleEdicionPerfil() {
  editando = !editando;
  const btnToggle = document.getElementById('btn-toggle-edit');
  const btnText = document.getElementById('btn-toggle-edit-text');
  const btnGuardar = document.getElementById('btn-guardar-perfil');
  const inputNombre = document.getElementById('campo-nombre');
  const inputApellido = document.getElementById('campo-apellido');

  if (!btnToggle || !inputNombre || !inputApellido) return;

  if (editando) {
    inputNombre.removeAttribute('readonly');
    inputApellido.removeAttribute('readonly');
    inputNombre.style.background = '#ffffff';
    inputApellido.style.background = '#ffffff';
    inputNombre.style.borderColor = '#4c9540';
    inputApellido.style.borderColor = '#4c9540';
    if (btnGuardar) btnGuardar.style.display = 'flex';
    if (btnText) btnText.textContent = 'Cancelar';
    btnToggle.style.background = '#fef2f2';
    btnToggle.style.borderColor = '#fecaca';
    btnToggle.style.color = '#dc2626';
    inputNombre.focus();
  } else {
    inputNombre.setAttribute('readonly', 'true');
    inputApellido.setAttribute('readonly', 'true');
    inputNombre.value = inputNombre.defaultValue;
    inputApellido.value = inputApellido.defaultValue;
    inputNombre.style.background = '#f9fafb';
    inputApellido.style.background = '#f9fafb';
    inputNombre.style.borderColor = '#e5e7eb';
    inputApellido.style.borderColor = '#e5e7eb';
    if (btnGuardar) btnGuardar.style.display = 'none';
    if (btnText) btnText.textContent = 'Editar';
    btnToggle.style.background = '#f0fdf4';
    btnToggle.style.borderColor = '#bbf7d0';
    btnToggle.style.color = '#166534';
  }
}

document.addEventListener('DOMContentLoaded', function() {
  if (typeof getPerfil === 'function') {
    const p = getPerfil();
    if (p) {
      if (document.getElementById('profile-entregas-count')) {
        document.getElementById('profile-entregas-count').textContent = p.entregasHoy || '6';
      }
      if (document.getElementById('profile-ganancias-count') && typeof fmt === 'function') {
        document.getElementById('profile-ganancias-count').textContent = fmt(p.gananciasHoy || 25800);
      }
    }
  }
  if (typeof updateNavBadges === 'function') updateNavBadges();
  if (typeof iniciarBotonEstadoRepartidor === 'function') iniciarBotonEstadoRepartidor();
});
</script>
@endpush
