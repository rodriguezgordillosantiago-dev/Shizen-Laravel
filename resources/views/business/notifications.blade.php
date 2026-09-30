<x-business-layout title="Notificaciones">
    <div class="page-header">
        <div>
            <h1 class="page-header-title">Notificaciones</h1>
            <p class="page-header-sub">
                {{ $unread }} sin leer de {{ $notifications->count() }} total
            </p>
        </div>
        @if ($unread > 0)
            <form method="post" action="{{ route('business.notifications.read') }}">
                @csrf
                <button class="btn btn-secondary" type="submit">
                    <i class="bx bx-check-double"></i> Marcar todas como leídas
                </button>
            </form>
        @endif
    </div>

    <div class="card">
        @forelse ($notifications as $i => $n)
            <div class="notif-item {{ !$n->leida ? 'unread' : '' }}" id="notif-{{ $i }}">
                <div class="notif-dot {{ $n->leida ? 'read' : '' }}" id="dot-{{ $i }}"></div>
                <div class="notif-icon" style="background:#dbeafe;color:#2563eb">
                    <i class="bx bx-bell"></i>
                </div>
                <div style="flex:1">
                    <div class="notif-title">{{ $n->titulo }}</div>
                    <div class="notif-body">{{ $n->mensaje }}</div>
                    <div class="notif-time">
                        <i class="bx bx-time-five"></i>
                        {{ $n->fecha_creacion }}
                    </div>
                </div>
                @if (!$n->leida)
                    <button class="btn btn-secondary btn-sm" onclick="markRead({{ $i }})" style="flex-shrink:0">
                        Marcar leída
                    </button>
                @endif
            </div>
        @empty
            <div style="padding:30px;text-align:center;color:#777">No hay notificaciones todavía.</div>
        @endforelse
    </div>

    <script>
    function markRead(i) {
        const item = document.getElementById('notif-' + i);
        const dot  = document.getElementById('dot-' + i);
        if (item) item.classList.remove('unread');
        if (dot) dot.classList.add('read');
        const btn = item ? item.querySelector('button') : null;
        if (btn) btn.remove();
    }
    </script>
</x-business-layout>
