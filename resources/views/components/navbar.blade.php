<nav class="navbar navbar-expand-lg navbar-light bg-light shadow-sm px-3">
    {{-- Botão toggle da sidebar --}}
    <button class="btn btn-outline-secondary me-3" id="sidebarToggle">
        ☰
    </button>

    {{-- Marca/Logo --}}
    <a class="navbar-brand fw-bold" href="{{ route('home') }}">BolsistaControl</a>

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
                <a href="#" class="nav-link">🔔 Notificações</a>
            </li>

            {{-- Dropdown de usuário --}}
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle d-flex align-items-center" href="#"
                   id="userDropdown" role="button" data-bs-toggle="dropdown"
                   aria-expanded="false">
                    <img src="{{ auth()->user()->avatar_url }}"
                         alt="Avatar"
                         class="rounded-circle me-2">
                    <span>{{ auth()->user()->name }}</span>
                </a>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                    @auth
                    <li><a class="dropdown-item" href="#">Perfil</a></li>
                    <li><a class="dropdown-item" href="#">Configurações</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><form method="POST" action="{{ route('logout') }}" class="d-inline">
                        @csrf
                        <button class="dropdown-item">Sair</button>
                        </form>
                    </li>
                    @else
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('login') }}">Login</a>
                    </li>
                    @endauth
                </ul>
            </li>
        </ul>
    </div>
</nav>
