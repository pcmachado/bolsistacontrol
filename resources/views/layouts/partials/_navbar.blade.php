<nav class="navbar navbar-expand-lg navbar-light bg-light shadow-sm px-3">
    <div class="container-fluid">
        {{-- Botão toggle da sidebar (desktop e mobile) --}}
        <button class="btn btn-outline-dark me-2" type="button" id="sidebarToggle">
            <i class="bi bi-list"></i>
        </button>

        {{-- Marca/Logo --}}
        <a class="navbar-brand fw-bold ms-3" href="{{ route('dashboard') }}">BolsistaControl</a>

        {{-- Conteúdo da navbar --}}
        <div class="collapse navbar-collapse" id="navbarContent">
            <ul class="navbar-nav ms-auto align-items-center">
                <li class="nav-item dropdown">
                    @if(activeInstitution())
                        <li class="nav-item me-3 text-dark">
                            <i class="bi bi-building me-1"></i>
                            <strong>{{ activeInstitution()->name }}</strong>
                        </li>
                    @endif

                    {{-- PAPEL DO USUÁRIO --}}
                    <li class="nav-item me-3 text-dark small">
                        <i class="bi bi-person-badge"></i>
                        {{ ucfirst(str_replace('_',' ', auth()->user()->roles()->pluck('name')->first())) }}
                    </li>
                </li>
                {{-- Ícone de notificações --}}
                <li class="nav-item dropdown">
                    <a class="nav-link position-relative" href="#" id="notificationsDropdown" role="button" data-bs-toggle="dropdown">
                        <i class="bi bi-bell" style="font-size: 1.3rem"></i>

                        @php $unread = auth()->user()->unreadNotifications()->count(); @endphp

                        @if($unread > 0)
                            <span class="badge bg-danger position-absolute top-0 start-100 translate-middle rounded-pill">
                                {{ $unread }}
                            </span>
                        @endif
                    </a>

                    <ul class="dropdown-menu dropdown-menu-end p-2" aria-labelledby="notificationsDropdown" style="width: 320px; max-height: 400px; overflow-y: auto;">
                        <li class="dropdown-header fw-bold">Notificações</li>

                        @forelse(auth()->user()->unreadNotifications()->limit(5)->get() as $n)
                            <li>
                                <a class="dropdown-item small" href="{{ route('notifications.read', $n->id) }}">
                                    <strong>{{ $n->data['title'] ?? 'Notificação' }}</strong>
                                    <div>{{ $n->data['message'] ?? '' }}</div>
                                    <span class="text-muted">{{ $n->created_at->diffForHumans() }}</span>
                                </a>
                            </li>
                        @empty
                            <li><span class="dropdown-item small text-muted">Nenhuma nova notificação</span></li>
                        @endforelse

                        <li><hr class="dropdown-divider"></li>

                        <li><a class="dropdown-item text-center small" href="{{ route('notifications.index') }}">Ver todas</a></li>
                    </ul>
                </li>

                {{-- Dropdown de usuário --}}
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle d-flex align-items-center"
                    href="#"
                    id="userDropdown"
                    role="button"
                    data-bs-toggle="dropdown"
                    aria-expanded="false">
                        <img src="https://www.gravatar.com/avatar/{{ md5(strtolower(trim(auth()->user()->email))) }}?d=mp"
                            alt="Avatar"
                            class="rounded-circle me-2" width="32" height="32">
                        <span>{{ auth()->user()->name }}</span>
                    </a>

                    <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="userDropdown">
                        @auth
                            <li>
                                <a class="dropdown-item" href="{{ route('profile.edit') }}">
                                    <i class="bi bi-person-circle me-2"></i> Perfil
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}" class="d-inline">
                                    @csrf
                                    <button class="dropdown-item">
                                        <i class="bi bi-box-arrow-right me-2"></i> Sair
                                    </button>
                                </form>
                            </li>
                        @else
                            <li>
                                <a class="dropdown-item" href="{{ route('login') }}">
                                    <i class="bi bi-box-arrow-in-right me-2"></i> Login
                                </a>
                            </li>
                        @endauth
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>