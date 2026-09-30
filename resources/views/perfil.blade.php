<!doctype html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Mi perfil | Shizen</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
  <link rel="stylesheet" href="{{ asset('css/nav.css') }}">
  <link rel="stylesheet" href="{{ asset('css/pages.css') }}">
  <link rel="stylesheet" href="{{ asset('css/modals.css') }}">
  <link rel="stylesheet" href="{{ asset('css/perfil.css') }}?v=logout-animation-1">
</head>
<body>
  <header id="navigation">@include('navegacion')</header>

  <main id="app-content">
    <div class="view active" id="view-user-profile">
      <div class="profile-page">
        <div class="profile-banner profile-banner--user">
          <a class="btn-back profile-back" href="{{ url('/') }}" aria-label="Volver al inicio">&#8592;</a>
          <div class="profile-banner-overlay"></div>
        </div>
        <div class="profile-header-wrap">
          <div class="profile-avatar-ring profile-avatar-ring--user">
            <div class="profile-avatar" id="uprofileEmoji">
              @if ($profileUser['avatar'] ?? false)
                <img src="{{ asset($profileUser['avatar']) }}" alt="Avatar de perfil">
              @else
                <span aria-hidden="true">&#128100;</span>
              @endif
            </div>
          </div>
          <div class="profile-meta">
            <div class="profile-name-row">
              <h1 class="profile-name" id="uprofileName">{{ trim(($profileUser['nombre'] ?? '') . ' ' . ($profileUser['apellido'] ?? '')) ?: 'Mi cuenta' }}</h1>
              <span class="profile-badge">&#128100; Usuario</span>
            </div>
            <div class="profile-handle" id="uprofileEmail">{{ $profileUser['email'] ?? '' }}</div>
            <div class="profile-handle" id="uprofileJoined" style="margin-top:2px">&#127807; Cliente Shizen</div>
            @if (!empty($profileUser['localidad']))
              <div class="profile-links"><span class="profile-link">📍 {{ $profileUser['localidad'] }}</span></div>
            @endif
            <div class="uprofile-quick-links">
              <button class="btn-follow uprofile-quick-btn" type="button" onclick="openUprofileSection('purchases')">&#128722; Historial</button>
              <button class="btn-follow uprofile-quick-btn" type="button" onclick="openUprofileSection('following')"><svg class="heart-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M16.5 3C19.538 3 22 5.5 22 9c0 7-7.5 11-10 12.5C9.5 20 2 16 2 9c0-3.5 2.5-6 5.5-6C9.36 3 11 4 12 5c1-1 2.64-2 4.5-2zm-3.566 15.604c.881-.556 1.676-1.109 2.42-1.701C18.335 14.533 20 11.943 20 9c0-2.36-1.537-4-3.5-4-1.076 0-2.24.57-3.086 1.414L12 7.828l-1.414-1.414C9.74 5.57 8.576 5 7.5 5 5.56 5 4 6.656 4 9c0 2.944 1.666 5.533 4.645 7.903.745.592 1.54 1.145 2.421 1.7.299.189.595.37.934.572.339-.202.635-.383.934-.571z"/></svg> Favoritos</button>
            </div>
          </div>
          <button class="btn-follow profile-edit-button" type="button" onclick="openEditProfileModal()">&#9998; Editar perfil</button>
        </div>

        @if (session('profile_success'))
          <p class="profile-success" role="status">{{ session('profile_success') }}</p>
        @endif

        <div class="profile-stats-bar">
          <div class="profile-stat"><div class="profile-stat-num" id="ustatPurchases">0</div><div class="profile-stat-label">Pedidos</div></div>
          <div class="profile-stat"><div class="profile-stat-num" id="ustatReviews">0</div><div class="profile-stat-label">Reseñas</div></div>
          <div class="profile-stat"><div class="profile-stat-num" id="ustatFollowing">0</div><div class="profile-stat-label">Favoritos</div></div>
        </div>

        <div class="profile-tabs-wrap">
          <div class="profile-tabs" role="tablist" aria-label="Actividad de la cuenta">
            <button class="profile-tab active" id="profile-tab-purchases" role="tab" aria-selected="true" aria-controls="uprofile-purchases" data-section="purchases" type="button" onclick="openUprofileSection('purchases')">&#128722; Historial</button>
            <button class="profile-tab" id="profile-tab-reviews" role="tab" aria-selected="false" aria-controls="uprofile-reviews" data-section="reviews" type="button" onclick="openUprofileSection('reviews')">&#11088; Reseñas</button>
            <button class="profile-tab" id="profile-tab-following" role="tab" aria-selected="false" aria-controls="uprofile-following" data-section="following" type="button" onclick="openUprofileSection('following')"><svg class="heart-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M16.5 3C19.538 3 22 5.5 22 9c0 7-7.5 11-10 12.5C9.5 20 2 16 2 9c0-3.5 2.5-6 5.5-6C9.36 3 11 4 12 5c1-1 2.64-2 4.5-2zm-3.566 15.604c.881-.556 1.676-1.109 2.42-1.701C18.335 14.533 20 11.943 20 9c0-2.36-1.537-4-3.5-4-1.076 0-2.24.57-3.086 1.414L12 7.828l-1.414-1.414C9.74 5.57 8.576 5 7.5 5 5.56 5 4 6.656 4 9c0 2.944 1.666 5.533 4.645 7.903.745.592 1.54 1.145 2.421 1.7.299.189.595.37.934.572.339-.202.635-.383.934-.571z"/></svg> Favoritos</button>
          </div>
        </div>

        <section class="profile-tab-content active" id="uprofile-purchases" role="tabpanel" aria-labelledby="profile-tab-purchases">
          <div class="profile-section">
            <div class="uprofile-empty" id="upurchasesEmpty">
              <div class="uprofile-empty-icon">&#128722;</div>
              <p>Aún no has realizado pedidos.</p>
              <a class="btn-primary-full" href="{{ url('/') }}">Explorar platos y restaurantes</a>
            </div>
          </div>
        </section>

        <section class="profile-tab-content" id="uprofile-reviews" role="tabpanel" aria-labelledby="profile-tab-reviews">
          <div class="profile-section">
            <div class="uprofile-empty" id="ureviewsEmpty">
              <div class="uprofile-empty-icon">&#11088;</div>
              <p>Aún no tienes calificaciones.<br>Cuando califiques un negocio, aparecerán aquí.</p>
            </div>
          </div>
        </section>

        <section class="profile-tab-content" id="uprofile-following" role="tabpanel" aria-labelledby="profile-tab-following">
          <div class="profile-section">
            <div class="uprofile-empty" id="ufollowingEmpty">
              <div class="uprofile-empty-icon"><svg class="heart-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M16.5 3C19.538 3 22 5.5 22 9c0 7-7.5 11-10 12.5C9.5 20 2 16 2 9c0-3.5 2.5-6 5.5-6C9.36 3 11 4 12 5c1-1 2.64-2 4.5-2zm-3.566 15.604c.881-.556 1.676-1.109 2.42-1.701C18.335 14.533 20 11.943 20 9c0-2.36-1.537-4-3.5-4-1.076 0-2.24.57-3.086 1.414L12 7.828l-1.414-1.414C9.74 5.57 8.576 5 7.5 5 5.56 5 4 6.656 4 9c0 2.944 1.666 5.533 4.645 7.903.745.592 1.54 1.145 2.421 1.7.299.189.595.37.934.572.339-.202.635-.383.934-.571z"/></svg></div>
              <p>Aún no tienes favoritos.</p>
              <a class="btn-primary-full" href="{{ route('favorites') }}">Ver mis favoritos</a>
            </div>
          </div>
        </section>

        <div class="profile-logout-row">
          <form method="post" action="{{ route('logout') }}">
            @csrf
            <button class="btn-uprofile-logout" type="submit" aria-label="Cerrar sesión" title="Cerrar sesión"><span class="sign" aria-hidden="true"><svg class="logout-icon" viewBox="0 0 512 512"><path d="M377.9 105.9L500.7 228.7c7.2 7.2 11.3 17.1 11.3 27.3s-4.1 20.1-11.3 27.3L377.9 406.1c-6.4 6.4-15 9.9-24 9.9c-18.7 0-33.9-15.2-33.9-33.9l0-62.1-128 0c-17.7 0-32-14.3-32-32l0-64c0-17.7 14.3-32 32-32l128 0 0-62.1c0-18.7 15.2-33.9 33.9-33.9c9 0 17.6 3.6 24 9.9zM160 96L96 96c-17.7 0-32 14.3-32 32l0 256c0 17.7 14.3 32 32 32l64 0c17.7 0 32 14.3 32 32s-14.3 32-32 32l-64 0c-53 0-96-43-96-96L0 128C0 75 43 32 96 32l64 0c17.7 0 32 14.3 32 32s-14.3 32-32 32z"></path></svg></span><span class="logout-button-text">Cerrar sesión</span></button>
          </form>
        </div>
      </div>
    </div>
  </main>

  <div class="modal-overlay" id="editProfileModal" onclick="handleEditProfileOverlay(event)" aria-hidden="true">
    <div class="modal-card edit-profile-card" role="dialog" aria-modal="true" aria-labelledby="editProfileTitle">
      <button class="modal-close" type="button" onclick="closeEditProfileModal()" aria-label="Cerrar">&#10005;</button>
      <h2 class="edit-profile-title" id="editProfileTitle">Editar perfil</h2>
      @if ($errors->any())
        <p class="login-error" role="alert">{{ $errors->first() }}</p>
      @endif
      <form method="post" action="{{ route('profile.update') }}" id="editProfileForm">
        @csrf
        <input type="hidden" name="avatar" id="editProfileAvatar" value="{{ old('avatar', $profileUser['avatar'] ?? '') }}">
        <div class="edit-profile-label">Avatar</div>
        <div class="edit-emoji-picker" id="editEmojiPicker">
          @foreach ($avatarFiles as $avatarFile)
            <button class="edit-emoji-opt{{ old('avatar', $profileUser['avatar'] ?? '') === $avatarFile ? ' selected' : '' }}" type="button" data-avatar="{{ $avatarFile }}" aria-label="Elegir avatar {{ pathinfo($avatarFile, PATHINFO_FILENAME) }}">
              <img src="{{ asset($avatarFile) }}" alt="">
            </button>
          @endforeach
        </div>
        <label class="edit-profile-label" for="editProfileName">Nombre</label>
        <input class="form-input" id="editProfileName" name="nombre" type="text" value="{{ old('nombre', $profileUser['nombre'] ?? '') }}" maxlength="100" required>
        <label class="edit-profile-label" for="editProfileLastName">Apellido</label>
        <input class="form-input" id="editProfileLastName" name="apellido" type="text" value="{{ old('apellido', $profileUser['apellido'] ?? '') }}" maxlength="100" required>
        <label class="edit-profile-label" for="editProfileAddress">Dirección</label>
        <input class="form-input" id="editProfileAddress" name="direccion" type="text" value="{{ old('direccion', $profileUser['direccion'] ?? '') }}" maxlength="255">
        <label class="edit-profile-label" for="editProfileCity">Localidad</label>
        <input class="form-input" id="editProfileCity" name="localidad" type="text" value="{{ old('localidad', $profileUser['localidad'] ?? '') }}" maxlength="100">
        <div class="edit-profile-readonly"><span>&#128274; Cuenta: <strong>{{ $profileUser['email'] ?? '' }}</strong></span></div>
        <button class="btn-dish-pedir edit-profile-save" type="submit">Guardar cambios</button>
      </form>
    </div>
  </div>

  <div id="overlays">@include('modales')</div>
  <script>
    (function () {
      var modal = document.getElementById('editProfileModal');
      var picker = document.getElementById('editEmojiPicker');
      var avatarInput = document.getElementById('editProfileAvatar');
      var form = document.getElementById('editProfileForm');

      window.openUprofileSection = function (section) {
        var panel = document.getElementById('uprofile-' + section);
        var tab = document.getElementById('profile-tab-' + section);
        if (!panel || !tab) return;
        document.querySelectorAll('#view-user-profile .profile-tab-content').forEach(function (item) {
          item.classList.toggle('active', item === panel);
        });
        document.querySelectorAll('#view-user-profile .profile-tab').forEach(function (item) {
          var active = item === tab;
          item.classList.toggle('active', active);
          item.setAttribute('aria-selected', active ? 'true' : 'false');
        });
      };

      window.openEditProfileModal = function () {
        modal.classList.add('open');
        modal.setAttribute('aria-hidden', 'false');
        document.getElementById('editProfileName').focus();
      };

      window.closeEditProfileModal = function () {
        modal.classList.remove('open');
        modal.setAttribute('aria-hidden', 'true');
      };

      window.handleEditProfileOverlay = function (event) {
        if (event.target === modal) window.closeEditProfileModal();
      };

      picker.addEventListener('click', function (event) {
        var option = event.target.closest('[data-avatar]');
        if (!option) return;
        avatarInput.value = option.getAttribute('data-avatar');
        picker.querySelectorAll('.edit-emoji-opt').forEach(function (item) {
          item.classList.toggle('selected', item === option);
        });
      });

      document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape' && modal.classList.contains('open')) window.closeEditProfileModal();
      });

      if (@json($errors->any())) window.openEditProfileModal();
    })();
  </script>
  <script src="{{ asset('js/app.js') }}"></script>
</body>
</html>
