<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm py-2 px-3">
    <div class="container-fluid">

        {{-- LOGO / BRAND --}}
        <a class="navbar-brand fw-semibold" href="{{ route('dashboard') }}">
            BolsistaControl
        </a>

        {{-- MOBILE TOGGLER (para dropdowns colapsáveis) --}}
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
            <span class="navbar-toggler-icon"></span>
        </button>

        {{-- CONTEÚDO DA NAVBAR --}}
        <div class="collapse navbar-collapse" id="navbarContent">

            <ul class="navbar-nav ms-auto align-items-center">

                {{-- INSTITUIÇÃO ATIVA (se existir) --}}
                @if(function_exists('activeInstitution') && activeInstitution())
                    <li class="nav-item me-3 text-muted small d-none d-md-inline">
                        <i class="bi bi-building me-1"></i>
                        {{ activeInstitution()->name }}
                    </li>
                @endif

                {{-- PAPEL DO USUÁRIO --}}
                <li class="nav-item me-3 text-muted small d-none d-md-inline">
                    <i class="bi bi-person-badge me-1"></i>
                    {{ ucfirst(str_replace('_',' ', auth()->user()->roles->pluck('name')->first())) }}
                </li>


                {{-- 🔔 NOTIFICAÇÕES --}}
                @php
                    $unreadCount = auth()->user()->unreadNotifications()->count();
                    $latestNotifications = auth()->user()->notifications()->latest()->take(5)->get();
                @endphp

                <li class="nav-item dropdown me-3">

                    <a class="nav-link position-relative" href="#" data-bs-toggle="dropdown">
                        <i class="bi bi-bell-fill fs-5"></i>

                        @if($unreadCount > 0)
                        <span class="badge bg-danger rounded-pill position-absolute top-0 start-100 translate-middle">
                            {{ $unreadCount }}
                        </span>
                        @endif
                    </a>

                    <ul class="dropdown-menu dropdown-menu-end shadow-lg p-0" style="width: 350px">

                        <li class="p-2 bg-light border-bottom fw-semibold">
                            Notificações
                        </li>

                        @forelse($latestNotifications as $n)

                            @php
                                $level = $n->data['level'] ?? 'info';
                                $color = match($level) {
                                    'warning' => 'bg-warning-subtle',
                                    'danger'  => 'bg-danger-subtle text-white',
                                    default   => ''
                                };
                            @endphp

                            <li>
                                <a href="{{ route('notifications.read', $n->id) }}"
                                   class="dropdown-item py-2 {{ $color }} {{ is_null($n->read_at) ? 'fw-bold' : '' }}">

                                    <div>{{ $n->data['title'] ?? 'Notificação' }}</div>
                                    <small class="text-muted">{{ $n->created_at->diffForHumans() }}</small>

                                </a>
                            </li>

                        @empty
                            <li>
                                <div class="dropdown-item text-muted text-center py-3">
                                    Nenhuma notificação.
                                </div>
                            </li>
                        @endforelse

                        <li>
                            <a href="{{ route('notifications.index') }}"
                               class="dropdown-item text-center py-2 bg-light">
                                Ver todas
                            </a>
                        </li>
                    </ul>
                </li>


                {{-- 👤 AVATAR / MENU DO USUÁRIO --}}
                <li class="nav-item dropdown">

                    <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" data-bs-toggle="dropdown">

                        <img src="https://www.gravatar.com/avatar/{{ md5(strtolower(trim(auth()->user()->email))) }}?d=mp"
                             class="rounded-circle me-2"
                             width="32" height="32">

                        <span class="d-none d-md-inline">
                            {{ auth()->user()->name }}
                        </span>
                    </a>

                    <ul class="dropdown-menu dropdown-menu-end shadow">
                        <li>
                            <a class="dropdown-item" href="{{ route('profile.edit') }}">
                                <i class="bi bi-person-circle me-2"></i> Perfil
                            </a>
                        </li>

                        <li><hr class="dropdown-divider"></li>

                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button class="dropdown-item">
                                    <i class="bi bi-box-arrow-right me-2"></i> Sair
                                </button>
                            </form>
                        </li>
                    </ul>

                </li>

            </ul>

        </div>
    </div>
</nav>
