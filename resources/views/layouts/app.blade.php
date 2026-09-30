<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>{{ $title ?? 'Shizen Repartidor' }}</title>
  <link rel="stylesheet" href="{{ asset('css/repartidor.css') }}?v=logout-button-2">
  <link rel="stylesheet" href="{{ asset('css/navigation.css') }}?v=scale-3">
  @stack('styles')
</head>
<body>
  @yield('content')
  @include('partials.nav')
  <script>window.repartidorApiUrl = @json(url('/api/entregas'));</script>
  <script src="{{ asset('js/data.js') }}?v=stats-1"></script>
  <div id="chat-root"></div>
  <script src="{{ asset('js/chat.js') }}?v=compact-2"></script>
  <script>if (typeof initChat === 'function') initChat();</script>
  @stack('scripts')
</body>
</html>
