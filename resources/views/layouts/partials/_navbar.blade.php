<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm py-2 px-3 app-navbar">
    <div class="container-fluid">

        <div class="d-flex d-lg-none align-items-center gap-2">
            <button
                type="button"
                class="btn btn-outline-secondary d-lg-none"
                data-sidebar-toggle
                aria-label="Abrir menu">
                <i class="bi bi-list fs-5"></i>
            </button>

            <a class="navbar-brand fw-semibold text-truncate mb-0" href="{{ route('dashboard') }}" style="max-width: 58vw;">
                @yield('title', 'ProBolsas')
            </a>
        </div>

        <a class="navbar-brand fw-semibold d-none d-lg-inline mb-0" href="{{ route('dashboard') }}">
            ProBolsas
        </a>

        <ul class="navbar-nav ms-auto align-items-center flex-row gap-1">

            {{-- INSTITUICAO ATIVA (se existir) --}}
            @if(function_exists('activeInstitution') && activeInstitution())
                <li class="nav-item me-3 text-muted small d-none d-md-inline">
                    <i class="bi bi-building me-1"></i>
                    {{ activeInstitution()->name }}
                </li>
            @endif

            {{-- PAPEL DO USUARIO --}}
            <li class="nav-item me-3 text-muted small d-none d-md-inline">
                <i class="bi bi-person-badge me-1"></i>
                {{ ucfirst(str_replace('_',' ', auth()->user()->roles->pluck('name')->first())) }}
            </li>

            <li class="nav-item me-2">
                <a class="nav-link px-2" href="{{ route('manual.index') }}" title="Manual do Sistema">
                    <i class="bi bi-journal-bookmark fs-5"></i>
                    <span class="d-none d-md-inline ms-1">Manual</span>
                </a>
            </li>


            {{-- NOTIFICACOES --}}
            @php
                $unreadCount = $navUnreadCount ?? 0;
                $latestNotifications = $navNotifications ?? collect();
            @endphp

            <li class="nav-item dropdown me-2">

                <a class="nav-link position-relative px-2" href="#" data-bs-toggle="dropdown">
                    <i class="bi bi-bell-fill fs-5"></i>

                    @if($unreadCount > 0)
                    <span class="badge bg-danger rounded-pill position-absolute top-0 start-100 translate-middle">
                        {{ $unreadCount }}
                    </span>
                    @endif
                </a>

                <ul class="dropdown-menu dropdown-menu-end shadow-lg p-0" style="width: 360px;">

                    <li class="p-2 bg-light border-bottom fw-semibold d-flex justify-content-between">
                        Notificacoes
                        @if($unreadCount)
                            <span class="badge bg-danger">{{ $unreadCount }}</span>
                        @endif
                    </li>

                    @forelse($latestNotifications as $n)

                        @php
                            $level = $n->data['level'] ?? 'info';
                            
                            $icon = match($level) {
                                'danger' => 'bi-x-circle text-danger',
                                'warning' => 'bi-exclamation-triangle text-warning',
                                'success' => 'bi-check-circle text-success',
                                default => 'bi-info-circle text-primary'
                            };
                        @endphp

                        <li>
                            <a href="{{ route('notifications.read', $n->id) }}"
                            class="dropdown-item py-2 small {{ is_null($n->read_at) ? 'fw-bold bg-light' : '' }}">

                                <div class="d-flex">
                                    <i class="bi {{ $icon }} me-2 mt-1"></i>

                                    <div>
                                        <div>{{ $n->data['title'] ?? 'Notificação' }}</div>
                                        <small class="text-muted">
                                            {{ Str::limit($n->data['message'] ?? '', 60) }}
                                        </small>
                                        <div class="text-muted" style="font-size: 10px;">
                                            {{ $n->created_at->diffForHumans() }}
                                        </div>
                                    </div>
                                </div>

                            </a>
                        </li>

                    @empty
                        <li>
                            <div class="dropdown-item text-muted text-center py-3">
                                Nenhuma notificacao.
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


            {{-- AVATAR / MENU DO USUARIO --}}
            <li class="nav-item dropdown">

                <a class="nav-link dropdown-toggle d-flex align-items-center px-2" href="#" data-bs-toggle="dropdown">

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
</nav>
