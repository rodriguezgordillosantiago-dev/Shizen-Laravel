<?php
$authUser = session('auth_user');
$logueado = is_array($authUser) && !empty($authUser['id_usuario']);
$nombreUsuario = e((string) ($authUser['nombre'] ?? 'Mi cuenta'));
$avatarFiles = glob(public_path('assets/Perfil/*.{png,jpg,jpeg,webp}'), GLOB_BRACE) ?: [];
$avatarDefault = $avatarFiles ? asset('assets/Perfil/' . basename($avatarFiles[0])) : '';
$avatarUsuario = (string) ($authUser['avatar'] ?? $avatarDefault);
$businessUrl = rtrim((string) config('services.shizen.business_url'), '/');
$courierUrl = rtrim((string) config('services.shizen.courier_url'), '/');
?>
<script>
  window.shizenUser = <?= json_encode($logueado ? [
      'id' => (int) $authUser['id_usuario'],
      'nombre' => (string) ($authUser['nombre'] ?? ''),
  ] : null, JSON_UNESCAPED_SLASHES) ?>;
  window.shizenSyncCartUrl = <?= json_encode(url('/php/sync_carrito.php'), JSON_UNESCAPED_SLASHES) ?>;
  window.shizenNotifUrl = <?= json_encode(url('/php/notificaciones.php?json=1'), JSON_UNESCAPED_SLASHES) ?>;
  window.shizenCsrfToken = <?= json_encode(csrf_token(), JSON_UNESCAPED_SLASHES) ?>;
</script>
<nav class="shizen-nav">
  <div class="nav-inner">
    <a class="nav-logo" href="<?= e(url('/')) ?>" aria-label="Shizen, inicio">
      <img src="<?= e(asset('assets/logo.png')) ?>" alt="Shizen" />
    </a>

    <form class="nav-search" method="get" action="<?= e(route('search')) ?>" role="search">
      <label class="sr-only" for="navSearch">Buscar en Shizen</label>
      <input id="navSearch" name="q" type="search" placeholder="¿Qué quieres comer?" value="<?= e(request('q', '')) ?>">
      <button type="submit" aria-label="Buscar">⌕</button>
    </form>

    <div class="nav-right">
      <?php if ($logueado): ?>
        <div class="nav-user-menu">
          <div class="nav-user-actions" id="navUserActions" aria-hidden="true">
            <button class="nav-tool-button" type="button" onclick="openCart(event)" aria-label="Abrir carrito" title="Carrito">
              <svg class="cartIcon" viewBox="0 0 576 512" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"><path d="M0 24C0 10.7 10.7 0 24 0H69.5c22 0 41.5 12.8 50.6 32h411c26.3 0 45.5 25 38.6 50.4l-41 152.3c-8.5 31.4-37 53.3-69.5 53.3H170.7l5.4 28.5c2.2 11.3 12.1 19.5 23.6 19.5H488c13.3 0 24 10.7 24 24s-10.7 24-24 24H199.7c-34.6 0-64.3-24.6-70.7-58.5L77.4 54.5c-.7-3.8-4-6.5-7.9-6.5H24C10.7 48 0 37.3 0 24zM128 464a48 48 0 1 1 96 0 48 48 0 1 1 -96 0zm336-48a48 48 0 1 1 0 96 48 48 0 1 1 0-96z"/></svg>
              <span id="cartCountMenu">0</span>
            </button>
            <a class="nav-tool-button" href="<?= e(route('orders')) ?>" aria-label="Mis pedidos" title="Mis pedidos">&#128666;</a>
            <button class="nav-tool-button" type="button" onclick="openNotificationModal()" aria-label="Notificaciones" title="Notificaciones">
              <svg class="notificationIcon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" aria-hidden="true"><path fill="none" d="M0 0h24v24H0z"/><path fill="currentColor" d="M20 17h2v2H2v-2h2v-7a8 8 0 1 1 16 0v7zm-2 0v-7a6 6 0 1 0-12 0v7h12zm-9 4h6v2H9v-2z"/></svg>
            </button>
            <form class="nav-logout-form" action="<?= e(route('logout')) ?>" method="post">
              <?= csrf_field() ?>
              <button class="Btn nav-logout-button" type="submit" aria-label="Cerrar sesión" title="Cerrar sesión">
                <span class="sign" aria-hidden="true"><svg viewBox="0 0 512 512"><path d="M377.9 105.9L500.7 228.7c7.2 7.2 11.3 17.1 11.3 27.3s-4.1 20.1-11.3 27.3L377.9 406.1c-6.4 6.4-15 9.9-24 9.9c-18.7 0-33.9-15.2-33.9-33.9l0-62.1-128 0c-17.7 0-32-14.3-32-32l0-64c0-17.7 14.3-32 32-32l128 0 0-62.1c0-18.7 15.2-33.9 33.9-33.9c9 0 17.6 3.6 24 9.9zM160 96L96 96c-17.7 0-32 14.3-32 32l0 256c0 17.7 14.3 32 32 32l64 0c17.7 0 32 14.3 32 32s-14.3 32-32 32l-64 0c-53 0-96-43-96-96L0 128C0 75 43 32 96 32l64 0c17.7 0 32 14.3 32 32s-14.3 32-32 32z"></path></svg></span>
              </button>
            </form>
          </div>
          <button class="nav-user-toggle" id="userMenuToggle" type="button" aria-label="Mostrar accesos de usuario" aria-expanded="false" aria-controls="navUserActions" onclick="toggleNavUserMenu(event)">
            <svg aria-hidden="true" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="m13 4.5-5.5 5.5 5.5 5.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
          </button>
          <a class="nav-avatar-link" href="<?= e(route('profile')) ?>" aria-label="Ir a mi perfil" title="Mi perfil">
            <?php if ($avatarUsuario !== ''): ?>
              <img class="nav-avatar-image" src="<?= e($avatarUsuario) ?>" alt="Foto de perfil" width="34" height="34">
            <?php else: ?>
              <span class="nav-avatar-fallback" aria-hidden="true">&#128100;</span>
            <?php endif; ?>
            <span class="nav-notification-dot" id="notifBadge" aria-label="Tienes notificaciones sin leer" title="Tienes notificaciones sin leer" hidden></span>
          </a>
        </div>
      <?php else: ?>
        <a class="nav-orders-button" href="<?= e(url('/login?redirect=/pedidos')) ?>" aria-label="Inicia sesión para ver tus pedidos" title="Mis pedidos">&#128666;</a>
        <a class="btn-ingreso" id="ingresoBtn" href="<?= e(url('/login')) ?>" onclick="event.preventDefault(); openAccessModal()">Ingreso</a>
      <?php endif; ?>
      <button class="btn-hamburger" id="hamburgerBtn" type="button" onclick="toggleNavMobileMenu()" aria-label="Abrir menú" aria-controls="mobileMenu" aria-expanded="false">
        <svg aria-hidden="true" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M4 6h16M4 12h16M4 18h16" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
      </button>
    </div>
  </div>
  <div class="mobile-menu" id="mobileMenu">
    <?php if ($logueado): ?>
      <a class="mobile-menu-btn mobile-profile-link" href="<?= e(route('profile')) ?>">
        <span class="mobile-avatar-wrap"><?php if ($avatarUsuario !== ''): ?><img src="<?= e($avatarUsuario) ?>" alt="" width="28" height="28"><?php else: ?>&#128100;<?php endif; ?></span>
        Mi perfil <span class="mobile-user-name"><?= $nombreUsuario ?></span>
      </a>
      <a class="mobile-menu-btn" href="<?= e(route('orders')) ?>">&#128666; Mis pedidos</a>
      <a class="mobile-menu-btn" href="<?= e(route('notifications')) ?>"><svg class="notificationIcon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" aria-hidden="true"><path fill="none" d="M0 0h24v24H0z"/><path fill="currentColor" d="M20 17h2v2H2v-2h2v-7a8 8 0 1 1 16 0v7zm-2 0v-7a6 6 0 1 0-12 0v7h12zm-9 4h6v2H9v-2z"/></svg> Notificaciones</a>
      <form class="nav-logout-form" action="<?= e(route('logout')) ?>" method="post">
        <?= csrf_field() ?>
        <button class="mobile-menu-btn" type="submit"><svg class="logout-icon" width="18" height="18" fill="currentColor" viewBox="0 0 512 512" aria-hidden="true"><path d="M377.9 105.9L500.7 228.7c7.2 7.2 11.3 17.1 11.3 27.3s-4.1 20.1-11.3 27.3L377.9 406.1c-6.4 6.4-15 9.9-24 9.9c-18.7 0-33.9-15.2-33.9-33.9l0-62.1-128 0c-17.7 0-32-14.3-32-32l0-64c0-17.7 14.3-32 32-32l128 0 0-62.1c0-18.7 15.2-33.9 33.9-33.9c9 0 17.6 3.6 24 9.9zM160 96L96 96c-17.7 0-32 14.3-32 32l0 256c0 17.7 14.3 32 32 32l64 0c17.7 0 32 14.3 32 32s-14.3 32-32 32l-64 0c-53 0-96-43-96-96L0 128C0 75 43 32 96 32l64 0c17.7 0 32 14.3 32 32s-14.3 32-32 32z"></path></svg> Cerrar sesión</button>
      </form>
    <?php else: ?>
      <a class="mobile-menu-btn" href="<?= e(url('/login?redirect=/pedidos')) ?>">&#128666; Mis pedidos</a>
      <a class="mobile-menu-btn" href="<?= e(url('/login')) ?>">&#128272; Iniciar sesión</a>
    <?php endif; ?>
    <a class="mobile-menu-btn" href="<?= e(url('/promociones')) ?>">&#127881; Promociones</a>
    <a class="mobile-menu-btn" href="<?= e(url('/registro-usuario')) ?>">&#128100; Para usuarios</a>
    <a class="mobile-menu-btn" href="<?= e(url('/registro-negocio')) ?>">&#127978; Para negocios</a>
    <a class="mobile-menu-btn" href="<?= e(url('/registro-repartidor')) ?>">&#128691; Para repartidores</a>
    <a class="mobile-menu-btn" href="<?= e($businessUrl . '/login') ?>">&#128188; Ingreso negocios</a>
    <a class="mobile-menu-btn" href="<?= e($courierUrl . '/') ?>">&#128694; Ingreso repartidores</a>
  </div>
  <div class="nav-underline"></div>
</nav>
<script>
  function toggleNavUserMenu(event) {
    event.stopPropagation();
    var menu = document.querySelector('.nav-user-menu');
    var button = document.getElementById('userMenuToggle');
    if (!menu || !button) return;
    var isOpen = menu.classList.toggle('open');
    button.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
    var actions = document.getElementById('navUserActions');
    if (actions) actions.setAttribute('aria-hidden', isOpen ? 'false' : 'true');
  }

  function toggleNavMobileMenu() {
    var menu = document.getElementById('mobileMenu');
    var button = document.getElementById('hamburgerBtn');
    if (!menu || !button) return;
    var isOpen = menu.classList.toggle('open');
    button.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
  }

  document.addEventListener('click', function (event) {
    var menu = document.querySelector('.nav-user-menu');
    var button = document.getElementById('userMenuToggle');
    if (menu && button && !menu.contains(event.target)) {
      menu.classList.remove('open');
      button.setAttribute('aria-expanded', 'false');
      var actions = document.getElementById('navUserActions');
      if (actions) actions.setAttribute('aria-hidden', 'true');
    }
  });

  document.addEventListener('keydown', function (event) {
    if (event.key !== 'Escape') return;
    var menu = document.querySelector('.nav-user-menu');
    var button = document.getElementById('userMenuToggle');
    if (menu && button) {
      menu.classList.remove('open');
      button.setAttribute('aria-expanded', 'false');
      var actions = document.getElementById('navUserActions');
      if (actions) actions.setAttribute('aria-hidden', 'true');
    }
  });
</script>
