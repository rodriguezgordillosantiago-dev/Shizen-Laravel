<!doctype html>
<html lang="es">
  <head>
    <meta charset="UTF-8" />
    <meta
      name="viewport"
      content="width=device-width, initial-scale=1.0"
    />
    <title>Promociones | Shizen</title>
    <base href="{{ asset('/') }}" />
    <link
      rel="preconnect"
      href="https://fonts.googleapis.com"
    />
    <link
      rel="preconnect"
      href="https://fonts.gstatic.com"
      crossorigin
    />
    <link
      href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
      rel="stylesheet"
    />
    <link rel="stylesheet" href="css/styles.css" />
    <link rel="stylesheet" href="css/nav.css" />
    <link
      rel="stylesheet"
      href="css/pages.css?v=20260816-1"
    />
    <link
      rel="stylesheet"
      href="css/modals.css?v=20260816-2"
    />
  </head>
  <body>
    <header id="navigation">
      @include('navegacion')
    </header>
    <main id="app-content">
      <section class="promos-page">
        <div class="page-header">
          <button
            class="btn-back back-link"
            onclick="window.location.href = '{{ url('/') }}'"
            aria-label="Volver"
          >
            <svg viewBox="0 0 24 24" aria-hidden="true">
              <path d="M19 12H5M12 19l-7-7 7-7" />
            </svg>
          </button>
          <div>
            <h2>Promociones</h2>
            <p>
              Descuentos exclusivos en restaurantes veganos
            </p>
          </div>
        </div>
        <div class="promos-inner">
          <div class="filter-tabs" id="promoFilterTabs">
            <button type="button" class="filter-tab active" data-cat="all">Todas</button>
            @foreach ($categories as $category)
              <button type="button" class="filter-tab" data-cat="{{ $category->id_categoria }}">
                {{ $category->icon ?: '🌱' }} {{ $category->nombre }}
              </button>
            @endforeach
          </div>
          <div class="promos-grid" id="promosGrid">
            @forelse ($promotions as $promo)
              @php
                $price = !empty($promo->on_promo) && (float) $promo->precio_promocion > 0
                  ? (float) $promo->precio_promocion
                  : (float) $promo->precio;
                $discount = $promo->precio > 0 && $price < $promo->precio
                  ? (int) round((($promo->precio - $price) / $promo->precio) * 100)
                  : 0;
                $cartDish = [
                  'id' => (int) ($promo->id_menu_item ?: $promo->id),
                  'name' => $promo->promo_nombre,
                  'price' => $price,
                  'restaurant' => $promo->negocio_nombre,
                  'businessId' => (int) $promo->id_negocio,
                  'image' => $promo->imagen_url,
                ];
              @endphp
              <article class="promo-card" data-cat="{{ $promo->id_categoria ?? 0 }}">
                <div class="promo-card-img" style="background-image:url('{{ $promo->imagen_url }}')">
                  <span class="promo-badge">{{ $discount > 0 ? $discount . '% OFF' : '🔥 OFERTA' }}</span>
                  <span class="promo-tag-top">{{ $promo->categoria_nombre ?: 'Vegano' }}</span>
                </div>
                <div class="promo-card-body">
                  <h3>{{ $promo->promo_nombre }}</h3>
                  <div class="promo-restaurant">🏬 {{ $promo->negocio_nombre }}</div>
                  @if ($promo->promo_desc)
                    <p class="promo-desc">{{ $promo->promo_desc }}</p>
                  @endif
                  @if ($promo->fecha_fin)
                    <div class="promo-timer">⏱️ Vence: {{ \Illuminate\Support\Carbon::parse($promo->fecha_fin)->format('d/m/Y') }}</div>
                  @endif
                  <div class="promo-prices">
                    <span class="promo-price-new">${{ number_format($price, 0, ',', '.') }}</span>
                    @if ($promo->precio > $price)
                      <span class="promo-price-old">${{ number_format((float) $promo->precio, 0, ',', '.') }}</span>
                      @if ($discount > 0)<span class="promo-discount">-{{ $discount }}%</span>@endif
                    @endif
                  </div>
                  <button class="btn-promo-cart" type="button" onclick='addToCart(<?= e(json_encode($cartDish, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP)) ?>)'>Añadir al carrito 🛒</button>
                </div>
              </article>
            @empty
              <p class="cart-empty" style="grid-column:1/-1;text-align:center;padding:40px 20px;color:#666;font-size:1.05rem">
                No hay promociones activas en este momento. ¡Vuelve pronto para disfrutar de ofertas exclusivas!
              </p>
            @endforelse
          </div>
        </div>
      </section>
    </main>
    <div id="overlays">
      @include('modales')
    </div>
    <script src="{{ asset('js/app.js') }}"></script>
    <script>
      document.querySelectorAll('#promoFilterTabs .filter-tab').forEach(function (tab) {
        tab.addEventListener('click', function () {
          document.querySelectorAll('#promoFilterTabs .filter-tab').forEach(function (item) {
            item.classList.toggle('active', item === tab);
          });
          var selected = tab.getAttribute('data-cat');
          document.querySelectorAll('#promosGrid .promo-card').forEach(function (card) {
            card.style.display = selected === 'all' || card.getAttribute('data-cat') === selected ? '' : 'none';
          });
        });
      });
    </script>
  </body>
</html>
