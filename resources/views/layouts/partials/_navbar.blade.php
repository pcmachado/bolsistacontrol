<nav class="navbar navbar-expand-lg navbar-light bg-light shadow-sm px-3">
    {{-- Botão toggle da sidebar --}}
    <button class="btn btn-outline-secondary" id="sidebarToggle">
        <i class="bi bi-list"></i>
    </button>

    {{-- Marca/Logo --}}
    <a class="navbar-brand fw-bold ms-3" href="{{ route('home') }}">BolsistaControl</a>

    {{-- Botão para colapsar o menu em telas pequenas --}}
    <button class="navbar-toggler border-0" type="button"
            data-bs-toggle="collapse"
            data-bs-target="#navbarContent"
            aria-controls="navbarContent"
            aria-expanded="false"
            aria-label="Alternar navegação">
        <span class="navbar-toggler-icon"></span>
    </button>

    {{-- Conteúdo da navbar --}}
    <div class="collapse navbar-collapse" id="navbarContent">
        <ul class="navbar-nav ms-auto align-items-center">
            {{-- Item de menu simples --}}
            <li class="nav-item">
                <a href="#" class="nav-link">
                    <i class="bi bi-bell-fill"></i>
                    <span class="d-lg-none ms-2">Notificações</span>
                </a>
            </li>

            {{-- Dropdown de usuário --}}
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle d-flex align-items-center" href="#"
                   id="userDropdown" role="button" data-bs-toggle="dropdown"
                   aria-expanded="false">
                    <img src="https://www.gravatar.com/avatar/{{ md5(strtolower(trim(auth()->user()->email))) }}?d=mp"
                         alt="Avatar"
                         class="rounded-circle me-2" width="32" height="32">
                    <span>{{ auth()->user()->name }}</span>
                </a>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                    @auth
                    <li><a class="dropdown-item" href="{{ route('profile.edit') }}">Perfil</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form method="POST" action="{{ route('logout') }}" class="d-inline">
                            @csrf
                            <button class="dropdown-item">Sair</button>
                        </form>
                    </li>
                    @else
                    <li><a class="dropdown-item" href="{{ route('login') }}">Login</a></li>
                    @endauth
                </ul>
            </li>
        </ul>
    </div>
</nav>

