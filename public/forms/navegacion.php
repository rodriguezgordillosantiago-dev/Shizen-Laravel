<?php
if (session_status() === PHP_SESSION_NONE) {
    session_name('SHIZEN_CLIENTE_SESSION');
    session_start();
}
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$logueado      = !empty($_SESSION["id_usuario"]);
$nombreUsuario = htmlspecialchars($logueado ? ($_SESSION['usuario_nombre'] ?? 'Mi cuenta') : '');
$rolUsuario    = strtolower(trim($_SESSION["usuario_rol"] ?? ""));
$csrfToken     = htmlspecialchars((string) $_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8');
$mostrarFavoritos = true;
?>
<nav>
  <div class="nav-inner">
    <a class="nav-logo" href="index.php">
      <img src="assets/logo.png" alt="Shizen" />
    </a>

    <form class="nav-search" method="get" action="php/buscar.php" role="search">
      <label class="sr-only" for="navSearch">Buscar en Shizen</label>
      <input id="navSearch" name="q" type="search" placeholder="¿Qué quieres comer?" value="<?= htmlspecialchars((string)($_GET['q'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"><script>(function(c){if(!c||c.dataset.validacionCampo)return;c.dataset.validacionCampo='1';var e=c.nextElementSibling&&c.nextElementSibling.classList.contains('field-error-inline')?c.nextElementSibling:document.createElement('small');e.className='field-error-inline';e.style.cssText='display:block;color:#c62828;font-size:12px;margin-top:4px;min-height:1em;';if(!e.parentNode)c.insertAdjacentElement('afterend',e);function v(){var x=c.required&&!c.value.trim(),i=x||!c.validity.valid;e.textContent=i?(x?'Este campo es obligatorio.':c.validationMessage):'';c.setAttribute('aria-invalid',i?'true':'false');return!i}['input','change','blur'].forEach(function(t){c.addEventListener(t,v)});if(c.form)c.form.addEventListener('submit',function(a){if(!v())a.preventDefault()})})(document.currentScript.previousElementSibling);</script>
      <button type="submit" aria-label="Buscar">⌕</button>
    </form>
    <div class="nav-right">
      <?php if ($logueado): ?>
        <?php if ($mostrarFavoritos): ?>
          <a class="nav-favorites-button" href="php/favoritos.php" aria-label="Ver favoritos" title="Favoritos">&#9825;</a>
        <?php endif; ?>
        <a class="nav-orders-button" href="php/pedidos.php" aria-label="Ver mis pedidos" title="Mis pedidos">&#128666;</a>
        <button class="nav-orders-button" type="button" onclick="openNotificationModal()" aria-label="Ver notificaciones" title="Notificaciones">&#128276;</button>
        <div class="nav-user-menu">
          <button class="btn-ingreso btn-ingreso--user" id="userMenuBtn" aria-haspopup="true" aria-expanded="false"
                  onclick="openProfileModal()">
            &#128100; <?= $nombreUsuario ?>
          </button>
          <div class="nav-dropdown" role="menu">
            <button class="nav-dropdown-item" type="button" onclick="openProfileModal()">Editar mis datos</button>
            <?php if ($mostrarFavoritos): ?>
              <a class="nav-dropdown-item" href="php/favoritos.php">Favoritos</a>
            <?php endif; ?>
            <a class="nav-dropdown-item" href="php/pedidos.php">Mis pedidos</a>
            <a class="nav-dropdown-item" href="php/notificaciones.php">Notificaciones</a>
            <form class="nav-logout-form" action="auth/logout.php" method="post">
              <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
              <button class="nav-dropdown-item" type="submit">Cerrar sesion</button>
            </form>
          </div>
        </div>
      <?php else: ?>
        <?php if ($mostrarFavoritos): ?>
          <a class="nav-favorites-button" href="/favoritos" aria-label="Inicia sesión para ver favoritos" title="Favoritos">&#9825;</a>
        <?php endif; ?>
        <a class="nav-orders-button" href="php/login.php?redirect=php%2Fpedidos.php" aria-label="Inicia sesión para ver tus pedidos" title="Mis pedidos">&#128666;</a>
        <a class="btn-ingreso" id="ingresoBtn" href="php/login.php" onclick="event.preventDefault(); openAccessModal()">Ingreso</a>
      <?php endif; ?>
      <button class="btn-cart" id="cartBtn" type="button" onclick="openCart(event)" aria-label="Abrir carrito" title="Carrito">
        &#128722; <span id="cartCount">0</span>
      </button>
      <button class="btn-hamburger" id="hamburgerBtn" onclick="toggleMobileMenu()" aria-label="Menu">&#9776;</button>
    </div>
  </div>
  <div class="mobile-menu" id="mobileMenu">
    <?php if ($logueado): ?>
      <button class="mobile-menu-btn" type="button" onclick="openProfileModal(); toggleMobileMenu()">&#128100; Editar mis datos</button>
      <?php if ($mostrarFavoritos): ?>
        <a class="mobile-menu-btn" href="php/favoritos.php">&#9825; Favoritos</a>
      <?php endif; ?>
      <a class="mobile-menu-btn" href="php/pedidos.php">&#128666; Mis pedidos</a>
      <button class="mobile-menu-btn" type="button" onclick="openNotificationModal(); toggleMobileMenu()">&#128276; Notificaciones</button>
      <form class="nav-logout-form" action="auth/logout.php" method="post">
        <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
        <button class="mobile-menu-btn" type="submit">&#128274; Cerrar sesion</button>
      </form>
    <?php else: ?>
      <?php if ($mostrarFavoritos): ?>
        <a class="mobile-menu-btn" href="/favoritos">&#9825; Favoritos</a>
      <?php endif; ?>
      <a class="mobile-menu-btn" href="php/login.php?redirect=php%2Fpedidos.php">&#128666; Mis pedidos</a>
      <a class="mobile-menu-btn" href="php/login.php">&#128272; Iniciar sesion</a>
    <?php endif; ?>
    <a class="mobile-menu-btn" href="php/promociones.php">&#127881; Promociones</a>
    <a class="mobile-menu-btn" href="php/registro_usuario.php">&#128100; Para usuarios</a>
    <a class="mobile-menu-btn" href="php/registro_negocio.php">&#127978; Para negocios</a>
    <a class="mobile-menu-btn" href="php/registro_repartidor.php">&#128691; Para repartidores</a>
    <a class="mobile-menu-btn" href="php/login_negocio.php">&#128188; Ingreso negocios</a>
    <a class="mobile-menu-btn" href="php/login_repartidor.php">&#128694; Ingreso repartidores</a>
  </div>
  <div class="nav-underline"></div>
</nav>
