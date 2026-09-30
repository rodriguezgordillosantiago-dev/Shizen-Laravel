<?php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_name('SHIZEN_CLIENTE_SESSION');
    session_start();
}
if (empty($_SESSION['id_usuario'])) {
    header('Location: login.php?redirect=php%2Fnotificaciones.php');
    exit;
}

require_once __DIR__ . '/../BD/conexion.php';
$db = obtenerConexion();
$userId = (int)$_SESSION['id_usuario'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db->prepare('UPDATE notificacion SET leida = 1 WHERE id_usuario = :usuario')
       ->execute(['usuario' => $userId]);
    header('Location: notificaciones.php');
    exit;
}

$stmt = $db->prepare(
    'SELECT id_notificacion, id_pedido, tipo, titulo, mensaje, leida, fecha_creacion
       FROM notificacion
      WHERE id_usuario = :usuario AND audiencia = "cliente"
      ORDER BY fecha_creacion DESC'
);
$stmt->execute(['usuario' => $userId]);
$notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (isset($_GET['json']) || (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false)) {
    header('Content-Type: application/json');
    $unreadCount = count(array_filter($notifications, fn(array $n): bool => !(bool)$n['leida']));
    echo json_encode([
        'success' => true,
        'unread_count' => $unreadCount,
        'notifications' => $notifications
    ]);
    exit;
}
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Notificaciones | Shizen</title>
  <link rel="stylesheet" href="../css/styles.css">
  <style>
    body{background:#f4faf4}.notifications{max-width:760px;margin:35px auto;padding:0 18px}
    .notification{background:#fff;border-radius:14px;padding:18px;margin:12px 0;box-shadow:0 2px 10px #0000000d}
    .notification.unread{border-left:4px solid #4c9540}.notification h2{font-size:1rem;margin:0 0 6px}
    .notification p{margin:0 0 8px;color:#555}.notification time{font-size:.8rem;color:#888}
    .empty{background:#fff;border-radius:14px;padding:30px;text-align:center;color:#777}
  </style>
</head>
<body>
  <header><?php require __DIR__ . '/../forms/navegacion.php'; ?></header>
  <main class="notifications">
    <div style="display:flex;justify-content:space-between;align-items:center;gap:12px">
      <h1>Notificaciones</h1>
      <?php if (array_filter($notifications, fn(array $n): bool => !(bool)$n['leida'])): ?>
        <form method="post"><button class="btn-modal-primary" type="submit">Marcar como leídas</button></form>
      <?php endif; ?>
    </div>
    <?php if (!$notifications): ?>
      <div class="empty">No tienes notificaciones todavía.</div>
    <?php else: foreach ($notifications as $notification): ?>
      <article class="notification <?= !$notification['leida'] ? 'unread' : '' ?>">
        <h2><?= htmlspecialchars($notification['titulo'], ENT_QUOTES, 'UTF-8') ?></h2>
        <p><?= htmlspecialchars($notification['mensaje'], ENT_QUOTES, 'UTF-8') ?></p>
        <time><?= htmlspecialchars($notification['fecha_creacion'], ENT_QUOTES, 'UTF-8') ?></time>
      </article>
    <?php endforeach; endif; ?>
  </main>
</body>
</html>
