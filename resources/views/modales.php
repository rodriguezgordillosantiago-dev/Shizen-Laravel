<?php
$authUser = session('auth_user');
$profileUser = is_array($authUser) ? $authUser : null;
$csrfToken = csrf_token();
?>
<script>
  window.shizenUser = <?= json_encode($profileUser ? [
      'id' => (int) ($profileUser['id_usuario'] ?? 0),
      'nombre' => (string) ($profileUser['nombre'] ?? ''),
  ] : null, JSON_UNESCAPED_SLASHES) ?>;
  window.shizenNotifUrl = <?= json_encode(url('/php/notificaciones.php?json=1'), JSON_UNESCAPED_SLASHES) ?>;
  window.loginError = <?= json_encode(isset($errors) && $errors->any()) ?>;
</script>
<?php if (!$profileUser): ?>
<div class="modal-overlay" id="loginModal" onclick="handleLoginOverlayClick(event)">
  <div class="modal-card login-modal-card">
    <button class="modal-close" type="button" onclick="closeLoginModal()" aria-label="Cerrar">×</button>
    <img class="modal-icon-logo login-modal-logo" src="<?= e(asset('assets/logo.png')) ?>" alt="Shizen">
    <div class="modal-title">Inicia sesión</div>
    <p class="modal-sub">Accede a tu cuenta Shizen para continuar.</p>
    <?php if (isset($errors) && $errors->any()): ?><p class="login-error" role="alert"><?= e($errors->first()) ?></p><?php endif; ?>
    <form method="post" action="<?= e(route('login')) ?>">
      <?= csrf_field() ?>
      <input type="hidden" id="loginModalRedirect" name="redirect" value="<?= e(request('redirect', '/')) ?>">
      <div class="modal-form-group">
        <label for="modal-login-email">Email</label>
        <input class="modal-input" id="modal-login-email" name="email" type="email" placeholder="tu@email.com" autocomplete="email" value="<?= e(old('email', '')) ?>" required>
      </div>
      <div class="modal-form-group">
        <label for="modal-login-password">Contraseña</label>
        <div class="password-field">
          <input class="modal-input" id="modal-login-password" name="password" type="password" placeholder="Contraseña" autocomplete="current-password" minlength="8" maxlength="255" required>
          <button class="password-toggle" type="button" onclick="togglePassword(this)" aria-label="Mostrar contraseña" title="Mostrar contraseña">&#128065;</button>
        </div>
      </div>
      <button class="btn-modal-primary" type="submit">Ingresar</button>
    </form>
    <p class="login-terms">Al ingresar aceptas los <a href="<?= e(asset('legal/terminos.html')) ?>" target="_blank" rel="noopener">Términos y Condiciones</a>.</p>
    <p class="login-register-prompt">¿Aún no tienes cuenta? <a href="<?= e(url('/registro-usuario')) ?>">Crear cuenta</a></p>
  </div>
</div>
<?php endif; ?>
  <?php if ($profileUser): ?>
  <div class="modal-overlay" id="profileModal" onclick="handleProfileOverlayClick(event)">
    <div class="modal-card profile-card">
      <button class="modal-close" type="button" onclick="closeProfileModal()" aria-label="Cerrar">×</button>
      <div class="modal-title">Mis datos</div>
      <p class="modal-sub">Consulta la información de tu cuenta Shizen.</p>
      <?php if (session('profile_success')): ?><p class="profile-success"><?= e(session('profile_success')) ?></p><?php endif; ?>
      <form method="post" action="<?= e(route('profile.update')) ?>">
        <?= csrf_field() ?>
        <div class="profile-fields">
          <input class="modal-input profile-field" name="nombre" value="<?= e($profileUser['nombre'] ?? '') ?>" placeholder="Nombre" readonly required><script>(function(c){if(!c||c.dataset.validacionCampo)return;c.dataset.validacionCampo='1';var e=c.nextElementSibling&&c.nextElementSibling.classList.contains('field-error-inline')?c.nextElementSibling:document.createElement('small');e.className='field-error-inline';e.style.cssText='display:block;color:#c62828;font-size:12px;margin-top:4px;min-height:1em;';if(!e.parentNode)c.insertAdjacentElement('afterend',e);function v(){var x=c.required&&!c.value.trim(),i=x||!c.validity.valid;e.textContent=i?(x?'Este campo es obligatorio.':c.validationMessage):'';c.setAttribute('aria-invalid',i?'true':'false');return!i}['input','change','blur'].forEach(function(t){c.addEventListener(t,v)});if(c.form)c.form.addEventListener('submit',function(a){if(!v())a.preventDefault()})})(document.currentScript.previousElementSibling);</script>
          <input class="modal-input profile-field" name="apellido" value="<?= e($profileUser['apellido'] ?? '') ?>" placeholder="Apellido" readonly required><script>(function(c){if(!c||c.dataset.validacionCampo)return;c.dataset.validacionCampo='1';var e=c.nextElementSibling&&c.nextElementSibling.classList.contains('field-error-inline')?c.nextElementSibling:document.createElement('small');e.className='field-error-inline';e.style.cssText='display:block;color:#c62828;font-size:12px;margin-top:4px;min-height:1em;';if(!e.parentNode)c.insertAdjacentElement('afterend',e);function v(){var x=c.required&&!c.value.trim(),i=x||!c.validity.valid;e.textContent=i?(x?'Este campo es obligatorio.':c.validationMessage):'';c.setAttribute('aria-invalid',i?'true':'false');return!i}['input','change','blur'].forEach(function(t){c.addEventListener(t,v)});if(c.form)c.form.addEventListener('submit',function(a){if(!v())a.preventDefault()})})(document.currentScript.previousElementSibling);</script>
          <input class="modal-input profile-wide profile-field" name="direccion" value="<?= e($profileUser['direccion'] ?? '') ?>" placeholder="Dirección" readonly><script>(function(c){if(!c||c.dataset.validacionCampo)return;c.dataset.validacionCampo='1';var e=c.nextElementSibling&&c.nextElementSibling.classList.contains('field-error-inline')?c.nextElementSibling:document.createElement('small');e.className='field-error-inline';e.style.cssText='display:block;color:#c62828;font-size:12px;margin-top:4px;min-height:1em;';if(!e.parentNode)c.insertAdjacentElement('afterend',e);function v(){var x=c.required&&!c.value.trim(),i=x||!c.validity.valid;e.textContent=i?(x?'Este campo es obligatorio.':c.validationMessage):'';c.setAttribute('aria-invalid',i?'true':'false');return!i}['input','change','blur'].forEach(function(t){c.addEventListener(t,v)});if(c.form)c.form.addEventListener('submit',function(a){if(!v())a.preventDefault()})})(document.currentScript.previousElementSibling);</script>
          <input class="modal-input profile-field" name="ciudad" value="<?= e($profileUser['ciudad'] ?? '') ?>" placeholder="Ciudad" readonly><script>(function(c){if(!c||c.dataset.validacionCampo)return;c.dataset.validacionCampo='1';var e=c.nextElementSibling&&c.nextElementSibling.classList.contains('field-error-inline')?c.nextElementSibling:document.createElement('small');e.className='field-error-inline';e.style.cssText='display:block;color:#c62828;font-size:12px;margin-top:4px;min-height:1em;';if(!e.parentNode)c.insertAdjacentElement('afterend',e);function v(){var x=c.required&&!c.value.trim(),i=x||!c.validity.valid;e.textContent=i?(x?'Este campo es obligatorio.':c.validationMessage):'';c.setAttribute('aria-invalid',i?'true':'false');return!i}['input','change','blur'].forEach(function(t){c.addEventListener(t,v)});if(c.form)c.form.addEventListener('submit',function(a){if(!v())a.preventDefault()})})(document.currentScript.previousElementSibling);</script>
        </div>
        <?php if (isset($errors) && $errors->any()): ?><p class="login-error"><?= e($errors->first()) ?></p><?php endif; ?>
        <button class="btn-modal-primary" id="profileEditButton" type="button" onclick="enableProfileEditing()">Editar datos</button>
        <button class="btn-modal-primary profile-save-button" id="profileSaveButton" type="submit" hidden>Guardar cambios</button>
      </form>
      <form class="profile-logout-form" method="post" action="<?= e(route('logout')) ?>">
        <?= csrf_field() ?>
        <button class="profile-logout-button" type="submit"><svg class="logout-icon" width="18" height="18" fill="currentColor" viewBox="0 0 512 512" aria-hidden="true"><path d="M377.9 105.9L500.7 228.7c7.2 7.2 11.3 17.1 11.3 27.3s-4.1 20.1-11.3 27.3L377.9 406.1c-6.4 6.4-15 9.9-24 9.9c-18.7 0-33.9-15.2-33.9-33.9l0-62.1-128 0c-17.7 0-32-14.3-32-32l0-64c0-17.7 14.3-32 32-32l128 0 0-62.1c0-18.7 15.2-33.9 33.9-33.9c9 0 17.6 3.6 24 9.9zM160 96L96 96c-17.7 0-32 14.3-32 32l0 256c0 17.7 14.3 32 32 32l64 0c17.7 0 32 14.3 32 32s-14.3 32-32 32l-64 0c-53 0-96-43-96-96L0 128C0 75 43 32 96 32l64 0c17.7 0 32 14.3 32 32s-14.3 32-32 32z"></path></svg> Cerrar sesión</button>
      </form>
    </div>
  </div>
  <?php endif; ?>
<?php if ($profileUser): ?>
<div class="modal-overlay" id="notificationModal" onclick="handleNotificationOverlayClick(event)">
  <div class="modal-card notification-modal-card">
    <button class="modal-close" type="button" onclick="closeNotificationModal()" aria-label="Cerrar">×</button>
    <div class="modal-icon">🔔</div>
    <div class="modal-title">Notificaciones</div>
    <p class="modal-sub">Actualizaciones de tus pedidos.</p>
    <div class="notification-modal-list" id="notificationModalList">
      <p class="notification-modal-empty">Cargando notificaciones…</p>
    </div>
    <a class="notification-modal-link" href="<?= e(route('notifications')) ?>">Ver todas</a>
  </div>
</div>
<?php endif; ?>
  <button class="chat-bubble" type="button" onclick="toggleChat()" aria-label="Abrir chat">
    <img src="<?= e(asset('assets/shizen-chat-leaf.png')) ?>" alt="">
  </button>
  <div class="chat-panel" id="chatPanel" aria-hidden="true">
    <div class="chat-header"><strong>Ayuda Shizen</strong><button type="button" onclick="toggleChat()" aria-label="Cerrar chat">×</button></div>
    <div class="chat-body"><p>¡Hola! ¿En qué podemos ayudarte?</p><button type="button" onclick="this.textContent='Un asesor te responderá pronto.'">Hablar con un asesor</button></div>
  </div>
<div class="modal-overlay" id="cartModal" onclick="handleCartOverlayClick(event)">
  <div class="modal-card cart-card">
    <button class="modal-close" type="button" onclick="closeCart()" aria-label="Cerrar">×</button>
    <div class="modal-title">Tu carrito</div>
    <div id="cartItems" class="cart-items"></div>
    <div class="cart-total-row" id="cartTotalRow">
      <span>Total</span>
      <strong id="cartTotal">$0</strong>
    </div>
    <button class="btn-modal-primary" type="button" id="checkoutButton" onclick="openCheckout()">
      Continuar compra
    </button>
  </div>
</div>
<div class="modal-overlay" id="checkoutModal" onclick="handleCheckoutOverlayClick(event)">
  <div class="modal-card checkout-card">
    <button class="modal-close" type="button" onclick="closeCheckout()" aria-label="Cerrar">×</button>
    <div class="modal-title">Datos de entrega</div>
    <p class="modal-sub">Completa tus datos para registrar el pedido.</p>
    <?php if (isset($errors) && $errors->any()): ?><p class="login-error" role="alert"><?= e($errors->first()) ?></p><?php endif; ?>
    <form method="post" action="<?= e(route('orders.store')) ?>" onsubmit="prepareCheckout(event)">
      <input type="hidden" name="items" id="checkoutItems">
      <?= csrf_field() ?>
      <div class="checkout-fields">
        <input class="modal-input" name="nombre" value="<?= e($profileUser['nombre'] ?? '') ?>" placeholder="Nombre" required maxlength="60"><script>(function(c){if(!c||c.dataset.validacionCampo)return;c.dataset.validacionCampo='1';var e=c.nextElementSibling&&c.nextElementSibling.classList.contains('field-error-inline')?c.nextElementSibling:document.createElement('small');e.className='field-error-inline';e.style.cssText='display:block;color:#c62828;font-size:12px;margin-top:4px;min-height:1em;';if(!e.parentNode)c.insertAdjacentElement('afterend',e);function v(){var x=c.required&&!c.value.trim(),i=x||!c.validity.valid;e.textContent=i?(x?'Este campo es obligatorio.':c.validationMessage):'';c.setAttribute('aria-invalid',i?'true':'false');return!i}['input','change','blur'].forEach(function(t){c.addEventListener(t,v)});if(c.form)c.form.addEventListener('submit',function(a){if(!v())a.preventDefault()})})(document.currentScript.previousElementSibling);</script>
        <input class="modal-input" name="apellido" value="<?= e($profileUser['apellido'] ?? '') ?>" placeholder="Apellido" required maxlength="60"><script>(function(c){if(!c||c.dataset.validacionCampo)return;c.dataset.validacionCampo='1';var e=c.nextElementSibling&&c.nextElementSibling.classList.contains('field-error-inline')?c.nextElementSibling:document.createElement('small');e.className='field-error-inline';e.style.cssText='display:block;color:#c62828;font-size:12px;margin-top:4px;min-height:1em;';if(!e.parentNode)c.insertAdjacentElement('afterend',e);function v(){var x=c.required&&!c.value.trim(),i=x||!c.validity.valid;e.textContent=i?(x?'Este campo es obligatorio.':c.validationMessage):'';c.setAttribute('aria-invalid',i?'true':'false');return!i}['input','change','blur'].forEach(function(t){c.addEventListener(t,v)});if(c.form)c.form.addEventListener('submit',function(a){if(!v())a.preventDefault()})})(document.currentScript.previousElementSibling);</script>
        <input class="modal-input" name="numero_documento" placeholder="Número de documento" required maxlength="20" inputmode="numeric"><script>(function(c){if(!c||c.dataset.validacionCampo)return;c.dataset.validacionCampo='1';var e=c.nextElementSibling&&c.nextElementSibling.classList.contains('field-error-inline')?c.nextElementSibling:document.createElement('small');e.className='field-error-inline';e.style.cssText='display:block;color:#c62828;font-size:12px;margin-top:4px;min-height:1em;';if(!e.parentNode)c.insertAdjacentElement('afterend',e);function v(){var x=c.required&&!c.value.trim(),i=x||!c.validity.valid;e.textContent=i?(x?'Este campo es obligatorio.':c.validationMessage):'';c.setAttribute('aria-invalid',i?'true':'false');return!i}['input','change','blur'].forEach(function(t){c.addEventListener(t,v)});if(c.form)c.form.addEventListener('submit',function(a){if(!v())a.preventDefault()})})(document.currentScript.previousElementSibling);</script>

        <input class="modal-input checkout-wide" name="correo" type="email" value="<?= e($profileUser['email'] ?? '') ?>" placeholder="Correo electrónico" required maxlength="120"><script>(function(c){if(!c||c.dataset.validacionCampo)return;c.dataset.validacionCampo='1';var e=c.nextElementSibling&&c.nextElementSibling.classList.contains('field-error-inline')?c.nextElementSibling:document.createElement('small');e.className='field-error-inline';e.style.cssText='display:block;color:#c62828;font-size:12px;margin-top:4px;min-height:1em;';if(!e.parentNode)c.insertAdjacentElement('afterend',e);function v(){var x=c.required&&!c.value.trim(),i=x||!c.validity.valid;e.textContent=i?(x?'Este campo es obligatorio.':c.validationMessage):'';c.setAttribute('aria-invalid',i?'true':'false');return!i}['input','change','blur'].forEach(function(t){c.addEventListener(t,v)});if(c.form)c.form.addEventListener('submit',function(a){if(!v())a.preventDefault()})})(document.currentScript.previousElementSibling);</script>
        <input class="modal-input checkout-wide" name="direccion" placeholder="Dirección de entrega" required maxlength="255"><script>(function(c){if(!c||c.dataset.validacionCampo)return;c.dataset.validacionCampo='1';var e=c.nextElementSibling&&c.nextElementSibling.classList.contains('field-error-inline')?c.nextElementSibling:document.createElement('small');e.className='field-error-inline';e.style.cssText='display:block;color:#c62828;font-size:12px;margin-top:4px;min-height:1em;';if(!e.parentNode)c.insertAdjacentElement('afterend',e);function v(){var x=c.required&&!c.value.trim(),i=x||!c.validity.valid;e.textContent=i?(x?'Este campo es obligatorio.':c.validationMessage):'';c.setAttribute('aria-invalid',i?'true':'false');return!i}['input','change','blur'].forEach(function(t){c.addEventListener(t,v)});if(c.form)c.form.addEventListener('submit',function(a){if(!v())a.preventDefault()})})(document.currentScript.previousElementSibling);</script>
        <input class="modal-input checkout-wide" name="localidad" placeholder="Localidad (opcional)" maxlength="60"><script>(function(c){if(!c||c.dataset.validacionCampo)return;c.dataset.validacionCampo='1';var e=c.nextElementSibling&&c.nextElementSibling.classList.contains('field-error-inline')?c.nextElementSibling:document.createElement('small');e.className='field-error-inline';e.style.cssText='display:block;color:#c62828;font-size:12px;margin-top:4px;min-height:1em;';if(!e.parentNode)c.insertAdjacentElement('afterend',e);function v(){var x=c.required&&!c.value.trim(),i=x||!c.validity.valid;e.textContent=i?(x?'Este campo es obligatorio.':c.validationMessage):'';c.setAttribute('aria-invalid',i?'true':'false');return!i}['input','change','blur'].forEach(function(t){c.addEventListener(t,v)});if(c.form)c.form.addEventListener('submit',function(a){if(!v())a.preventDefault()})})(document.currentScript.previousElementSibling);</script>
        <select class="modal-input checkout-wide" name="metodo_pago" required>
          <option value="" disabled selected>Método de pago</option>
          <option value="Contraentrega">Contraentrega (efectivo)</option>
          <option value="Transferencia">Transferencia bancaria</option>
          <option value="Nequi">Nequi</option>
          <option value="Daviplata">Daviplata</option>
        </select><script>(function(c){if(!c||c.dataset.validacionCampo)return;c.dataset.validacionCampo='1';var e=c.nextElementSibling&&c.nextElementSibling.classList.contains('field-error-inline')?c.nextElementSibling:document.createElement('small');e.className='field-error-inline';e.style.cssText='display:block;color:#c62828;font-size:12px;margin-top:4px;min-height:1em;';if(!e.parentNode)c.insertAdjacentElement('afterend',e);function v(){var x=c.required&&!c.value.trim(),i=x||!c.validity.valid;e.textContent=i?(x?'Este campo es obligatorio.':c.validationMessage):'';c.setAttribute('aria-invalid',i?'true':'false');return!i}['input','change','blur'].forEach(function(t){c.addEventListener(t,v)});if(c.form)c.form.addEventListener('submit',function(a){if(!v())a.preventDefault()})})(document.currentScript.previousElementSibling);</script>
      </div>
      <p id="checkoutError" class="login-error" hidden></p>
      <button class="btn-modal-primary" type="submit">Registrar pedido</button>
    </form>
  </div>
</div>
