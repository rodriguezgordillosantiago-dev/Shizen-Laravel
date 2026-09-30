<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Notificaciones — Repartidor</title>
    <link rel="stylesheet" href="{{ asset('pages/styles/navigation.css') }}?v=nav-stable-4">
    <style>
        body { margin: 0 auto; max-width: 430px; overflow: hidden; background: #f3f4f6; font-family: "Inter", sans-serif; color: #1f2937; }
        .page { height: 100dvh; overflow-y: auto; padding: 28px 18px 90px; }
        .notifications-header { display: flex; align-items: center; justify-content: space-between; gap: 12px; margin-bottom: 18px; }
        .notifications-header h1 { margin: 0; font-size: 28px; line-height: 1.1; }
        .notifications-header form { flex: 0 1 150px; margin: 0; }
        .notifications-header button { display: block; width: 100%; min-height: 48px; border: 0; border-radius: 12px; padding: 10px 14px; background: #4c9540; color: #fff; font-weight: 700; font-size: 14px; line-height: 1.15; cursor: pointer; }
        .item { background: #fff; border-radius: 14px; padding: 16px; margin: 12px 0; box-shadow: 0 2px 8px #0000000d; }
        .unread { border-left: 4px solid #4c9540; }
        .item h2 { font-size: 15px; margin: 0 0 7px; }
        .item p { font-size: 13px; margin: 0 0 8px; color: #4b5563; }
        .item time { font-size: 11px; color: #9ca3af; }
        .bottom-nav button { float: none; }
        @media (max-width: 390px) {
            .page { padding: 20px 12px 84px; }
            .notifications-header { align-items: stretch; gap: 10px; }
            .notifications-header h1 { font-size: 25px; align-self: center; }
            .notifications-header form { flex: 1 1 auto; max-width: 145px; }
            .notifications-header button { min-height: 52px; padding: 8px 10px; font-size: 13px; }
        }
    </style>
</head>
<body>
<main class="page">
    <div class="notifications-header">
        <h1>Notificaciones</h1>
        @if ($items->where('leida', 0)->count() > 0)
            <form method="post" action="{{ url('/notificaciones') }}">
                @csrf
                <button type="submit">Marcar<br>leídas</button>
            </form>
        @endif
    </div>

    @forelse ($items as $item)
        <article class="item {{ !$item->leida ? 'unread' : '' }}">
            <h2>{{ $item->titulo }}</h2>
            <p>{{ $item->mensaje }}</p>
            <time>{{ $item->fecha_creacion }}</time>
        </article>
    @empty
        <div class="item">No tienes notificaciones todavía.</div>
    @endforelse
</main>

<nav class="bottom-nav" aria-label="Navegación principal">
    <a href="{{ route('inicio') }}" class="nav-item" aria-label="Inicio">
        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10.5 12 3l9 7.5v8.25A2.25 2.25 0 0 1 18.75 21H5.25A2.25 2.25 0 0 1 3 18.75V10.5Z"/></svg>
        <span>Inicio</span>
    </a>
    <a href="{{ route('activos') }}" class="nav-item" aria-label="Activos">
        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m4 7 8-4 8 4-8 4-8-4Zm0 0v10l8 4 8-4V7M12 11v10"/></svg>
        <span>Activos</span>
        <b class="badge" id="badge-activos"></b>
    </a>
    <button id="delivery-status-toggle" class="nav-status-toggle" type="button" aria-pressed="false">
        <span class="nav-status-icon"><svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" d="M12 2v10m0 0 4-4m-4 4-4-4M5 13v4a3 3 0 0 0 3 3h8a3 3 0 0 0 3-3v-4"/></svg></span>
        <span class="nav-status-label">Activo</span>
    </button>
    <a href="{{ url('/notificaciones') }}" class="nav-item is-active" aria-current="page" aria-label="Notificaciones">
        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9Zm-8 13h4"/></svg>
        <span>Notificaciones</span>
        <b class="badge" id="badge-notificaciones"></b>
    </a>
    <a href="{{ route('perfil') }}" class="nav-item" aria-label="Perfil">
        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 1 1-8 0 4 4 0 0 1 8 0Zm-12 14a8 8 0 0 1 16 0"/></svg>
        <span>Perfil</span>
    </a>
</nav>

<script src="{{ asset('js/data.js') }}"></script>
<script>
    iniciarBotonEstadoRepartidor();
    if (typeof updateNavBadges === 'function') updateNavBadges();
</script>
</body>
</html>
