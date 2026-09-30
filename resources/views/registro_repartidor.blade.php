<!doctype html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Registro de repartidor | Shizen</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
  <link rel="stylesheet" href="{{ asset('css/registro_base.css') }}">
</head>
<body>
<div class="view active" id="view-join-delivery">
  <div class="join-page">
    <div
      class="join-left"
      style="
        background: linear-gradient(
          135deg,
          #bf360c,
          #f57c00
        );
      "
    >
      <div
        class="join-left-bg"
        style="
          background-image: url(&quot;https://images.unsplash.com/photo-1611068562065-994ba66501ba?w=800&fit=crop&auto=format&quot;);
        "
      ></div>
      <div class="join-left-content">
        <img
          src="{{ asset('assets/logo-repartidor.png') }}"
          alt="Shizen"
          class="join-left-logo"
        />
        <span class="join-left-badge">
          Para repartidores
        </span>
        <h2>Gana dinero recorriendo la ciudad</h2>
        <p>
          Sé repartidor Shizen: horarios flexibles, mejores
          tarifas y una comunidad que cuida el planeta.
        </p>
        <br />
        <div class="benefit-grid">
          <div class="benefit-card">
            <div class="b-icon" aria-hidden="true">
              &#128176;
            </div>
            <div class="b-title">Ganancias top</div>
            <div class="b-desc">
              Las mejores tarifas del mercado
            </div>
          </div>
          <div class="benefit-card">
            <div class="b-icon" aria-hidden="true">
              &#128338;
            </div>
            <div class="b-title">Horarios libres</div>
            <div class="b-desc">Trabaja cuando quieras</div>
          </div>
          <div class="benefit-card">
            <div class="b-icon" aria-hidden="true">
              &#128737;
            </div>
            <div class="b-title">Seguro incluido</div>
            <div class="b-desc">
              Accidentes cubiertos en cada domicilio
            </div>
          </div>
          <div class="benefit-card">
            <div class="b-icon" aria-hidden="true">
              &#128242;
            </div>
            <div class="b-title">App intuitiva</div>
            <div class="b-desc">
              Gestiona todo desde tu celular
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="join-right">
      <div class="join-form-wrap">
        <a class="join-back back-link" href="{{ url('/') }}">
          ← Volver al inicio
        </a>
        <div class="progress-steps">
          <div class="step-dot active" id="joinDel-dot-1">
            1
          </div>
          <div class="step-line" id="joinDel-line-1"></div>
          <div class="step-dot" id="joinDel-dot-2">2</div>
        </div>
        <form
          method="post"
          action="{{ url('/registro-repartidor') }}"
          enctype="multipart/form-data"
        >
          @csrf
          <h2>¡Regístrate como repartidor!</h2>
          <p class="form-sub">
            Es rápido, gratis y empieza hoy.
          </p>
          <br />
          <div class="form-group">
            <label> Nombre </label>
            <input class="form-input" type="text" name="nombre" placeholder="Tu nombre" pattern="[A-Za-zÁÉÍÓÚÜÑáéíóúüñ .'-]{2,}" title="Ingresa tu nombre completo." required />
            <script>
              (function (c) {
                if (!c || c.dataset.validacionCampo) return; c.dataset.validacionCampo = "1";
                var e = c.nextElementSibling && c.nextElementSibling.classList.contains("field-error-inline") ? c.nextElementSibling : document.createElement("small");
                e.className = "field-error-inline"; e.style.cssText = "display:block;color:#dc2626;font-size:12px;margin-top:4px;font-weight:500;";
                if (!e.parentNode) c.insertAdjacentElement("afterend", e);
                function v() { var x = c.required && !c.value.trim(), i = x || !c.validity.valid; e.textContent = i ? (x ? "Este campo es obligatorio." : c.validationMessage) : ""; c.setAttribute("aria-invalid", i ? "true" : "false"); return !i; }
                ["input", "change", "blur"].forEach(function (t) { c.addEventListener(t, v); });
                if (c.form) c.form.addEventListener("submit", function (a) { if (!v()) a.preventDefault(); });
              })(document.currentScript.previousElementSibling);
            </script>
          </div>
          <div class="form-group">
            <label> Apellido </label>
            <input class="form-input" type="text" name="apellido" placeholder="Tu apellido" pattern="[A-Za-zÁÉÍÓÚÜÑáéíóúüñ .'-]{2,}" title="Ingresa tu apellido completo." required />
            <script>
              (function (c) {
                if (!c || c.dataset.validacionCampo) return; c.dataset.validacionCampo = "1";
                var e = c.nextElementSibling && c.nextElementSibling.classList.contains("field-error-inline") ? c.nextElementSibling : document.createElement("small");
                e.className = "field-error-inline"; e.style.cssText = "display:block;color:#dc2626;font-size:12px;margin-top:4px;font-weight:500;";
                if (!e.parentNode) c.insertAdjacentElement("afterend", e);
                function v() { var x = c.required && !c.value.trim(), i = x || !c.validity.valid; e.textContent = i ? (x ? "Este campo es obligatorio." : c.validationMessage) : ""; c.setAttribute("aria-invalid", i ? "true" : "false"); return !i; }
                ["input", "change", "blur"].forEach(function (t) { c.addEventListener(t, v); });
                if (c.form) c.form.addEventListener("submit", function (a) { if (!v()) a.preventDefault(); });
              })(document.currentScript.previousElementSibling);
            </script>
          </div>
          <div class="form-group">
            <label> Vehículo </label>
            <select class="form-input" name="vehiculo" required>
              <option value="" selected disabled>Selecciona un vehículo</option>
              <option value="Bicicleta">Bicicleta</option>
              <option value="Moto">Moto</option>
              <option value="Patineta electrica">Patineta eléctrica</option>
              <option value="Carro">Carro</option>
            </select>
            <script>
              (function (c) {
                if (!c || c.dataset.validacionCampo) return; c.dataset.validacionCampo = "1";
                var e = c.nextElementSibling && c.nextElementSibling.classList.contains("field-error-inline") ? c.nextElementSibling : document.createElement("small");
                e.className = "field-error-inline"; e.style.cssText = "display:block;color:#dc2626;font-size:12px;margin-top:4px;font-weight:500;";
                if (!e.parentNode) c.insertAdjacentElement("afterend", e);
                function v() { var x = c.required && !c.value.trim(), i = x || !c.validity.valid; e.textContent = i ? (x ? "Este campo es obligatorio." : c.validationMessage) : ""; c.setAttribute("aria-invalid", i ? "true" : "false"); return !i; }
                ["input", "change", "blur"].forEach(function (t) { c.addEventListener(t, v); });
                if (c.form) c.form.addEventListener("submit", function (a) { if (!v()) a.preventDefault(); });
              })(document.currentScript.previousElementSibling);
            </script>
          </div>
          <div class="form-group">
            <label> Dirección </label>
            <input class="form-input" type="text" name="direccion" placeholder="Ej. Calle 123 #45-67" maxlength="150" required />
            <script>
              (function (c) {
                if (!c || c.dataset.validacionCampo) return; c.dataset.validacionCampo = "1";
                var e = c.nextElementSibling && c.nextElementSibling.classList.contains("field-error-inline") ? c.nextElementSibling : document.createElement("small");
                e.className = "field-error-inline"; e.style.cssText = "display:block;color:#dc2626;font-size:12px;margin-top:4px;font-weight:500;";
                if (!e.parentNode) c.insertAdjacentElement("afterend", e);
                function v() { var x = c.required && !c.value.trim(), i = x || !c.validity.valid; e.textContent = i ? (x ? "Este campo es obligatorio." : c.validationMessage) : ""; c.setAttribute("aria-invalid", i ? "true" : "false"); return !i; }
                ["input", "change", "blur"].forEach(function (t) { c.addEventListener(t, v); });
                if (c.form) c.form.addEventListener("submit", function (a) { if (!v()) a.preventDefault(); });
              })(document.currentScript.previousElementSibling);
            </script>
          </div>
          <div class="form-group">
            <label> Email </label>
            <input class="form-input" type="email" name="email" placeholder="tu@email.com" required />
            <script>
              (function (c) {
                if (!c || c.dataset.validacionCampo) return; c.dataset.validacionCampo = "1";
                var e = c.nextElementSibling && c.nextElementSibling.classList.contains("field-error-inline") ? c.nextElementSibling : document.createElement("small");
                e.className = "field-error-inline"; e.style.cssText = "display:block;color:#dc2626;font-size:12px;margin-top:4px;font-weight:500;";
                if (!e.parentNode) c.insertAdjacentElement("afterend", e);
                function v() { var x = c.required && !c.value.trim(), i = x || !c.validity.valid; e.textContent = i ? (x ? "Este campo es obligatorio." : c.validationMessage) : ""; c.setAttribute("aria-invalid", i ? "true" : "false"); return !i; }
                ["input", "change", "blur"].forEach(function (t) { c.addEventListener(t, v); });
                if (c.form) c.form.addEventListener("submit", function (a) { if (!v()) a.preventDefault(); });
              })(document.currentScript.previousElementSibling);
            </script>
          </div>
          <div class="form-group">
            <label>Contraseña</label>
            <input class="form-input" id="password_repartidor" type="password" name="password" autocomplete="new-password" minlength="8" required />
            <script>
              (function (c) {
                if (!c || c.dataset.validacionCampo) return; c.dataset.validacionCampo = "1";
                var e = c.nextElementSibling && c.nextElementSibling.classList.contains("field-error-inline") ? c.nextElementSibling : document.createElement("small");
                e.className = "field-error-inline"; e.style.cssText = "display:block;color:#dc2626;font-size:12px;margin-top:4px;font-weight:500;";
                if (!e.parentNode) c.insertAdjacentElement("afterend", e);
                function v() { var x = c.required && !c.value.trim(), i = x || !c.validity.valid; e.textContent = i ? (x ? "Este campo es obligatorio." : c.validationMessage) : ""; c.setAttribute("aria-invalid", i ? "true" : "false"); return !i; }
                ["input", "change", "blur"].forEach(function (t) { c.addEventListener(t, v); });
                if (c.form) c.form.addEventListener("submit", function (a) { if (!v()) a.preventDefault(); });
              })(document.currentScript.previousElementSibling);
            </script>
          </div>
          <div class="form-group">
            <label>Confirmar contraseña</label>
            <input class="form-input" id="password-confirmation_repartidor" type="password" name="password_confirmation" autocomplete="new-password" minlength="8" required />
            <script>
              (function (c) {
                if (!c || c.dataset.validacionCampo) return; c.dataset.validacionCampo = "1";
                var e = c.nextElementSibling && c.nextElementSibling.classList.contains("field-error-inline") ? c.nextElementSibling : document.createElement("small");
                e.className = "field-error-inline"; e.style.cssText = "display:block;color:#dc2626;font-size:12px;margin-top:4px;font-weight:500;";
                if (!e.parentNode) c.insertAdjacentElement("afterend", e);
                var pass = document.getElementById("password_repartidor");
                function v() { var match = pass ? c.value === pass.value : true; var x = c.required && !c.value.trim(), i = x || !c.validity.valid || !match; e.textContent = i ? (x ? "Este campo es obligatorio." : !match ? "Las contraseñas no coinciden." : c.validationMessage) : ""; c.setAttribute("aria-invalid", i ? "true" : "false"); return !i; }
                ["input", "change", "blur"].forEach(function (t) { c.addEventListener(t, v); });
                if (pass) pass.addEventListener("input", v);
                if (c.form) c.form.addEventListener("submit", function (a) { if (!v()) a.preventDefault(); });
              })(document.currentScript.previousElementSibling);
            </script>
          </div>
          <div id="joinDel-step2">
            <h2>Casi listo</h2>
            <br />
            <br />
            <p class="form-sub">
              En el siguiente paso podrás adjuntar tus documentos.
            </p>
          </div>
          <br />
          <div class="terms-check">
            <input type="checkbox" id="termsDel" name="terminos" value="aceptado" required />
            <script>
              (function (c) {
                if (!c || c.dataset.validacionCampo) return; c.dataset.validacionCampo = "1";
                var e = c.nextElementSibling && c.nextElementSibling.classList.contains("field-error-inline") ? c.nextElementSibling : document.createElement("small");
                e.className = "field-error-inline"; e.style.cssText = "display:block;color:#dc2626;font-size:12px;margin-top:4px;font-weight:500;";
                if (!e.parentNode) c.insertAdjacentElement("afterend", e);
                function v() { var x = c.required && !c.checked; e.textContent = x ? "Debes aceptar los términos y condiciones." : ""; c.setAttribute("aria-invalid", x ? "true" : "false"); return !x; }
                ["change"].forEach(function (t) { c.addEventListener(t, v); });
                if (c.form) c.form.addEventListener("submit", function (a) { if (!v()) a.preventDefault(); });
              })(document.currentScript.previousElementSibling);
            </script>
            <label for="termsDel">
              Acepto los términos y condiciones de Shizen y la política de privacidad.
            </label>
          </div>
          <button class="btn-primary-full" type="submit">
            Continuar
          </button>
        </form>
        <div
          id="joinDel-success"
          class="success-state hidden"
        >
          <div class="success-icon" aria-hidden="true">
            &#127881;
          </div>
          <h3>
            Ya eres parte del equipo de repartidores Shizen
          </h3>
          <p>¡Bienvenido a la familia Shizen!</p>
          <a class="btn-primary-full" href="{{ url('/') }}">
            Volver al inicio
          </a>
        </div>
      </div>
    </div>
  </div>
</div>
