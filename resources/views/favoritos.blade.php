<!doctype html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Favoritos | Shizen</title>
  <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
  <link rel="stylesheet" href="{{ asset('css/nav.css') }}">
  <link rel="stylesheet" href="{{ asset('css/modals.css') }}">
  <link rel="stylesheet" href="{{ asset('css/favorites.css') }}">
</head>
<body>
  <header>@include('navegacion')</header>
  <main class="favorites-page">
    <a class="menu-back back-link" href="{{ url('/') }}">← Volver al inicio</a>
    <h1>Mis favoritos</h1>
    <p class="favorites-subtitle">Tus negocios y platos favoritos, siempre a mano.</p>
    <h2>Restaurantes favoritos</h2>
    <div class="favorites-grid">
      @forelse ($businesses as $business)
        <div class="favorite-card-wrap" style="position:relative">
          <a class="favorite-card" href="{{ route('business.menu', $business->id) }}">
            <img src="{{ $business->logo_url }}" alt="Logo de {{ $business->nombre }}" loading="lazy" decoding="async">
            <div><strong>{{ $business->nombre }}</strong><span>★ {{ number_format($business->rating, 1) }}</span></div>
          </a>
          <form method="post" action="{{ route('favorites.toggle') }}" style="position:absolute;top:12px;right:12px">
            @csrf
            <input type="hidden" name="id_negocio" value="{{ $business->id }}">
            <input type="hidden" name="redirect" value="/favoritos">
            <button class="favorite-button is-favorite" type="submit" aria-label="Quitar {{ $business->nombre }} de favoritos">♥</button>
          </form>
        </div>
      @empty
        <p class="favorites-empty">Aún no tienes restaurantes favoritos.</p>
      @endforelse
    </div>
    @if ($dishes->isNotEmpty())
      <h2>Platos favoritos</h2>
      <div class="favorites-grid">
        @foreach ($dishes as $dish)
          <div class="favorite-card-wrap" style="position:relative">
            <a class="favorite-card" href="{{ url('/categorias') }}?categoria={{ $dish->id_categoria ?: 1 }}">
              <img src="{{ $dish->imagen_url }}" alt="Imagen de {{ $dish->nombre }}" loading="lazy" decoding="async">
              <div>
                <strong>{{ $dish->nombre }}</strong>
                <small>{{ $dish->negocio_nombre ?: 'Shizen' }}</small>
                <span>${{ number_format((float) $dish->precio, 0, ',', '.') }}</span>
              </div>
            </a>
            <form method="post" action="{{ route('favorites.toggle') }}" style="position:absolute;top:12px;right:12px">
              @csrf
              <input type="hidden" name="id_menu_item" value="{{ $dish->id }}">
              <input type="hidden" name="redirect" value="/favoritos">
              <button class="favorite-button is-favorite" type="submit" aria-label="Quitar {{ $dish->nombre }} de favoritos">♥</button>
            </form>
          </div>
        @endforeach
      </div>
    @endif
  </main>
  <script src="{{ asset('js/app.js') }}"></script>
</body>
</html>
