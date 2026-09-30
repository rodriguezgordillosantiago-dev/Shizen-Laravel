<!doctype html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Registro de usuario | Shizen</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
  <link rel="stylesheet" href="{{ asset('css/registro_base.css') }}">
</head>
<body>
<div class="view active" id="view-join-restaurant">
  <div class="join-page">
    <div class="join-left" style="background:linear-gradient(135deg,#1a3a2a,#2a7a50)">
      <div class="join-left-bg" style="background-image:url('https://images.unsplash.com/photo-1572715376701-98568319fd0b?w=800&fit=crop&auto=format')">
      </div>
      <div class="join-left-content">
        <img src="{{ asset('assets/logo.png') }}" alt="Shizen" class="join-left-logo" />
        <span class="join-left-badge">
          Para usuarios
        </span>
        <h2>
          Conoce miles de negocios veganos
        </h2>
        <p>
          Compra y haz parte de la red de personas conscientes más grande de Colombia.
        </p>
        <br>
        <div class="benefit-grid">
          <div class="benefit-card">
            <div class="b-icon">
            📖
            </div>
            <div class="b-title">
            Gran catálogo
            </div>
            <div class="b-desc">
              Accede a miles de platillos veganos al instante
            </div>
          </div>
          <div class="benefit-card">
            <div class="b-icon">
              🚵
            </div>
            <div class="b-desc">
              Domicilios en toda la ciudad
            </div>
          </div>
          <div class="benefit-card">
            <div class="b-icon">
              🏷️
            </div>
            <div class="b-title">
              Miles de descuentos
            </div>
            <div class="b-desc">
              Contribuye al cuidado del planeta y de tu bolsillo
            </div>
          </div>
          <div class="benefit-card">
            <div class="b-icon">
              💚
            </div>
            <div class="b-title">
              Marca sostenible
            </div>
            <div class="b-desc">
              Productos veganos y ecológicos
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
          <div class="step-dot active" id="joinRest-dot-1">
            1
          </div>
          <div class="step-line" id="joinRest-line-1">
          </div>
          <div class="step-dot" id="joinRest-dot-2">
            2
          </div>
        </div>
        @if ($errors->any())
          <div class="form-server-error" role="alert">
            {{ $errors->first() }}
          </div>
        @endif
        <form method="post" action="{{ route('registro.usuario') }}">
          @csrf
          <h2>
            ¡Regístrate ahora y empieza a comprar!
          </h2>
          <p class="form-sub">
            Completa tus datos para comenzar.
          </p>
          <br>
          <div class="form-group">
            <label> Nombre </label>
            <input class="form-input" type="text" name="nombre" placeholder="Tu nombre" pattern="[A-Za-zÁÉÍÓÚÜÑáéíóúüñ .'-]{2,}" title="Ingresa tu nombre." required />
            <script>
              (function (c) {
                if (!c || c.dataset.validacionCampo) return;
                c.dataset.validacionCampo = "1";
                var e = c.nextElementSibling && c.nextElementSibling.classList.contains("field-error-inline") ? c.nextElementSibling : document.createElement("small");
                e.className = "field-error-inline";
                e.style.cssText = "display:block;color:#dc2626;font-size:12px;margin-top:4px;font-weight:500;";
                if (!e.parentNode) c.insertAdjacentElement("afterend", e);
                function v() { var x = c.required && !c.value.trim(), i = x || !c.validity.valid; e.textContent = i ? (x ? "Este campo es obligatorio." : c.validationMessage) : ""; c.setAttribute("aria-invalid", i ? "true" : "false"); return !i; }
                ["input", "change", "blur"].forEach(function (t) { c.addEventListener(t, v); });
                if (c.form) c.form.addEventListener("submit", function (a) { if (!v()) a.preventDefault(); });
              })(document.currentScript.previousElementSibling);
            </script>
          </div>
          <div class="form-group">
            <label> Apellido </label>
            <input class="form-input" type="text" name="apellido" placeholder="Tu apellido" pattern="[A-Za-zÁÉÍÓÚÜÑáéíóúüñ .'-]{2,}" title="Ingresa tu apellido." required />
            <script>
              (function (c) {
                if (!c || c.dataset.validacionCampo) return;
                c.dataset.validacionCampo = "1";
                var e = c.nextElementSibling && c.nextElementSibling.classList.contains("field-error-inline") ? c.nextElementSibling : document.createElement("small");
                e.className = "field-error-inline";
                e.style.cssText = "display:block;color:#dc2626;font-size:12px;margin-top:4px;font-weight:500;";
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
                if (!c || c.dataset.validacionCampo) return;
                c.dataset.validacionCampo = "1";
                var e = c.nextElementSibling && c.nextElementSibling.classList.contains("field-error-inline") ? c.nextElementSibling : document.createElement("small");
                e.className = "field-error-inline";
                e.style.cssText = "display:block;color:#dc2626;font-size:12px;margin-top:4px;font-weight:500;";
                if (!e.parentNode) c.insertAdjacentElement("afterend", e);
                function v() { var x = c.required && !c.value.trim(), i = x || !c.validity.valid; e.textContent = i ? (x ? "Este campo es obligatorio." : c.validationMessage) : ""; c.setAttribute("aria-invalid", i ? "true" : "false"); return !i; }
                ["input", "change", "blur"].forEach(function (t) { c.addEventListener(t, v); });
                if (c.form) c.form.addEventListener("submit", function (a) { if (!v()) a.preventDefault(); });
              })(document.currentScript.previousElementSibling);
            </script>
          </div>
          <div class="form-group">
            <label for="password">Contraseña</label>
            <input class="form-input" id="password" type="password" name="password" autocomplete="new-password" minlength="8" placeholder="Mínimo 8 caracteres" required />
            <script>
              (function (c) {
                if (!c || c.dataset.validacionCampo) return;
                c.dataset.validacionCampo = "1";
                var e = c.nextElementSibling && c.nextElementSibling.classList.contains("field-error-inline") ? c.nextElementSibling : document.createElement("small");
                e.className = "field-error-inline";
                e.style.cssText = "display:block;color:#dc2626;font-size:12px;margin-top:4px;font-weight:500;";
                if (!e.parentNode) c.insertAdjacentElement("afterend", e);
                function v() { var x = c.required && !c.value.trim(), i = x || !c.validity.valid; e.textContent = i ? (x ? "Este campo es obligatorio." : c.validationMessage) : ""; c.setAttribute("aria-invalid", i ? "true" : "false"); return !i; }
                ["input", "change", "blur"].forEach(function (t) { c.addEventListener(t, v); });
                if (c.form) c.form.addEventListener("submit", function (a) { if (!v()) a.preventDefault(); });
              })(document.currentScript.previousElementSibling);
            </script>
          </div>
          <div class="form-group">
            <label for="password-confirmation">Confirmar contraseña</label>
            <input class="form-input" id="password-confirmation" type="password" name="password_confirmation" autocomplete="new-password" minlength="8" placeholder="Confirma tu contraseña" required />
            <script>
              (function (c) {
                if (!c || c.dataset.validacionCampo) return;
                c.dataset.validacionCampo = "1";
                var e = c.nextElementSibling && c.nextElementSibling.classList.contains("field-error-inline") ? c.nextElementSibling : document.createElement("small");
                e.className = "field-error-inline";
                e.style.cssText = "display:block;color:#dc2626;font-size:12px;margin-top:4px;font-weight:500;";
                if (!e.parentNode) c.insertAdjacentElement("afterend", e);
                var pass = document.getElementById("password");
                function v() { var match = pass ? c.value === pass.value : true; var x = c.required && !c.value.trim(), i = x || !c.validity.valid || !match; e.textContent = i ? (x ? "Este campo es obligatorio." : !match ? "Las contraseñas no coinciden." : c.validationMessage) : ""; c.setAttribute("aria-invalid", i ? "true" : "false"); return !i; }
                ["input", "change", "blur"].forEach(function (t) { c.addEventListener(t, v); });
                if (pass) pass.addEventListener("input", v);
                if (c.form) c.form.addEventListener("submit", function (a) { if (!v()) a.preventDefault(); });
              })(document.currentScript.previousElementSibling);
            </script>
          </div>
        <div id="joinRest-step2">
          <br>
          <br>
          <h2>Casi listo</h2>
          <br>
          <div class="terms-check">
            <input type="checkbox" id="termsRest" name="terminos" value="aceptado" required />
            <script>
              (function (c) {
                if (!c || c.dataset.validacionCampo) return;
                c.dataset.validacionCampo = "1";
                var e = c.nextElementSibling && c.nextElementSibling.classList.contains("field-error-inline") ? c.nextElementSibling : document.createElement("small");
                e.className = "field-error-inline";
                e.style.cssText = "display:block;color:#dc2626;font-size:12px;margin-top:4px;font-weight:500;";
                if (!e.parentNode) c.insertAdjacentElement("afterend", e);
                function v() { var x = c.required && !c.checked; e.textContent = x ? "Debes aceptar los términos y condiciones." : ""; c.setAttribute("aria-invalid", x ? "true" : "false"); return !x; }
                ["change"].forEach(function (t) { c.addEventListener(t, v); });
                if (c.form) c.form.addEventListener("submit", function (a) { if (!v()) a.preventDefault(); });
              })(document.currentScript.previousElementSibling);
            </script>
            <label for="termsRest">
              Acepto los términos y condiciones de Shizen y la política de privacidad.
            </label>
          </div>
          <button class="btn-primary-full" type="submit">
            Enviar solicitud 🚀
          </button>
        </div>
        </form>
        <div id="joinRest-success" class="success-state hidden">
          <div class="success-icon">
            🎉
          </div>
          <h3>
            Registro completado
          </h3>
          <p>
            Bienvenido a la familia Shizen
          </p>
          <a class="btn-primary-full" href="{{ url('/') }}">
            Volver al inicio
          </a>
        </div>
      </div>
    </div>
  </div>
</div>
</body>
</html>
