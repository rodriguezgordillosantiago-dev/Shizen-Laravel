<div class="view active" id="view-home">
  <section class="hero-section">
    <h1>
      Si tienes
      <span> Shizen </span>
      , tienes Todo.
    </h1>
    <p>
      La plataforma de comida vegana que aspira a ser la más
      grande de Colombia. Descubre restaurantes, rastreo en
      tiempo real y envio gratis durante la beta.
    </p>
    <div class="hero-pills">
      <a
        class="hero-pill pill-outline"
        href="{{ url('/promociones') }}"
      >
        Ver promociones 🎉
      </a>
    </div>
  </section>
  <section class="info-section textured">
    <div class="info-bg"></div>
    <div class="info-overlay"></div>
    <div class="info-content">
      <h2>
        Nuestra misión: alimentar Colombia con conciencia
      </h2>
      <p>
        Conectamos a restaurantes veganos, comedores
        conscientes y repartidores éticos para llevar comida
        plant-based a cada rincón del país, con
        transparencia y compromiso ambiental.
      </p>
      <div class="glass-cards">
        <div class="glass-card">
          <div class="card-icon">🗺</div>
          <h3>Mapa en tiempo real</h3>
          <p>
            Sigue tu pedido en el mapa y sabe exactamente
            cuándo llegará tu comida.
          </p>
        </div>
        <div class="glass-card">
          <div class="card-icon">📱</div>
          <h3>Seguimiento inteligente</h3>
          <p>
            Notificaciones al instante desde que el chef
            empieza hasta que tocan tu puerta.
          </p>
        </div>
        <div class="glass-card">
          <div class="card-icon">♻</div>
          <h3>Compromiso sostenible</h3>
          <p>
            Empaques biodegradables, rutas eficientes y cero
            plástico de un solo uso.
          </p>
        </div>
      </div>
      <div class="stat-cards">
        <div class="stat-card">
          <div class="stat-icon">📍</div>
          <div class="stat-text">
            Bogotá &amp; ciudades principales
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon">🚵</div>
          <div class="stat-text">Envío gratis en beta</div>
        </div>
        <div class="stat-card">
          <div class="stat-icon">🌱</div>
          <div class="stat-text">100% Plant-based</div>
        </div>
      </div>
    </div>
  </section>
  <section
    class="category-band"
    aria-labelledby="category-band-title"
  >
    <div class="category-band-inner">
      <h2 id="category-band-title">¿Qué quieres comer hoy?</h2>
      <p class="category-band-sub">
        Explora negocios y categorías para encontrar tu próxima comida
        vegana favorita.
      </p>
      <h3 class="category-subtitle">Categorías</h3>
      <p class="category-section-text">Elige una categoría y descubre lo mejor de la cocina vegana colombiana.</p>
      @php
        $categoryThemes = ['card-comidas', 'card-cenas', 'card-bowls', 'card-postres', 'card-bebidas', 'card-desayunos', 'card-todos'];
        $categorySpotlights = ['rgba(255,100,0,.40)', 'rgba(168,0,255,.38)', 'rgba(0,180,216,.38)', 'rgba(247,37,133,.40)', 'rgba(0,100,255,.38)', 'rgba(255,160,0,.40)', 'rgba(99,102,241,.35)'];
      @endphp
      <div class="filter-section">
        <div class="filter-track-wrapper">
          <div class="filter-track" id="filterTrack" role="region" aria-label="Categorías de platos">
            @forelse ($categories as $index => $category)
              <a class="filter-card {{ $categoryThemes[$index % count($categoryThemes)] }}" href="{{ route('categories', ['categoria' => $category->id_categoria]) }}" aria-label="Ver {{ $category->platos_count }} platos de {{ $category->nombre }}">
                <span class="card-ring" aria-hidden="true"></span>
                <span class="card-count"><span>{{ $category->platos_count }}</span></span>
                <span class="card-emoji-area">
                  <span class="card-spotlight" style="background:{{ $categorySpotlights[$index % count($categorySpotlights)] }}" aria-hidden="true"></span>
                  <span class="card-emoji">{{ $category->icon ?: '🥗' }}</span>
                </span>
                <span class="card-bottom">
                  <span class="card-label">{{ $category->nombre }}</span>
                  <span class="card-pill">Ver platos</span>
                </span>
              </a>
            @empty
              <p class="cat-empty">No hay categorías disponibles todavía.</p>
            @endforelse
          </div>
        </div>
      </div>
      <div class="business-inline" aria-labelledby="business-band-title">
        <h3 id="business-band-title">Negocios mejor calificados</h3>
        <p class="business-band-sub">Descubre los favoritos de nuestra comunidad.</p>
        <div class="business-scroll">
          @forelse ($businesses as $business)
            <a class="business-card" href="{{ route('business.menu', $business->id) }}">
              <img src="{{ $business->logo_url }}" alt="Logo de {{ $business->nombre }}" loading="lazy" decoding="async">
              <span class="business-card-info">
                <strong>{{ $business->nombre }}</strong>
                <span class="business-rating">★ {{ $business->rating > 0 ? number_format($business->rating, 1) : 'Nuevo' }}</span>
              </span>
            </a>
          @empty
            <p>No hay negocios disponibles todavía.</p>
          @endforelse
        </div>
      </div>
    </div>
  </section>
  <section class="join-section" id="registro" tabindex="-1">
    <div class="section-header">
      <h2>Únete a Shizen</h2>
      <p>
        Haz parte del movimiento de comida consciente que
        aspira a ser el más grande de Colombia.
      </p>
    </div>
    <div class="join-grid">
      <div class="join-card">
        <div
          class="join-card-img"
          style="
            background-image: url(&quot;https://images.unsplash.com/photo-1572715376701-98568319fd0b?w=800&h=500&fit=crop&auto=format&quot;);
          "
        >
          <div class="join-card-img-overlay"></div>
          <span
            class="join-card-tag"
            style="background: #388e3c"
          >
            Para usuarios
          </span>
        </div>
        <div class="join-card-body">
          <h3>Crea tu cuenta</h3>
          <p>
            Descubre negocios veganos, promociones y
            domicilios en toda Colombia.
          </p>
          <a
            class="btn-join"
            href="{{ url('/registro-usuario') }}"
          >
            Crear cuenta
          </a>
        </div>
      </div>
      <div class="join-card">
        <div
          class="join-card-img"
          style="
            background-image: url(&quot;https://images.unsplash.com/photo-1542838132-92c53300491e?w=800&h=500&fit=crop&auto=format&quot;);
          "
        >
          <div class="join-card-img-overlay"></div>
          <span
            class="join-card-tag"
            style="background: #de231f"
          >
            Para negocios
          </span>
        </div>
        <div class="join-card-body">
          <h3>Registra tu negocio</h3>
          <p>
            Accede a millones de usuarios de Shizen y
            disfruta de una logística inmediata sin salir de
            tu tienda.
          </p>
          <a
            class="btn-join"
            href="{{ url('/registro-negocio') }}"
          >
            Empezar registro
          </a>
        </div>
      </div>
      <div class="join-card">
        <div
          class="join-card-img"
          style="
            background-image: url(&quot;https://images.unsplash.com/photo-1611068562065-994ba66501ba?w=800&h=500&fit=crop&auto=format&quot;);
          "
        >
          <div class="join-card-img-overlay"></div>
          <span
            class="join-card-tag"
            style="background: #f57c00"
          >
            Para repartidores
          </span>
        </div>
        <div class="join-card-body">
          <h3>¡Únete como repartidor!</h3>
          <p>
            Gana dinero extra entregando domicilios en
            Colombia. Las mejores tarifas y beneficios.
          </p>
          <a
            class="btn-join"
            href="{{ url('/registro-repartidor') }}"
          >
            ¡Regístrate ahora!
          </a>
        </div>
      </div>
    </div>
  </section>
</div>
