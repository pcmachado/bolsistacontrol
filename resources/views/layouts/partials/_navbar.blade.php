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
                    @php
                        $user = Auth::user();
                        $institutions = $user->isAdmin()
                            ? \App\Models\Institution::all()
                            : $user->institutions;

                        $activeInstitution = $user->activeInstitutions() ?? $institutions->first();
                    @endphp

                    @if($institutions->count() > 1 || $user->isAdmin())
                        <form method="POST" action="{{ route('institution.set') }}">
                            @csrf
                            <select name="institution_id" onchange="this.form.submit()" class="form-select">
                                @foreach($institutions as $inst)
                                    <option value="{{ $inst->id }}" {{ $activeInstitution && $activeInstitution->id == $inst->id ? 'selected' : '' }}>
                                        {{ $inst->name }}
                                    </option>
                                @endforeach
                            </select>
                        </form>
                    @else
                        <span class="navbar-text">{{ $activeInstitution->name ?? 'Sem vínculo' }}</span>
                    @endif
                </li>
                {{-- Ícone de notificações --}}
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="bi bi-bell-fill"></i>
                        <span class="d-lg-none ms-2">Notificações</span>
                    </a>
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