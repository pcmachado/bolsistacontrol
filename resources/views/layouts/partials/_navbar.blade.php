<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm py-2 px-3 app-navbar">
    <div class="container-fluid">

        <div class="d-flex align-items-center gap-2">
            <button
                type="button"
                class="btn btn-outline-secondary"
                data-sidebar-toggle
                aria-label="Abrir menu">
                <i class="bi bi-list fs-5"></i>
            </button>

            <a class="navbar-brand d-flex align-items-center mb-0" href="{{ route('dashboard') }}">

                <img src="{{ asset('images/probolsas_fundo_branco.png') }}"
                    alt="ProBolsas"
                    style="max-height: 36px; width:auto;">
            </a>
        </div>

        <ul class="navbar-nav ms-auto align-items-center flex-row gap-1">
            @hasanyrole('admin|superadmin')
                @php
                    // Busca as instituições para montar o menu
                    $linkedInstitutionIds = auth()->user()->accessibleInstitutionIds();
                    $institutions = \App\Models\Institution::query()
                        ->whereIn('id', $linkedInstitutionIds)
                        ->orderBy('name')
                        ->get();
                    // Pega a escolha atual da sessão
                    $currentContextId = session('admin_institution_context');
                    $currentContext = $currentContextId ? $institutions->firstWhere('id', $currentContextId) : null;
                @endphp

                <!-- O ms-auto empurra o item para a direita se estiver num flexbox -->
                <li class="nav-item dropdown list-unstyled ms-auto me-3">
                    <a class="nav-link dropdown-toggle btn btn-light border text-dark shadow-sm px-3 d-flex align-items-center gap-2" 
                    href="#" 
                    role="button" 
                    data-bs-toggle="dropdown" 
                    aria-expanded="false">
                        <i class="bi bi-building text-primary"></i>
                        <span class="fw-bold text-truncate" style="max-width: 200px;">
                            {{ $currentContext ? $currentContext->name : 'Todas as Instituições' }}
                        </span>
                    </a>
                    
                    <ul class="dropdown-menu dropdown-menu-end shadow">
                        {{-- Opção de Ver Tudo --}}
                        <li>
                            <form action="{{ route('admin.context.switch') }}" method="POST" class="m-0">
                                @csrf
                                <input type="hidden" name="institution_id" value="">
                                <button type="submit" class="dropdown-item {{ !$currentContextId ? 'active' : '' }}">
                                    <i class="bi bi-globe me-2"></i> Ver Todas
                                </button>
                            </form>
                        </li>
                        
                        <li><hr class="dropdown-divider"></li>

                        {{-- Lista de Instituições Dinâmicas --}}
                        @foreach($institutions as $inst)
                            <li>
                                <form action="{{ route('admin.context.switch') }}" method="POST" class="m-0">
                                    @csrf
                                    <input type="hidden" name="institution_id" value="{{ $inst->id }}">
                                    <button type="submit" class="dropdown-item {{ $currentContextId == $inst->id ? 'active' : '' }}">
                                        {{ $inst->name }}
                                    </button>
                                </form>
                            </li>
                        @endforeach
                    </ul>
                </li>
            @endhasanyrole

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
