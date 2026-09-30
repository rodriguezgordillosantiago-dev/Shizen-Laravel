<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Buscar | Shizen</title>
  <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
  <link rel="stylesheet" href="{{ asset('css/nav.css') }}">
  <link rel="stylesheet" href="{{ asset('css/pages.css') }}">
  <link rel="stylesheet" href="{{ asset('css/search.css') }}">
</head>
<body>
  <header>@include('navegacion')</header>
  <main class="search-page">
    <h1>Resultados de búsqueda</h1>
    <p class="search-page-sub">
      @if ($term !== '') Resultados para <strong>“{{ $term }}”</strong>
      @else Escribe algo para encontrar categorías de comida.
      @endif
    </p>
    @if ($categories->isEmpty() && $dishes->isEmpty())
      <div class="search-empty">No encontramos resultados. Prueba con otra búsqueda.</div>
    @else
      @if ($categories->isNotEmpty())
      <h2 class="search-section-title">Categorías</h2>
      <div class="search-results">
        @foreach ($categories as $category)
          <a class="search-result-card" href="{{ url('/categorias') }}?categoria={{ $category->id_categoria }}">
            <span class="search-result-icon">{{ $category->icon }}</span>
            <span><strong>{{ $category->nombre }}</strong><small>{{ $category->descripcion }}</small></span>
          </a>
        @endforeach
      </div>
      @endif
      @if ($dishes->isNotEmpty())
      <h2 class="search-section-title">Platos</h2>
      <div class="search-results">
        @foreach ($dishes as $dish)
          @php
            $hasPromo = !empty($dish->on_promo) && (float) $dish->precio_promocion > 0;
            $cartDish = [
              'id' => (int) $dish->id,
              'name' => $dish->nombre,
              'price' => $hasPromo ? (float) $dish->precio_promocion : (float) $dish->precio,
              'restaurant' => $dish->negocio_nombre ?: 'Restaurante Shizen',
              'businessId' => (int) ($dish->negocio_id ?? 0),
              'image' => $dish->imagen_url,
            ];
          @endphp
          <article class="search-result-card">
            <a class="search-result-content" href="{{ url('/categorias') }}?categoria={{ $dish->categoria_id ?: 1 }}">
              <img class="search-result-image" src="{{ $dish->imagen_url }}" alt="" loading="lazy" decoding="async">
              <span>
                <strong>{{ $dish->nombre }}</strong>
                <small>{{ $dish->negocio_nombre ?: 'Restaurante Shizen' }} · ${{ number_format($cartDish['price'], 0, ',', '.') }}</small>
                <em>{{ $dish->descripcion ?: 'Plato vegano de Shizen.' }}</em>
              </span>
            </a>
            <button class="btn-add-cart" type="button" onclick='addToCart(<?= e(json_encode($cartDish, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP)) ?>)'>Añadir al carrito</button>
          </article>
        @endforeach
      </div>
      @endif
    @endif
  </main>
  <div id="overlays">@include('modales')</div>
  <script src="{{ asset('js/app.js') }}"></script>
</body>
</html>
