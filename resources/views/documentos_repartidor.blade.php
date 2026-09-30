<!doctype html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Documentos del repartidor | Shizen</title>
  <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
  <link rel="stylesheet" href="{{ asset('css/registro_base.css') }}">
</head>
<body>
<main class="view active document-page">
  <div class="join-page">
    <div class="join-left" style="background:linear-gradient(135deg,#bf360c,#f57c00)">
      <div class="join-left-bg" style="background-image:url('https://images.unsplash.com/photo-1611068562065-994ba66501ba?w=800&fit=crop&auto=format')"></div>
      <div class="join-left-content">
        <img src="{{ asset('assets/logo-repartidor.png') }}" alt="Shizen" class="join-left-logo" />
        <span class="join-left-badge">Para repartidores</span>
        <h2>Gana dinero recorriendo la ciudad</h2>
        <p>Sé repartidor Shizen: horarios flexibles, mejores tarifas y una comunidad que cuida el planeta.</p>
        <div class="benefit-grid">
          <div class="benefit-card">
            <div class="b-icon">💰</div>
            <div class="b-title">Ganancias top</div>
            <div class="b-desc">Las mejores tarifas del mercado</div>
          </div>
          <div class="benefit-card">
            <div class="b-icon">⌛</div>
            <div class="b-title">Horarios libres</div>
            <div class="b-desc">Trabaja cuando quieras</div>
          </div>
          <div class="benefit-card">
            <div class="b-icon">🏥</div>
            <div class="b-title">Seguro incluido</div>
            <div class="b-desc">Accidentes cubiertos en cada domicilio</div>
          </div>
          <div class="benefit-card">
            <div class="b-icon">📱</div>
            <div class="b-title">App intuitiva</div>
            <div class="b-desc">Gestiona todo desde tu celular</div>
          </div>
        </div>
      </div>
    </div>
    <div class="join-right">
      <div class="join-form-wrap document-form-wrap">
        <a class="join-back back-link" href="{{ route('registro.repartidor') }}">← Volver a tus datos</a>
        <div class="progress-steps">
          <div class="step-dot done">✓</div>
          <div class="step-line done"></div>
          <div class="step-dot active">2</div>
        </div>
        <form method="post" action="{{ route('registro.repartidor.documentos') }}" enctype="multipart/form-data">
          @csrf
          <h2>Sube tus documentos</h2>
          <p class="form-sub">Aceptamos archivos PDF. La foto es solo para identificarte.</p>
          <div class="form-group">
            <label for="cedula">Número de cédula</label>
            <input class="form-input" id="cedula" type="text" name="cedula" inputmode="numeric" pattern="[0-9]{6,12}" placeholder="Número de identificación" required /><script>(function(c){if(!c||c.dataset.validacionCampo)return;c.dataset.validacionCampo='1';var e=c.nextElementSibling&&c.nextElementSibling.classList.contains('field-error-inline')?c.nextElementSibling:document.createElement('small');e.className='field-error-inline';e.style.cssText='display:block;color:#c62828;font-size:12px;margin-top:4px;min-height:1em;';if(!e.parentNode)c.insertAdjacentElement('afterend',e);function v(){var x=c.required&&!c.value.trim(),i=x||!c.validity.valid;e.textContent=i?(x?'Este campo es obligatorio.':c.validationMessage):'';c.setAttribute('aria-invalid',i?'true':'false');return!i}['input','change','blur'].forEach(function(t){c.addEventListener(t,v)});if(c.form)c.form.addEventListener('submit',function(a){if(!v())a.preventDefault()})})(document.currentScript.previousElementSibling);</script>
          </div>
          <div class="photo-upload-box">
            <input class="upload-hidden-input" id="foto-repartidor" type="file" name="foto_repartidor" accept="image/*" required /><script>(function(c){if(!c||c.dataset.validacionCampo)return;c.dataset.validacionCampo='1';var e=c.nextElementSibling&&c.nextElementSibling.classList.contains('field-error-inline')?c.nextElementSibling:document.createElement('small');e.className='field-error-inline';e.style.cssText='display:block;color:#c62828;font-size:12px;margin-top:4px;min-height:1em;';if(!e.parentNode)c.insertAdjacentElement('afterend',e);function v(){var x=c.required&&!c.value.trim(),i=x||!c.validity.valid;e.textContent=i?(x?'Este campo es obligatorio.':c.validationMessage):'';c.setAttribute('aria-invalid',i?'true':'false');return!i}['input','change','blur'].forEach(function(t){c.addEventListener(t,v)});if(c.form)c.form.addEventListener('submit',function(a){if(!v())a.preventDefault()})})(document.currentScript.previousElementSibling);</script>
            <div class="photo-upload-wrapper">
              <label for="foto-repartidor" class="photo-upload-button" id="foto-repartidor-btn">
                <img id="foto-repartidor-preview" class="photo-preview-img" style="display: none;" alt="Vista previa de tu foto" />
                <div class="photo-placeholder" id="foto-repartidor-placeholder">
                  <span class="photo-icon">📷</span>
                  <span>Agregar foto</span>
                </div>
              </label>
              <div class="photo-filename" id="foto-repartidor-filename">Ningún archivo seleccionado</div>
            </div>
          </div>
          <div class="document-upload-grid">
            <div class="document-upload-item">
              <span>Cédula</span>
              <input class="upload-hidden-input" id="documento-cedula" type="file" name="documento_cedula" accept="application/pdf" required /><script>(function(c){if(!c||c.dataset.validacionCampo)return;c.dataset.validacionCampo='1';var e=c.nextElementSibling&&c.nextElementSibling.classList.contains('field-error-inline')?c.nextElementSibling:document.createElement('small');e.className='field-error-inline';e.style.cssText='display:block;color:#c62828;font-size:12px;margin-top:4px;min-height:1em;';if(!e.parentNode)c.insertAdjacentElement('afterend',e);function v(){var x=c.required&&!c.value.trim(),i=x||!c.validity.valid;e.textContent=i?(x?'Este campo es obligatorio.':c.validationMessage):'';c.setAttribute('aria-invalid',i?'true':'false');return!i}['input','change','blur'].forEach(function(t){c.addEventListener(t,v)});if(c.form)c.form.addEventListener('submit',function(a){if(!v())a.preventDefault()})})(document.currentScript.previousElementSibling);</script>
              <label class="upload-pdf-button" for="documento-cedula" id="documento-cedula-label">📄 Cédula</label>
            </div>

            @php
              $regRep = session('registro_repartidor');
              $esMotorizado = !empty($regRep['vehiculo']) && in_array($regRep['vehiculo'], ['Moto', 'Carro']);
            @endphp
            @if ($esMotorizado)
            <div class="document-upload-item">
              <span>Licencia de conducción</span>
              <input class="upload-hidden-input" id="licencia" type="file" name="licencia_conduccion" accept="application/pdf" required /><script>(function(c){if(!c||c.dataset.validacionCampo)return;c.dataset.validacionCampo='1';var e=c.nextElementSibling&&c.nextElementSibling.classList.contains('field-error-inline')?c.nextElementSibling:document.createElement('small');e.className='field-error-inline';e.style.cssText='display:block;color:#c62828;font-size:12px;margin-top:4px;min-height:1em;';if(!e.parentNode)c.insertAdjacentElement('afterend',e);function v(){var x=c.required&&!c.value.trim(),i=x||!c.validity.valid;e.textContent=i?(x?'Este campo es obligatorio.':c.validationMessage):'';c.setAttribute('aria-invalid',i?'true':'false');return!i}['input','change','blur'].forEach(function(t){c.addEventListener(t,v)});if(c.form)c.form.addEventListener('submit',function(a){if(!v())a.preventDefault()})})(document.currentScript.previousElementSibling);</script>
              <label class="upload-pdf-button" for="licencia" id="licencia-label">📄 Licencia</label>
            </div>
            <div class="document-upload-item">
              <span>Tarjeta de propiedad</span>
              <input class="upload-hidden-input" id="tarjeta" type="file" name="tarjeta_propiedad" accept="application/pdf" required /><script>(function(c){if(!c||c.dataset.validacionCampo)return;c.dataset.validacionCampo='1';var e=c.nextElementSibling&&c.nextElementSibling.classList.contains('field-error-inline')?c.nextElementSibling:document.createElement('small');e.className='field-error-inline';e.style.cssText='display:block;color:#c62828;font-size:12px;margin-top:4px;min-height:1em;';if(!e.parentNode)c.insertAdjacentElement('afterend',e);function v(){var x=c.required&&!c.value.trim(),i=x||!c.validity.valid;e.textContent=i?(x?'Este campo es obligatorio.':c.validationMessage):'';c.setAttribute('aria-invalid',i?'true':'false');return!i}['input','change','blur'].forEach(function(t){c.addEventListener(t,v)});if(c.form)c.form.addEventListener('submit',function(a){if(!v())a.preventDefault()})})(document.currentScript.previousElementSibling);</script>
              <label class="upload-pdf-button" for="tarjeta" id="tarjeta-label">📄 Tarjeta</label>
            </div>
            <div class="document-upload-item">
              <span>SOAT vigente</span>
              <input class="upload-hidden-input" id="soat" type="file" name="soat" accept="application/pdf" required /><script>(function(c){if(!c||c.dataset.validacionCampo)return;c.dataset.validacionCampo='1';var e=c.nextElementSibling&&c.nextElementSibling.classList.contains('field-error-inline')?c.nextElementSibling:document.createElement('small');e.className='field-error-inline';e.style.cssText='display:block;color:#c62828;font-size:12px;margin-top:4px;min-height:1em;';if(!e.parentNode)c.insertAdjacentElement('afterend',e);function v(){var x=c.required&&!c.value.trim(),i=x||!c.validity.valid;e.textContent=i?(x?'Este campo es obligatorio.':c.validationMessage):'';c.setAttribute('aria-invalid',i?'true':'false');return!i}['input','change','blur'].forEach(function(t){c.addEventListener(t,v)});if(c.form)c.form.addEventListener('submit',function(a){if(!v())a.preventDefault()})})(document.currentScript.previousElementSibling);</script>
              <label class="upload-pdf-button" for="soat" id="soat-label">📄 SOAT</label>
            </div>
            @endif
          </div>
          <button class="btn-primary-full" type="submit">Finalizar solicitud</button>
        </form>
      </div>
    </div>
  </div>
</main>

<script>
  (function() {
    var inputFoto = document.getElementById("foto-repartidor");
    var preview = document.getElementById("foto-repartidor-preview");
    var placeholder = document.getElementById("foto-repartidor-placeholder");
    var filename = document.getElementById("foto-repartidor-filename");
    var cedulaInput = document.getElementById("cedula");

    function ensureError(input, messageText) {
      var error = input.parentElement.querySelector(".field-error");
      if (!error) {
        error = document.createElement("p");
        error.className = "field-error";
        error.style.cssText = "color: #dc2626; font-size: 12px; margin: 8px 0 0;";
        input.parentElement.appendChild(error);
      }
      error.textContent = messageText || "";
      return error;
    }

    function validateCedula() {
      if (!cedulaInput) return;
      var value = cedulaInput.value.trim();
      var valid = /^\d{6,12}$/.test(value);
      cedulaInput.setCustomValidity(valid ? "" : "Ingresa una cédula válida.");
      ensureError(cedulaInput, cedulaInput.dataset.touched === "true" && !valid ? "Ingresa una cédula válida." : "");
    }

    if (cedulaInput) {
      cedulaInput.addEventListener("input", function () {
        cedulaInput.dataset.touched = "true";
        validateCedula();
      });
      cedulaInput.addEventListener("blur", function () {
        cedulaInput.dataset.touched = "true";
        validateCedula();
      });
    }

    if (inputFoto) {
      inputFoto.addEventListener("change", function () {
        if (this.files && this.files[0]) {
          var file = this.files[0];
          var valid = /\.(jpe?g|png|webp)$/i.test(file.name);
          this.setCustomValidity(valid ? "" : "La foto debe ser una imagen válida.");
          ensureError(this, this.dataset.touched === "true" && !valid ? "La foto debe ser una imagen válida." : "");
          if (!valid) return;
          preview.src = URL.createObjectURL(file);
          preview.style.display = "block";
          if (placeholder) placeholder.style.display = "none";
          if (filename) {
            filename.textContent = file.name;
            filename.classList.add("selected");
          }
        }
      });
      inputFoto.addEventListener("blur", function () {
        this.dataset.touched = "true";
      });
    }

    var pdfInputs = document.querySelectorAll(".document-upload-item input[type='file']");
    pdfInputs.forEach(function(input) {
      input.addEventListener("change", function() {
        var label = this.parentElement.querySelector(".upload-pdf-button");
        var file = this.files && this.files[0];
        var valid = !file || /\.pdf$/i.test(file.name);
        this.setCustomValidity(valid ? "" : "El archivo debe ser PDF.");
        ensureError(this, this.dataset.touched === "true" && !valid ? "El archivo debe ser PDF." : "");
        if (label && file) {
          label.textContent = valid ? "✓ " + file.name : label.dataset.defaultText || label.textContent;
          label.classList.toggle("uploaded", valid);
        }
      });
      input.addEventListener("blur", function () {
        this.dataset.touched = "true";
      });
      if (input.parentElement.querySelector(".upload-pdf-button")) {
        input.parentElement.querySelector(".upload-pdf-button").dataset.defaultText = input.parentElement.querySelector(".upload-pdf-button").textContent;
      }
    });

    validateCedula();
  })();
</script>
</body>
</html>
