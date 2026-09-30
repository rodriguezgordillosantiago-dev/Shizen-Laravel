<!doctype html>
<html lang="es">
  <head>
    <meta charset="UTF-8" />
    <meta
      name="viewport"
      content="width=device-width, initial-scale=1.0"
    />
    <base href="{{ asset('/') }}" />
    <title>Shizen — Comida vegana a tu puerta</title>
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
    <link
      rel="stylesheet"
      href="css/nav.css?v=20260929-logout-2"
    />
    <link rel="stylesheet" href="css/home.css" />
    <link rel="stylesheet" href="{{ asset('css/catalog.css') }}" />
    <link
      rel="stylesheet"
      href="css/pages.css?v=20260816-1"
    />
    <link rel="stylesheet" href="css/registro_base.css" />
    <link
      rel="stylesheet"
      href="css/registro_usuario.css"
    />
    <link
      rel="stylesheet"
      href="css/registro_negocio.css"
    />
    <link
      rel="stylesheet"
      href="css/registro_repartidor.css"
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
      @include('home')
    </main>
    <div id="overlays">
      @include('modales')
    </div>
    <script>
      window.shizenCategoryUrl = @json(url('/categorias'));
    </script>
    <script src="js/data.js?v=20260827-1"></script>
    <script src="js/layout.js?v=20260827-1"></script>
    <script src="js/app.js?v=20260827-1"></script>
  </body>
</html>
