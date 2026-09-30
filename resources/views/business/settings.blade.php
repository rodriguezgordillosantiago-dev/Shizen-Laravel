<x-business-layout title="Configuración">
    <div class="page-header">
      <div>
        <h1 class="page-header-title">Configuración del Sistema y Pagos</h1>
        <p class="page-header-sub">Parámetros de atención, métodos de cobro y tiempos en <strong>Pesos Colombianos (COP $)</strong></p>
      </div>
    </div>

    @if (session('success'))
      <div class="alert alert-success">
        <i class="bx bx-check-circle" style="font-size:18px;flex-shrink:0"></i>
        {{ session('success') }}
      </div>
    @endif

    <form method="post" action="{{ route('business.settings.update') }}">
      @csrf
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px">
        
        <!-- Parámetros de Moneda y Cobro en Pesos Colombianos -->
        <div class="card">
          <div class="card-header">
            <div class="card-title"><i class="bx bx-money"></i> Moneda de Pago y Cobro</div>
          </div>
          <div class="card-body">
            <div class="form-group">
              <label class="form-label">Moneda Oficial del Sistema</label>
              <input type="text" class="form-control" value="Pesos Colombianos (COP - $)" disabled style="background:#f0fdf4;color:#047857;font-weight:700">
            </div>
            <div class="form-group">
              <label class="form-label">Valor mínimo de pedido en Pesos Colombianos (COP $)</label>
              <input type="number" class="form-control" name="minOrder" value="{{ old('minOrder', $settings['min_order'] ?? 15000) }}" placeholder="15000 COP" min="0">
              <script>
                (function (c) {
                  if (!c || c.dataset.validacionCampo) return;
                  c.dataset.validacionCampo = '1';
                  var e = c.nextElementSibling && c.nextElementSibling.classList.contains('field-error-inline') ? c.nextElementSibling : document.createElement('small');
                  e.className = 'field-error-inline';
                  e.style.cssText = 'display:block;color:#dc2626;font-size:12px;margin-top:6px;';
                  if (!e.parentNode) c.insertAdjacentElement('afterend', e);
                  function v() {
                    var val = Number(c.value);
                    var inv = c.value === '' || !Number.isFinite(val) || val < 0;
                    e.textContent = inv ? 'Ingresa un valor mínimo válido.' : '';
                    c.setAttribute('aria-invalid', inv ? 'true' : 'false');
                    return !inv;
                  }
                  ['input', 'change', 'blur'].forEach(function (t) { c.addEventListener(t, v); });
                  if (c.form) c.form.addEventListener('submit', function (a) { if (!v()) a.preventDefault(); });
                })(document.currentScript.previousElementSibling);
              </script>
            </div>
            <div class="form-group" style="margin-bottom:0">
              <label class="form-label">Radio de domicilio (km)</label>
              <input type="number" class="form-control" name="radius" value="{{ old('radius', $settings['radius'] ?? 5) }}" min="0.1" step="0.1">
              <script>
                (function (c) {
                  if (!c || c.dataset.validacionCampo) return;
                  c.dataset.validacionCampo = '1';
                  var e = c.nextElementSibling && c.nextElementSibling.classList.contains('field-error-inline') ? c.nextElementSibling : document.createElement('small');
                  e.className = 'field-error-inline';
                  e.style.cssText = 'display:block;color:#dc2626;font-size:12px;margin-top:6px;';
                  if (!e.parentNode) c.insertAdjacentElement('afterend', e);
                  function v() {
                    var val = Number(c.value);
                    var inv = c.value === '' || !Number.isFinite(val) || val <= 0;
                    e.textContent = inv ? 'Ingresa un radio válido.' : '';
                    c.setAttribute('aria-invalid', inv ? 'true' : 'false');
                    return !inv;
                  }
                  ['input', 'change', 'blur'].forEach(function (t) { c.addEventListener(t, v); });
                  if (c.form) c.form.addEventListener('submit', function (a) { if (!v()) a.preventDefault(); });
                })(document.currentScript.previousElementSibling);
              </script>
            </div>
          </div>
        </div>

        <!-- Parámetros de preparación y operación -->
        <div class="card">
          <div class="card-header">
            <div class="card-title"><i class="bx bx-dish"></i> Parámetros de Operación</div>
          </div>
          <div class="card-body">
            <div class="form-group">
              <label class="form-label">Tiempo estimado de preparación (minutos)</label>
              <input type="number" class="form-control" name="prepTime" value="{{ old('prepTime', $settings['prep_time'] ?? 25) }}" min="5" max="120">
              <script>
                (function (c) {
                  if (!c || c.dataset.validacionCampo) return;
                  c.dataset.validacionCampo = '1';
                  var e = c.nextElementSibling && c.nextElementSibling.classList.contains('field-error-inline') ? c.nextElementSibling : document.createElement('small');
                  e.className = 'field-error-inline';
                  e.style.cssText = 'display:block;color:#dc2626;font-size:12px;margin-top:6px;';
                  if (!e.parentNode) c.insertAdjacentElement('afterend', e);
                  function v() {
                    var val = Number(c.value);
                    var inv = c.value === '' || !Number.isInteger(val) || val < 5 || val > 120;
                    e.textContent = inv ? 'El tiempo debe estar entre 5 y 120 minutos.' : '';
                    c.setAttribute('aria-invalid', inv ? 'true' : 'false');
                    return !inv;
                  }
                  ['input', 'change', 'blur'].forEach(function (t) { c.addEventListener(t, v); });
                  if (c.form) c.form.addEventListener('submit', function (a) { if (!v()) a.preventDefault(); });
                })(document.currentScript.previousElementSibling);
              </script>
            </div>
            <div class="form-group">
              <label class="form-label">Máximo de pedidos simultáneos</label>
              <input type="number" class="form-control" name="maxOrders" value="{{ old('maxOrders', $settings['max_orders'] ?? 12) }}" min="1">
              <script>
                (function (c) {
                  if (!c || c.dataset.validacionCampo) return;
                  c.dataset.validacionCampo = '1';
                  var e = c.nextElementSibling && c.nextElementSibling.classList.contains('field-error-inline') ? c.nextElementSibling : document.createElement('small');
                  e.className = 'field-error-inline';
                  e.style.cssText = 'display:block;color:#dc2626;font-size:12px;margin-top:6px;';
                  if (!e.parentNode) c.insertAdjacentElement('afterend', e);
                  function v() {
                    var val = Number(c.value);
                    var inv = c.value === '' || !Number.isInteger(val) || val < 1;
                    e.textContent = inv ? 'Ingresa un máximo de pedidos válido.' : '';
                    c.setAttribute('aria-invalid', inv ? 'true' : 'false');
                    return !inv;
                  }
                  ['input', 'change', 'blur'].forEach(function (t) { c.addEventListener(t, v); });
                  if (c.form) c.form.addEventListener('submit', function (a) { if (!v()) a.preventDefault(); });
                })(document.currentScript.previousElementSibling);
              </script>
            </div>
          </div>
        </div>

        <!-- Información básica del negocio -->
        <div class="card" style="grid-column: span 2">
          <div class="card-header">
            <div class="card-title"><i class="bx bx-store"></i> Información del Establecimiento</div>
          </div>
          <div class="card-body" style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:16px">
            <div class="form-group">
              <label class="form-label">Nombre del negocio</label>
              <input type="text" class="form-control" name="nombre" value="{{ old('nombre', $business->nombre ?? '') }}" required>
            </div>
            <div class="form-group">
              <label class="form-label">Dirección</label>
              <input type="text" class="form-control" name="direccion" value="{{ old('direccion', $business->direccion ?? '') }}" required>
            </div>
            <div class="form-group">
              <label class="form-label">Correo de contacto</label>
              <input type="email" class="form-control" name="gmail_negocio" value="{{ old('gmail_negocio', $business->gmail_negocio ?? '') }}">
            </div>
          </div>
        </div>

        <!-- Métodos de Pago en Moneda Colombiana -->
        <div class="card" style="grid-column: span 2">
          <div class="card-header">
            <div class="card-title"><i class="bx bx-credit-card"></i> Métodos de Pago Habilitados (COP)</div>
          </div>
          <div class="card-body" style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:16px">
            <div class="toggle-row" style="border:1px solid #e5e7eb;border-radius:10px;padding:12px">
              <div>
                <div class="toggle-label">Efectivo (COP)</div>
                <div class="toggle-desc">Pago en efectivo al entregar</div>
              </div>
              <input type="hidden" name="cash_enabled" value="{{ !empty($settings['cash_enabled']) ? 1 : 0 }}">
              <div class="toggle {{ !empty($settings['cash_enabled']) ? 'on' : '' }}" onclick="this.classList.toggle('on'); this.previousElementSibling.value = this.classList.contains('on') ? '1' : '0'"></div>
            </div>
            <div class="toggle-row" style="border:1px solid #e5e7eb;border-radius:10px;padding:12px">
              <div>
                <div class="toggle-label">Pagos Digitales (Nequi / Daviplata / PSE)</div>
                <div class="toggle-desc">Transferencias directas COP</div>
              </div>
              <input type="hidden" name="digital_enabled" value="{{ !empty($settings['digital_enabled']) ? 1 : 0 }}">
              <div class="toggle {{ !empty($settings['digital_enabled']) ? 'on' : '' }}" onclick="this.classList.toggle('on'); this.previousElementSibling.value = this.classList.contains('on') ? '1' : '0'"></div>
            </div>
            <div class="toggle-row" style="border:1px solid #e5e7eb;border-radius:10px;padding:12px">
              <div>
                <div class="toggle-label">Tarjetas Débito / Crédito</div>
                <div class="toggle-desc">Cobro con datáfono o pasarela</div>
              </div>
              <input type="hidden" name="card_enabled" value="{{ !empty($settings['card_enabled']) ? 1 : 0 }}">
              <div class="toggle {{ !empty($settings['card_enabled']) ? 'on' : '' }}" onclick="this.classList.toggle('on'); this.previousElementSibling.value = this.classList.contains('on') ? '1' : '0'"></div>
            </div>
          </div>
        </div>

      </div>

      <div style="margin-top:24px;display:flex;justify-content:flex-end">
        <button type="submit" class="btn btn-primary btn-lg" style="width:auto">
          <i class="bx bx-save"></i> Guardar configuración
        </button>
      </div>
    </form>
</x-business-layout>
