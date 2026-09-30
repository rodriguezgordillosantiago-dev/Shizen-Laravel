<nav class="bottom-nav" aria-label="Navegación principal">
  <a href="{{ route('inicio') }}" class="nav-item {{ request()->routeIs('inicio') ? 'is-active' : '' }}" aria-label="Inicio">
    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
      <path stroke-linecap="round" stroke-linejoin="round" d="M3 10.5 12 3l9 7.5v8.25A2.25 2.25 0 0 1 18.75 21H5.25A2.25 2.25 0 0 1 3 18.75V10.5Z"/>
      <path d="M9 21v-5.5a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1V21" />
    </svg>
    <span>Inicio</span>
  </a>

  <a href="{{ route('activos') }}" class="nav-item {{ request()->routeIs('activos') ? 'is-active' : '' }}" aria-label="Activos">
    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
      <path stroke-linecap="round" stroke-linejoin="round" d="m4 7 8-4 8 4-8 4-8-4Zm0 0v10l8 4 8-4V7M12 11v10"/>
    </svg>
    <span>Activos</span>
    <b class="badge" id="badge-activos"></b>
  </a>

  <button id="delivery-status-toggle" class="nav-status-toggle" type="button" aria-pressed="false">
    <span class="nav-status-icon">
      <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
        <path stroke-linecap="round" d="M12 2v10m0 0 4-4m-4 4-4-4M5 13v4a3 3 0 0 0 3 3h8a3 3 0 0 0 3-3v-4"/>
      </svg>
    </span>
    <span class="nav-status-label">Activo</span>
  </button>

  <a href="{{ url('/notificaciones') }}" class="nav-item {{ request()->is('notificaciones*') ? 'is-active' : '' }}" aria-label="Notificaciones">
    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
      <path stroke-linecap="round" stroke-linejoin="round" d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9Zm-8 13h4"/>
    </svg>
    <span>Notificaciones</span>
    <b class="badge" id="badge-notificaciones"></b>
  </a>

  <a href="{{ route('perfil') }}" class="nav-item {{ request()->routeIs('perfil', 'configuracion') ? 'is-active' : '' }}" aria-label="Perfil">
    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
      <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 1 1-8 0 4 4 0 0 1 8 0Zm-12 14a8 8 0 0 1 16 0"/>
    </svg>
    <span>Perfil</span>
  </a>
</nav>
