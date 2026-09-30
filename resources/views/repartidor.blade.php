<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Shizen Repartidor | {{ ucfirst($section) }}</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('css/courier.css') }}">
</head>
<body>
<main class="courier-shell">
  @if(session('courier_success'))
    <p class="courier-alert">{{ session('courier_success') }}</p>
  @endif
  @if(isset($errors) && $errors->any())
    <p class="courier-alert courier-alert--error">{{ $errors->first() }}</p>
  @endif

  @if($section === 'inicio')
    <section class="courier-header">
      <div class="courier-header__top">
        <div>
          <p class="courier-kicker">Bienvenido de vuelta</p>
          <h1 class="courier-title">{{ $courier->nombre }} {{ $courier->apellido }}</h1>
        </div>
        <img class="courier-logo" src="{{ asset('assets/logo_blanco.png') }}" alt="Shizen">
      </div>
      <div class="courier-stats">
        <div class="courier-stat"><small>Activos</small><strong>{{ $stats['active'] }}</strong></div>
        <div class="courier-stat"><small>Disponibles</small><strong>{{ $stats['available'] }}</strong></div>
        <div class="courier-stat"><small>Calificación</small><strong>{{ $stats['rating'] ? number_format($stats['rating'], 1) : '—' }}</strong></div>
      </div>
    </section>
    <div class="courier-banner">
      <span aria-hidden="true">📦</span>
      <div><strong>{{ $stats['active'] }} pedidos activos</strong><span>{{ $stats['available'] }} pedidos esperan repartidor.</span></div>
    </div>
    <section class="courier-section">
      <h2>Accesos rápidos</h2>
      <p>Gestiona tus entregas desde un solo lugar.</p>
      <div class="courier-actions">
        <a class="courier-button courier-button--primary" href="{{ route('courier.dashboard', 'pedidos') }}">Ver pedidos</a>
        <a class="courier-button courier-button--muted" href="{{ route('courier.dashboard', 'activos') }}">Mis activos</a>
      </div>
    </section>
  @elseif($section === 'perfil')
    <section class="courier-profile">
      <div class="courier-profile__avatar">{{ strtoupper(substr($courier->nombre, 0, 1) . substr($courier->apellido, 0, 1)) }}</div>
      <h1>{{ $courier->nombre }} {{ $courier->apellido }}</h1>
      <p>{{ $courier->email }} · {{ $courier->estado }}</p>
      <form class="courier-form" method="post" action="{{ route('courier.profile.update') }}">
        @csrf
        <label for="nombre">Nombre</label>
        <input id="nombre" name="nombre" value="{{ old('nombre', $courier->nombre) }}" required>
        <label for="apellido">Apellido</label>
        <input id="apellido" name="apellido" value="{{ old('apellido', $courier->apellido) }}" required>
        <label for="vehiculo">Vehículo</label>
        <select id="vehiculo" name="vehiculo" required>
          @foreach(['Bicicleta', 'Moto', 'Patineta electrica', 'Carro'] as $vehicle)
            <option value="{{ $vehicle }}" @selected(old('vehiculo', $courier->vehiculo) === $vehicle)>{{ $vehicle }}</option>
          @endforeach
        </select>
        <button class="courier-button courier-button--primary" type="submit">Guardar cambios</button>
      </form>
      <form method="post" action="{{ route('logout') }}" style="margin-top:10px">
        @csrf
        <button class="courier-button courier-button--muted" type="submit"><svg class="logout-icon" width="18" height="18" fill="currentColor" viewBox="0 0 512 512" aria-hidden="true"><path d="M377.9 105.9L500.7 228.7c7.2 7.2 11.3 17.1 11.3 27.3s-4.1 20.1-11.3 27.3L377.9 406.1c-6.4 6.4-15 9.9-24 9.9c-18.7 0-33.9-15.2-33.9-33.9l0-62.1-128 0c-17.7 0-32-14.3-32-32l0-64c0-17.7 14.3-32 32-32l128 0 0-62.1c0-18.7 15.2-33.9 33.9-33.9c9 0 17.6 3.6 24 9.9zM160 96L96 96c-17.7 0-32 14.3-32 32l0 256c0 17.7 14.3 32 32 32l64 0c17.7 0 32 14.3 32 32s-14.3 32-32 32l-64 0c-53 0-96-43-96-96L0 128C0 75 43 32 96 32l64 0c17.7 0 32 14.3 32 32s-14.3 32-32 32z"></path></svg> Cerrar sesión</button>
      </form>
    </section>
  @else
    @php
      $items = $section === 'activos' ? $active : ($section === 'pedidos' ? $available : $history);
      $isAvailable = $section === 'pedidos';
    @endphp
    <section class="courier-section">
      <h2>{{ $isAvailable ? 'Pedidos disponibles' : ($section === 'activos' ? 'Pedidos activos' : 'Historial') }}</h2>
      <p>{{ $isAvailable ? 'Acepta los pedidos que quieras entregar.' : ($section === 'activos' ? 'Gestiona las entregas en curso.' : 'Tus últimas entregas.') }}</p>
      @forelse($items as $delivery)
        <article class="courier-card">
          <div class="courier-card__head">
            <div class="courier-avatar">{{ strtoupper(substr($delivery->cliente_nombre, 0, 1)) }}</div>
            <div class="courier-card__main">
              <strong>{{ $delivery->cliente_nombre }} {{ $delivery->cliente_apellido }}</strong>
              <span>{{ $delivery->negocio_nombre }} · Pedido #{{ $delivery->id_pedido }}</span>
            </div>
            <div class="courier-total"><strong>${{ number_format($delivery->total, 0, ',', '.') }}</strong><span>{{ $delivery->entrega_estado }}</span></div>
          </div>
          <div class="courier-route">📍 Recoger: {{ $delivery->negocio_nombre }}<br>🏁 Entregar: {{ $delivery->direccion_entrega }}</div>
          @if($isAvailable)
            <form method="post" action="{{ route('courier.delivery.accept', $delivery->id_entrega) }}">
              @csrf
              <button class="courier-button courier-button--primary" type="submit">Aceptar pedido</button>
            </form>
          @elseif($section === 'activos' && !$delivery->fecha_entrega)
            <form method="post" action="{{ route('courier.delivery.advance', $delivery->id_entrega) }}">
              @csrf
              <button class="courier-button courier-button--primary" type="submit">{{ $delivery->entrega_estado === 'En camino' ? 'Marcar entregado' : 'Recogí el pedido' }}</button>
            </form>
          @else
            <span style="color:#2d6a31;font-size:12px;font-weight:700">Entrega completada</span>
          @endif
        </article>
      @empty
        <div class="courier-card courier-empty">No hay pedidos en esta sección.</div>
      @endforelse
    </section>
  @endif

  <nav class="courier-nav" aria-label="Navegación del repartidor">
    <a class="courier-nav__item {{ $section === 'inicio' ? 'is-active' : '' }}" href="{{ route('courier.dashboard', 'inicio') }}">⌂<span>Inicio</span></a>
    <a class="courier-nav__item {{ $section === 'activos' ? 'is-active' : '' }}" href="{{ route('courier.dashboard', 'activos') }}">▣<span>Activos</span></a>
    <form method="post" action="{{ route('courier.status') }}">
      @csrf
      <button class="courier-nav__item courier-nav__item--status {{ $online ? 'is-online' : '' }}" type="submit" aria-label="{{ $online ? 'Desactivar disponibilidad' : 'Activar disponibilidad' }}">
        <span class="courier-status-icon">{{ $online ? '✓' : '×' }}</span><span>{{ $online ? 'Activo' : 'Inactivo' }}</span>
      </button>
    </form>
    <a class="courier-nav__item {{ $section === 'pedidos' ? 'is-active' : '' }}" href="{{ route('courier.dashboard', 'pedidos') }}">□<span>Pedidos</span></a>
    <a class="courier-nav__item {{ $section === 'perfil' ? 'is-active' : '' }}" href="{{ route('courier.dashboard', 'perfil') }}">♙<span>Perfil</span></a>
  </nav>
</main>
</body>
</html>
