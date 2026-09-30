<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Pedido #{{ $order->id_pedido }} | Shizen</title>
  <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
  <link rel="stylesheet" href="{{ asset('css/nav.css') }}">
  <link rel="stylesheet" href="{{ asset('css/modals.css') }}">
  <link rel="stylesheet" href="{{ asset('css/orders.css') }}">
</head>
<body>
  <header>@include('navegacion')</header>
  <main class="orders-page">
    <a class="back-link" href="{{ route('orders') }}" aria-label="Volver a mis pedidos" title="Volver a mis pedidos">← Volver a mis pedidos</a>
    @php
      $deliveryStatus = strtolower(trim((string) ($order->entrega_estado ?? '')));
      $courierHasOrder = in_array($deliveryStatus, ['recogido en negocio', 'en camino', 'entregado'], true);
      $onRoute = in_array($deliveryStatus, ['en camino', 'entregado'], true);
    @endphp
    <section class="order-detail">
      <h1>Pedido #{{ $order->id_pedido }}</h1>
      <p>{{ $order->descripcion }}</p>
      <p>Estado: <strong>{{ $order->estado }}</strong></p>
      <p>Dirección: {{ $order->direccion_entrega }}</p>

      <div class="order-tracking" aria-label="Rastreo del pedido">
        <h2>Rastreo del pedido</h2>
        <div class="tracking-line">
          <div class="tracking-step is-complete"><span>✓</span><strong>Pedido realizado</strong><small>Confirmado</small></div>
          <div class="tracking-step is-complete"><span>✓</span><strong>En preparación</strong><small>El negocio está preparando tu pedido</small></div>
          <div class="tracking-step {{ $order->fecha_confirmacion || $courierHasOrder ? 'is-complete' : 'is-current' }}"><span>{{ $order->fecha_confirmacion || $courierHasOrder ? '✓' : '3' }}</span><strong>{{ $courierHasOrder ? 'Recogido por repartidor' : 'En preparación' }}</strong><small>{{ $courierHasOrder ? 'El repartidor ya recibió tu pedido' : 'Esperando al repartidor' }}</small></div>
          <div class="tracking-step {{ $order->fecha_confirmacion ? 'is-complete' : ($onRoute ? 'is-current' : '') }}"><span>{{ $order->fecha_confirmacion ? '✓' : '4' }}</span><strong>En camino</strong><small>{{ $order->fecha_confirmacion ? 'Pedido recibido' : ($onRoute ? 'Tu pedido va hacia la dirección de entrega' : 'Pendiente de salida') }}</small></div>
        </div>
      </div>

      @if(session('order_created'))
        <p class="order-success" role="alert">✓ Pedido registrado correctamente. Puedes consultar su estado y código de entrega aquí.</p>
      @elseif(session('order_success'))
        <p class="order-success" role="alert">✓ {{ session('order_success') }}</p>
      @endif

      @if($order->fecha_confirmacion)
        <p class="order-success">✅ Pedido entregado. ¡Gracias por elegir Shizen!</p>
      @else
        @if($courierHasOrder && !$onRoute)
          <p class="order-success">🛵 El repartidor ya recogió tu pedido. Pronto iniciará el recorrido hacia tu dirección.</p>
        @elseif($onRoute)
          <p class="order-success">🛵 Tu pedido está en camino. Comparte el código al llegar para confirmar la entrega.</p>
        @endif
        <div class="delivery-code-box" style="background:#f0fdf4;border:2px dashed #22c55e;border-radius:12px;padding:16px;text-align:center;margin:20px 0">
          <div style="font-size:14px;color:#15803d;font-weight:700">🔑 Tu Código de Entrega:</div>
          <div style="font-size:28px;font-weight:900;letter-spacing:4px;color:#166534;margin:6px 0">{{ $order->codigo_entrega }}</div>
          <div style="font-size:12px;color:#4b5563">Entrégaselo al repartidor cuando llegue con tu pedido para confirmar la entrega.</div>
        </div>
      @endif

      @if($order->fecha_confirmacion)
        @if(!$rated)
          <form class="rating-form" method="post" action="{{ route('orders.rate', $order->id_pedido) }}">
            @csrf
            <h2>Califica al repartidor</h2>
            <fieldset class="rating-stars" aria-label="Selecciona una calificación de 1 a 5 estrellas">
              <legend>Tu valoración</legend>
              @for($rating = 1; $rating <= 5; $rating++)
                <label class="rating-star">
                  <input type="radio" name="puntuacion" value="{{ $rating }}" aria-label="{{ $rating }} estrellas" required><script>(function(c){if(!c||c.dataset.validacionCampo)return;c.dataset.validacionCampo='1';var e=c.nextElementSibling&&c.nextElementSibling.classList.contains('field-error-inline')?c.nextElementSibling:document.createElement('small');e.className='field-error-inline';e.style.cssText='display:block;color:#c62828;font-size:12px;margin-top:4px;min-height:1em;';if(!e.parentNode)c.insertAdjacentElement('afterend',e);function v(){var x=c.required&&!c.value.trim(),i=x||!c.validity.valid;e.textContent=i?(x?'Este campo es obligatorio.':c.validationMessage):'';c.setAttribute('aria-invalid',i?'true':'false');return!i}['input','change','blur'].forEach(function(t){c.addEventListener(t,v)});if(c.form)c.form.addEventListener('submit',function(a){if(!v())a.preventDefault()})})(document.currentScript.previousElementSibling);</script>
                  <span aria-hidden="true"></span>
                </label>
              @endfor
            </fieldset>
            <textarea class="modal-input" name="comentario" placeholder="Comentario opcional" rows="3"></textarea><script>(function(c){if(!c||c.dataset.validacionCampo)return;c.dataset.validacionCampo='1';var e=c.nextElementSibling&&c.nextElementSibling.classList.contains('field-error-inline')?c.nextElementSibling:document.createElement('small');e.className='field-error-inline';e.style.cssText='display:block;color:#c62828;font-size:12px;margin-top:4px;min-height:1em;';if(!e.parentNode)c.insertAdjacentElement('afterend',e);function v(){var x=c.required&&!c.value.trim(),i=x||!c.validity.valid;e.textContent=i?(x?'Este campo es obligatorio.':c.validationMessage):'';c.setAttribute('aria-invalid',i?'true':'false');return!i}['input','change','blur'].forEach(function(t){c.addEventListener(t,v)});if(c.form)c.form.addEventListener('submit',function(a){if(!v())a.preventDefault()})})(document.currentScript.previousElementSibling);</script>
            <button class="btn-modal-primary" type="submit">Enviar calificación</button>
          </form>
        @elseif($rated)
          <p class="order-success">Ya calificaste este pedido.</p>
        @else
          <p class="order-success">La calificación estará disponible cuando se asigne el repartidor.</p>
        @endif
      @endif
      @if($order->fecha_confirmacion)
        @if(!$businessRated)
          <form class="rating-form" method="post" action="{{ route('orders.rate-business', $order->id_pedido) }}">
            @csrf
            <h2>Califica al negocio</h2>
            <fieldset class="rating-stars" aria-label="Selecciona una calificación de 1 a 5 estrellas">
              <legend>Tu valoración</legend>
              @for($rating = 1; $rating <= 5; $rating++)
                <label class="rating-star">
                  <input type="radio" name="puntuacion" value="{{ $rating }}" aria-label="{{ $rating }} estrellas" required>
                  <span aria-hidden="true"></span>
                </label>
              @endfor
            </fieldset>
            <textarea class="modal-input" name="comentario" placeholder="Comentario opcional" rows="3"></textarea>
            <button class="btn-modal-primary" type="submit">Enviar calificación</button>
          </form>
        @else
          <p class="order-success">Ya calificaste este negocio.</p>
        @endif
      @endif
    </section>
  </main>
  <div id="overlays">@include('modales')</div>
  <script src="{{ asset('js/app.js') }}"></script>
  <script>
    window.shizenOrderedItemIds = @json(session('ordered_item_ids', []));
    if (window.shizenOrderedItemIds.length && Array.isArray(window.cart)) {
      window.cart = window.cart.filter(function (item) {
        return !window.shizenOrderedItemIds.includes(Number(item.id));
      });
      if (typeof window.saveCart === 'function') window.saveCart();
    }
  </script>
</body>
</html>
