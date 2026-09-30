<x-business-layout title="Perfil del Negocio">
@php
    $displayLogo = $business->logo_display_url ?? asset('assets/vegano_central_logo.jpg');

    $ownerName = trim(($user->nombre ?? '') . ' ' . ($user->apellido ?? '')) ?: 'Propietario';
    $categoriaText = !empty($business->categoria) ? $business->categoria : 'Alimentación Vegana';
    $direccionText = !empty($business->direccion) ? $business->direccion : 'Dirección no registrada';
    $coberturaText = !empty($businessConfig->radius) ? $businessConfig->radius . ' km de cobertura' : 'Radio por configurar';
    $prepTimeText = !empty($businessConfig->prep_time) ? $businessConfig->prep_time . ' min' : 'Por definir';
    $horarioText = (!empty($business->hora_apertura) && !empty($business->hora_cierre))
        ? date('h:i A', strtotime($business->hora_apertura)) . ' - ' . date('h:i A', strtotime($business->hora_cierre))
        : 'Horario no configurado';
    $costoEnvioText = !empty($businessConfig->min_order) ? 'Pedido mín: $' . number_format((float)$businessConfig->min_order, 0, ',', '.') . ' COP' : 'Tarifa estándar';
    $descripcionText = (string)($business->descripcion ?? '');
    $ratingLabel = $ratingAvg !== null ? number_format($ratingAvg, 1) . ' ★' : 'Nuevo';
@endphp

    <div class="page-header">
      <div>
        <h1 class="page-header-title">Perfil del Negocio</h1>
        <p class="page-header-sub">Información general y edición de tu establecimiento</p>
      </div>
    </div>

    @if (session('success'))
      <div class="alert alert-success">
        <i class="bx bx-check-circle" style="font-size:18px;flex-shrink:0"></i>
        {{ session('success') }}
      </div>
    @endif

    @if (session('error'))
      <div class="alert alert-danger">
        <i class="bx bx-error-circle" style="font-size:18px;flex-shrink:0"></i>
        {{ session('error') }}
      </div>
    @endif

    <div style="max-width:860px">
      
      <!-- SECCIÓN SUPERIOR: LOGO Y DATOS DE ENCABEZADO -->
      <div style="display:flex;align-items:center;gap:20px;padding-bottom:20px;border-bottom:1px solid #f3f4f6;flex-wrap:wrap">
        
        <!-- Logo de la empresa -->
        <div style="width:110px;height:110px;border-radius:12px;overflow:hidden;border:2px solid #e5e7eb;background:#f9fafb;flex-shrink:0;display:flex;align-items:center;justify-content:center">
          <img src="{{ $displayLogo }}" 
               alt="Logo de {{ $business->nombre ?? 'Negocio' }}" 
               style="width:100%;height:100%;object-fit:cover;display:block">
        </div>

        <div style="flex:1">
          <h2 style="font-size:24px;font-weight:800;color:#111827;margin:0 0 6px">
            {{ $business->nombre ?? 'Restaurante Vegano Central' }}
          </h2>
          <p style="font-size:14px;color:#4b5563;margin:0 0 10px;line-height:1.4">
            {{ $descripcionText }}
          </p>
          <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap">
            <span style="background:#fef3c7;color:#d97706;font-size:12px;font-weight:800;padding:4px 10px;border-radius:6px;display:inline-flex;align-items:center;gap:4px">
              {{ $ratingLabel }}
            </span>
            <span style="background:#f3f4f6;color:#374151;font-size:12px;font-weight:600;padding:4px 10px;border-radius:6px">
              ⏱️ {{ $prepTimeText }}
            </span>
            <span style="background:var(--green-light);color:var(--green-dark);font-size:12px;font-weight:700;padding:4px 10px;border-radius:6px">
              🏷️ {{ $categoriaText }}
            </span>
          </div>
        </div>
      </div>

      <!-- SECCIÓN INTERMEDIA: DATOS DEL NEGOCIO (SOLO LECTURA) -->
      <div style="padding:20px 0;border-bottom:1px solid #f3f4f6">
        <h3 style="font-size:15px;font-weight:800;color:#374151;margin:0 0 14px;text-transform:uppercase;letter-spacing:0.5px">
          📋 Datos del Establecimiento
        </h3>
        
        <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(220px, 1fr));gap:16px">
          <div>
            <span style="font-size:12px;color:#6b7280;display:block">Categoría principal:</span>
            <strong style="font-size:14px;color:#111827">{{ $categoriaText }}</strong>
          </div>
          <div>
            <span style="font-size:12px;color:#6b7280;display:block">Calificación y tiempos:</span>
            <strong style="font-size:14px;color:#111827">{{ $ratingLabel }} - {{ $prepTimeText }}</strong>
          </div>
          <div>
            <span style="font-size:12px;color:#6b7280;display:block">Dirección / Cobertura:</span>
            <strong style="font-size:14px;color:#111827">{{ $direccionText }} ({{ $coberturaText }})</strong>
          </div>
          <div>
            <span style="font-size:12px;color:#6b7280;display:block">Horario de atención:</span>
            <strong style="font-size:14px;color:#111827">{{ $horarioText }}</strong>
          </div>
          <div>
            <span style="font-size:12px;color:#6b7280;display:block">Costo de envío:</span>
            <strong style="font-size:14px;color:var(--green-dark)">{{ $costoEnvioText }}</strong>
          </div>
          <div>
            <span style="font-size:12px;color:#6b7280;display:block">Propietario registrado:</span>
            <strong style="font-size:14px;color:#111827">{{ $ownerName }}</strong>
          </div>
        </div>
      </div>

      <!-- SECCIÓN INFERIOR: FORMULARIO DE EDICIÓN -->
      <div style="padding-top:20px;border-top:1px solid #f3f4f6">
        <div id="btn-toggle-box" style="{{ request()->has('edit') ? 'display:none;' : '' }}">
          <button type="button" onclick="document.getElementById('form-editar-perfil').style.display='block';document.getElementById('btn-toggle-box').style.display='none';" class="btn btn-secondary" style="font-weight:700;padding:8px 18px;display:inline-flex;align-items:center;gap:6px">
            ✏️ Editar Perfil
          </button>
        </div>

        <div id="form-editar-perfil" style="margin-top:10px;{{ request()->has('edit') ? '' : 'display:none;' }}">

          <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px">
            <h3 style="font-size:16px;font-weight:800;color:#111827;margin:0">Editar Perfil</h3>
            <button type="button" onclick="document.getElementById('form-editar-perfil').style.display='none';document.getElementById('btn-toggle-box').style.display='block';" style="background:none;border:none;color:#6b7280;font-size:13px;cursor:pointer;padding:4px 8px;border-radius:6px;font-weight:600">
              ✕ Cancelar
            </button>
          </div>

          <form method="post" action="{{ route('business.profile.update') }}" enctype="multipart/form-data" novalidate>
            @csrf
            <input type="hidden" name="action" value="update_business_profile">
            <!-- Campo 1: Foto / Logo -->
            <div class="form-group" style="margin-bottom:18px">
              <label class="form-label" style="font-weight:600">Logo del negocio</label>
              
              <div id="logoPreviewWrap" style="margin-bottom:10px">
                <img id="logoPreviewImg" src="{{ $displayLogo }}" 
                     alt="Preview Logo" style="width:110px;height:110px;border-radius:10px;object-fit:cover;border:2px solid #e5e7eb;display:block">
              </div>

              <label for="logoImagenInput" style="display:flex;align-items:center;gap:12px;cursor:pointer;border:2px dashed #d1d5db;border-radius:10px;padding:14px 16px;background:#f9fafb;transition:border-color .2s">
                <i class="bx bx-image-add" style="font-size:26px;color:var(--green)"></i>
                <div>
                  <div style="font-weight:600;color:#374151;font-size:13px">Subir nueva imagen de logo</div>
                  <div style="font-size:11px;color:#9ca3af">JPG, PNG, WebP o GIF · Máx. 5 MB</div>
                </div>
              </label>
              <input type="file" id="logoImagenInput" name="logo_imagen" accept="image/*" style="display:none" onchange="previewProfileLogo(this)">
            </div>

            <!-- Campo 2: Nombre -->
            <div class="form-group" style="margin-bottom:16px">
              <label class="form-label" for="nombre_negocio" style="font-weight:600">Nombre del negocio *</label>
              <input type="text" id="nombre_negocio" name="nombre_negocio" class="form-control"
                     value="{{ $business->nombre ?? '' }}"
                     placeholder="Nombre del negocio" required minlength="2">
              <script>
                (function(){var c=document.currentScript.previousElementSibling;if(!c)return;var e=document.createElement('small');e.className='field-error-inline';e.style.cssText='display:block;color:#dc2626;font-size:12px;margin-top:4px;';c.insertAdjacentElement('afterend',e);function v(){var empty=c.required&&!c.value.trim();var i=empty||c.value.trim().length<2;e.textContent=i?(empty?'Este campo es obligatorio.':'Mínimo 2 caracteres.'):'';c.setAttribute('aria-invalid',i?'true':'false');return!i;}['input','change','blur'].forEach(function(t){c.addEventListener(t,v);});if(c.form)c.form.addEventListener('submit',function(a){if(!v())a.preventDefault();});}());
              </script>
            </div>

            <!-- Campo 3: Descripción -->
            <div class="form-group" style="margin-bottom:20px">
              <label class="form-label" for="descripcion_negocio" style="font-weight:600">Descripción corta *</label>
              <textarea id="descripcion_negocio" name="descripcion_negocio" class="form-control" rows="3"
                        placeholder="Describe tu negocio..." required minlength="5"
                        style="padding:10px;border-radius:8px;font-family:inherit">{{ $descripcionText }}</textarea>
              <script>
                (function(){var c=document.currentScript.previousElementSibling;if(!c)return;var e=document.createElement('small');e.className='field-error-inline';e.style.cssText='display:block;color:#dc2626;font-size:12px;margin-top:4px;';c.insertAdjacentElement('afterend',e);function v(){var empty=c.required&&!c.value.trim();var i=empty||c.value.trim().length<5;e.textContent=i?(empty?'Este campo es obligatorio.':'Mínimo 5 caracteres.'):'';c.setAttribute('aria-invalid',i?'true':'false');return!i;}['input','change','blur'].forEach(function(t){c.addEventListener(t,v);});if(c.form)c.form.addEventListener('submit',function(a){if(!v())a.preventDefault();});}());
              </script>
            </div>

            <button type="submit" class="btn btn-primary" style="font-weight:700;padding:10px 22px">
              <i class="bx bx-save"></i> Guardar Cambios
            </button>
          </form>
        </div>
      </div>

    </div>

    @push('scripts')
    <script>
    function previewProfileLogo(input) {
      const previewImg = document.getElementById('logoPreviewImg');
      if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
          previewImg.src = e.target.result;
        };
        reader.readAsDataURL(input.files[0]);
      }
    }
    </script>
    @endpush
</x-business-layout>
