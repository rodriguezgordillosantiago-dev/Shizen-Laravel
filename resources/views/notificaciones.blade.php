<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Notificaciones | Shizen</title>
  <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
  <link rel="stylesheet" href="{{ asset('css/nav.css') }}">
  <link rel="stylesheet" href="{{ asset('css/modals.css') }}">
  <style>
    body { background: #f4faf4; }
    .notifications { max-width: 760px; margin: 35px auto; padding: 0 18px; }
    .notification { background: #fff; border-radius: 14px; padding: 18px; margin: 12px 0; box-shadow: 0 2px 10px #0000000d; }
    .notification.unread { border-left: 4px solid #4c9540; }
    .notification h2 { font-size: 1rem; margin: 0 0 6px; }
    .notification p { margin: 0 0 8px; color: #555; }
    .notification time { font-size: .8rem; color: #888; }
  </style>
</head>
<body>
  <header>@include('navegacion')</header>
  <main class="notifications">
    <h1>Notificaciones</h1>
    @if ($notifications->contains(fn ($notification) => !(bool) $notification->leida))
      <form method="post" action="{{ url('/php/notificaciones.php') }}">
        @csrf
        <button class="btn-modal-primary" type="submit">Marcar como leídas</button>
      </form>
    @endif
    @forelse ($notifications as $notification)
      <article class="notification {{ !$notification->leida ? 'unread' : '' }}">
        <h2>{{ $notification->titulo }}</h2>
        <p>{{ $notification->mensaje }}</p>
        <time>{{ $notification->fecha_creacion }}</time>
      </article>
    @empty
      <p class="favorites-empty">No tienes notificaciones todavía.</p>
    @endforelse
  </main>
  <script src="{{ asset('js/app.js') }}"></script>
</body>
</html>
