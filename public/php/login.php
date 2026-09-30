<?php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_name('SHIZEN_CLIENTE_SESSION');
    session_start();
}

if (!empty($_SESSION['id_usuario'])) {
    header('Location: ../index.php');
    exit;
}

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$error    = isset($_GET['error'])   && $_GET['error']   === '1';
$expired  = isset($_GET['expired']) && $_GET['expired'] === '1';
$redirect = (string)($_GET['redirect'] ?? '');
$redirect = preg_match('#^(?:php/)?[A-Za-z0-9_-]+\.php(?:\?[A-Za-z0-9_=&%-]*)?$#', $redirect) ? $redirect : '';
$tipo = in_array($_GET['tipo'] ?? '', ['negocio', 'repartidor'], true) ? (string)$_GET['tipo'] : 'usuario';
$tipoLabel = ['usuario' => 'cliente', 'negocio' => 'negocio', 'repartidor' => 'repartidor'][$tipo];
$csrfToken = htmlspecialchars((string)$_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8');
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Iniciar sesion | Shizen</title>
  <base href="../" />
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="css/styles.css" />
  <link rel="stylesheet" href="css/nav.css?v=20260905-2" />
  <link rel="stylesheet" href="css/modals.css?v=20260816-2" />
  <style>
    body { min-height:100vh; background:linear-gradient(135deg,#f0fdf4 0%,#dcfce7 100%); display:flex; flex-direction:column; }
    .login-page { flex:1; display:flex; align-items:center; justify-content:center; padding:40px 20px; }
    .login-card { background:#fff; border-radius:22px; padding:40px 36px; width:100%; max-width:420px; box-shadow:0 20px 60px rgba(0,0,0,0.12); }
    .login-logo  { display:block; width:110px; height:56px; object-fit:contain; margin:0 auto 20px; }
    .login-title { text-align:center; font-size:1.4rem; font-weight:800; color:#1b3a1d; margin-bottom:6px; }
    .login-sub   { text-align:center; color:#888; font-size:0.93rem; margin-bottom:24px; }
    .login-error { background:#fef2f2; border:1px solid #fecaca; color:#b91c1c; border-radius:10px; padding:10px 14px; font-size:0.9rem; margin-bottom:16px; }
    .login-info  { background:#fff8e1; border:1px solid #ffe082; color:#92400e; border-radius:10px; padding:10px 14px; font-size:0.9rem; margin-bottom:16px; }
    .login-divider { text-align:center; color:#aaa; font-size:0.85rem; margin:20px 0 12px; }
    .login-register { display:block; text-align:center; color:#3a8c3f; font-size:0.92rem; font-weight:600; text-decoration:none; }
    .login-register:hover { text-decoration:underline; }
    .login-back { display:inline-flex; align-items:center; gap:6px; color:#3a8c3f; font-size:0.9rem; font-weight:600; text-decoration:none; margin-bottom:24px; }
    .login-back svg { width:18px; height:18px; fill:none; stroke:currentColor; stroke-width:2; stroke-linecap:round; stroke-linejoin:round; }
    .login-back:hover { text-decoration:underline; }
    .login-terms { color:#6b7280; font-size:.8rem; text-align:center; margin:16px 0 0; }
    .login-terms a { color:#3a8c3f; font-weight:600; }
    .login-roles { display:flex; justify-content:center; gap:10px; flex-wrap:wrap; margin:12px 0 20px; font-size:.82rem; }
    .login-roles a { color:#3a8c3f; text-decoration:none; }
    .login-roles a:hover { text-decoration:underline; }
  </style>
</head>
<body>
  <header id="navigation">
    <?php include __DIR__ . '/../forms/navegacion.php'; ?>
  </header>
  <main class="login-page">
    <div class="login-card">
      <a class="login-back" href="index.php">
        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
        Volver al inicio
      </a>
      <img class="login-logo" src="assets/logo.png" alt="Shizen" />
      <div class="login-title">Bienvenido de vuelta</div>
      <div class="login-sub">Accede a tu cuenta Shizen de <?= htmlspecialchars($tipoLabel, ENT_QUOTES, 'UTF-8') ?></div>
      <nav class="login-roles" aria-label="Tipo de cuenta">
        <a href="login.php?tipo=usuario">Cliente</a>
        <a href="login_negocio.php">Negocio</a>
        <a href="login_repartidor.php">Repartidor</a>
      </nav>

      <?php if ($error): ?>
        <p class="login-error">Credenciales inválidas, inténtalo de nuevo</p>
      <?php endif; ?>
      <?php if ($expired): ?>
        <p class="login-info">Tu sesion expiro. Inicia sesion de nuevo.</p>
      <?php endif; ?>

      <form method="post" action="auth/login.php">
        <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
        <input type="hidden" name="redirect" value="<?= htmlspecialchars($redirect, ENT_QUOTES, 'UTF-8') ?>">
        <div class="modal-form-group">
          <label>Email</label>
          <input class="modal-input" name="email" type="email" placeholder="tu@email.com" autocomplete="email" required />
<script>
(function () {
  const field = document.currentScript.previousElementSibling;
  const message = document.createElement('small');
  message.className = 'field-error-inline';
  message.style.cssText = 'display:block;color:#dc2626;font-size:12px;margin-top:6px';
  field.insertAdjacentElement('afterend', message);
  function validate() {
    
    const valid = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(field.value.trim());
    field.setCustomValidity(valid ? '' : 'Ingresa un correo v?lido.');
    message.textContent = field.dataset.touched === 'true' && !valid ? 'Ingresa un correo v?lido.' : '';
  }
  field.addEventListener('input', function () { field.dataset.touched = 'true'; validate(); });
  field.addEventListener('blur', function () { field.dataset.touched = 'true'; validate(); });
  field.form.addEventListener('submit', function () { field.dataset.touched = 'true'; validate(); });
}());
</script>
        </div>
        <div class="modal-form-group">
          <label>Contrasena</label>
          <div class="password-field">
            <input class="modal-input" id="lp" name="password" type="password" placeholder="Contrasena" autocomplete="current-password" minlength="6" required />
<script>
(function () {
  const field = document.currentScript.previousElementSibling;
  const message = document.createElement('small');
  message.className = 'field-error-inline';
  message.style.cssText = 'display:block;color:#dc2626;font-size:12px;margin-top:6px';
  field.insertAdjacentElement('afterend', message);
  function validate() {
    
    const valid = field.value.length >= 6;
    field.setCustomValidity(valid ? '' : 'La contrase?a debe tener m?nimo 6 caracteres.');
    message.textContent = field.dataset.touched === 'true' && !valid ? 'La contrase?a debe tener m?nimo 6 caracteres.' : '';
  }
  field.addEventListener('input', function () { field.dataset.touched = 'true'; validate(); });
  field.addEventListener('blur', function () { field.dataset.touched = 'true'; validate(); });
  field.form.addEventListener('submit', function () { field.dataset.touched = 'true'; validate(); });
}());
</script>
            <button class="password-toggle" type="button"
              onclick="var i=document.getElementById('lp'); i.type=i.type==='password'?'text':'password';"
              aria-label="Mostrar">&#128065;</button>
          </div>
        </div>
        <button class="btn-modal-primary" type="submit" style="margin-top:10px;">Ingresar</button>
      </form>

      <p class="login-terms">Al ingresar aceptas los <a href="../legal/terminos.html" target="_blank" rel="noopener">Términos y Condiciones</a>.</p>
      <div class="login-divider">Nuevo en Shizen?</div>
    </div>
  </main>
</body>
</html>
