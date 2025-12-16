{{-- ===========================================
     SIDEBAR MOBILE (OFFCANVAS)
     - Esta versão aparece SOMENTE no mobile
     - Textos serão ocultados via CSS (app.css)
     - Ícones continuam visíveis
   =========================================== --}}
<div class="bg-dark text-white h-100" id="sidebarMobileContent">

    {{-- ===========================================
         CABEÇALHO COM IDENTIDADE DO SISTEMA
    ============================================ --}}
    <div class="p-3 border-bottom border-secondary d-flex align-items-center">
        <i class="bi bi-calendar-check-fill fs-4 me-2"></i>
        <span class="fw-bold fs-5 sidebar-text">BolsistaControl</span>
        {{-- ← sidebar-text será ocultado via CSS no mobile --}}
    </div>


    {{-- ===========================================
         USUÁRIO LOGADO
    ============================================ --}}
    <div class="p-3 border-bottom border-secondary d-flex align-items-center">
        <img src="https://www.gravatar.com/avatar/{{ md5(strtolower(trim(auth()->user()->email))) }}?d=mp"
            class="rounded-circle me-2" width="36" height="36">

        <div>
            <div class="fw-semibold sidebar-text">{{ auth()->user()->name }}</div>
            <div class="small text-muted sidebar-text">
                {{ auth()->user()->roles->pluck('name')->first() }}
            </div>
        </div>
    </div>


    {{-- ===========================================
         MENU (APENAS ÍCONES NO MOBILE)
         O TEXTO SERÁ OCULTADO AUTOMATICAMENTE
    ============================================ --}}
    <div class="mt-2">

        {{-- ---------------------
             BOLSISTA
        ---------------------- --}}
        @role('bolsista')
            <div class="mt-3 px-3 text-uppercase small text-secondary sidebar-text">
                Bolsista
            </div>

            <a href="{{ route('dashboard') }}" class="sidebar-mobile-link d-flex align-items-center">
                <i class="bi bi-speedometer2 sidebar-icon"></i>
                <span class="sidebar-text ms-2">Dashboard</span>
            </a>

            {{-- Submenu Frequências --}}
            <div class="sidebar-submenu mt-1">

                <button class="sidebar-submenu-toggle d-flex align-items-center w-100">
                    <i class="bi bi-file-earmark-bar-graph sidebar-icon"></i>
                    <span class="sidebar-text ms-2">Frequências</span>
                    <i class="bi bi-chevron-down arrow-icon ms-auto"></i>
                </button>

                <div class="sidebar-submenu-items">
                    <a href="{{ route('attendance.my') }}" class="submenu-item sidebar-mobile-link">
                        <i class="bi bi-clock-history sidebar-icon"></i>
                        <span class="sidebar-text ms-2">Minhas Frequências</span>
                    </a>

                    <a href="{{ route('attendance.create') }}" class="submenu-item sidebar-mobile-link">
                        <i class="bi bi-plus-circle sidebar-icon"></i>
                        <span class="sidebar-text ms-2">Registrar</span>
                    </a>

                    <a href="{{ route('attendance.pending') }}" class="submenu-item sidebar-mobile-link">
                        <i class="bi bi-hourglass-split sidebar-icon"></i>
                        <span class="sidebar-text ms-2">Pendentes</span>
                    </a>
                </div>

            </div>
        @endrole


        {{-- ---------------------
             ADMINISTRAÇÃO
        ---------------------- --}}
        @hasanyrole('admin|coordenador_geral|coordenador_adjunto')

            <div class="mt-4 px-3 text-uppercase small text-secondary sidebar-text">
                Administração
            </div>

            {{-- LISTA DE ITENS ADMIN --}}
            @php
                $adminMenu = [
                    ['route' => 'admin.dashboard', 'icon' => 'bi-speedometer'],
                    ['route' => 'admin.units.index', 'icon' => 'bi-building'],
                    ['route' => 'admin.positions.index', 'icon' => 'bi-briefcase'],
                    ['route' => 'admin.scholarship_holders.index', 'icon' => 'bi-people'],
                    ['route' => 'admin.projects.index', 'icon' => 'bi-kanban'],
                    ['route' => 'admin.courses.index', 'icon' => 'bi-mortarboard'],
                    ['route' => 'admin.attendance_records.index', 'icon' => 'bi-journal-check'],
                    ['route' => 'admin.homologations.index', 'icon' => 'bi-check2-square'],
                    ['route' => 'admin.users.index', 'icon' => 'bi-person-gear'],
                    ['route' => 'admin.roles.index', 'icon' => 'bi-key'],
                    ['route' => 'admin.permissions.index', 'icon' => 'bi-shield-check'],
                ];
            @endphp

            @foreach ($adminMenu as $item)
                <a href="{{ route($item['route']) }}" class="sidebar-mobile-link d-flex align-items-center">
                    <i class="bi {{ $item['icon'] }} sidebar-icon"></i>
                    <span class="sidebar-text ms-2">{{ ucfirst(last(explode('.', $item['route']))) }}</span>
                </a>
            @endforeach

        @endhasanyrole


        {{-- ---------------------
             RELATÓRIOS
        ---------------------- --}}
        <div class="mt-4 px-3 text-uppercase small text-secondary sidebar-text">
            Relatórios
        </div>

        {{-- ADMIN + GERAL --}}
        @role(['admin','coordenador_geral'])
            <a href="{{ route('admin.reports.unit_detail') }}" class="sidebar-mobile-link d-flex align-items-center">
                <i class="bi bi-funnel sidebar-icon"></i>
                <span class="sidebar-text ms-2">Por Unidade</span>
            </a>
            <a href="{{ route('admin.reports.report') }}" class="sidebar-mobile-link d-flex align-items-center">
                <i class="bi bi-bar-chart sidebar-icon"></i>
                <span class="sidebar-text ms-2">Consolidado</span>
            </a>
        @endrole

        {{-- COORD. ADJUNTO --}}
        @role('coordenador_adjunto')
            <a href="{{ route('admin.reports.unit_detail') }}" class="sidebar-mobile-link d-flex align-items-center">
                <i class="bi bi-building sidebar-icon"></i>
                <span class="sidebar-text ms-2">Minhas Unidades</span>
            </a>
        @endrole

        {{-- BOLSISTA --}}
        @role('bolsista')
            <a href="{{ route('reports.myReport') }}" class="sidebar-mobile-link d-flex align-items-center">
                <i class="bi bi-file-earmark-person sidebar-icon"></i>
                <span class="sidebar-text ms-2">Meu Relatório</span>
            </a>
        @endrole

    </div>
</div>


{{-- ===========================================
     ESTILOS DO MOBILE
     (ícones centralizados, textos ocultos)
=========================================== --}}
<style>
    .sidebar-mobile-link {
        display: flex;
        align-items: center;
        padding: 12px 18px;
        text-decoration: none;
        color: #fff;
        border-bottom: 1px solid #444;
    }
    .sidebar-mobile-link:hover {
        background: #333;
    }

    .sidebar-submenu-toggle {
        background: transparent;
        border: 0;
        width: 100%;
        padding: 10px 18px;
        color: #fff;
        text-align: left;
        display: flex;
        align-items: center;
    }

    .sidebar-submenu-items {
        display: none;
        background: #222;
    }
    .sidebar-submenu.open .sidebar-submenu-items {
        display: block;
    }

    .submenu-item {
        padding-left: 40px !important;
    }
</style>
{{-- ===========================================
     SIDEBAR MOBILE (OFFCANVAS)
     - Esta versão aparece SOMENTE no mobile
     - Textos serão ocultados via CSS (app.css)
     - Ícones continuam visíveis
   =========================================== --}}
<div class="bg-dark text-white h-100" id="sidebarMobileContent">

    {{-- ===========================================
         CABEÇALHO COM IDENTIDADE DO SISTEMA
    ============================================ --}}
    <div class="p-3 border-bottom border-secondary d-flex align-items-center">
        <i class="bi bi-calendar-check-fill fs-4 me-2"></i>
        <span class="fw-bold fs-5 sidebar-text">BolsistaControl</span>
        {{-- ← sidebar-text será ocultado via CSS no mobile --}}
    </div>


    {{-- ===========================================
         USUÁRIO LOGADO
    ============================================ --}}
    <div class="p-3 border-bottom border-secondary d-flex align-items-center">
        <img src="https://www.gravatar.com/avatar/{{ md5(strtolower(trim(auth()->user()->email))) }}?d=mp"
            class="rounded-circle me-2" width="36" height="36">

        <div>
            <div class="fw-semibold sidebar-text">{{ auth()->user()->name }}</div>
            <div class="small text-muted sidebar-text">
                {{ auth()->user()->roles->pluck('name')->first() }}
            </div>
        </div>
    </div>


    {{-- ===========================================
         MENU (APENAS ÍCONES NO MOBILE)
         O TEXTO SERÁ OCULTADO AUTOMATICAMENTE
    ============================================ --}}
    <div class="mt-2">

        {{-- ---------------------
             BOLSISTA
        ---------------------- --}}
        @role('bolsista')
            <div class="mt-3 px-3 text-uppercase small text-secondary sidebar-text">
                Bolsista
            </div>

            <a href="{{ route('dashboard') }}" class="sidebar-mobile-link d-flex align-items-center">
                <i class="bi bi-speedometer2 sidebar-icon"></i>
                <span class="sidebar-text ms-2">Dashboard</span>
            </a>

            {{-- Submenu Frequências --}}
            <div class="sidebar-submenu mt-1">

                <button class="sidebar-submenu-toggle d-flex align-items-center w-100">
                    <i class="bi bi-file-earmark-bar-graph sidebar-icon"></i>
                    <span class="sidebar-text ms-2">Frequências</span>
                    <i class="bi bi-chevron-down arrow-icon ms-auto"></i>
                </button>

                <div class="sidebar-submenu-items">
                    <a href="{{ route('attendance.my') }}" class="submenu-item sidebar-mobile-link">
                        <i class="bi bi-clock-history sidebar-icon"></i>
                        <span class="sidebar-text ms-2">Minhas Frequências</span>
                    </a>

                    <a href="{{ route('attendance.create') }}" class="submenu-item sidebar-mobile-link">
                        <i class="bi bi-plus-circle sidebar-icon"></i>
                        <span class="sidebar-text ms-2">Registrar</span>
                    </a>

                    <a href="{{ route('attendance.pending') }}" class="submenu-item sidebar-mobile-link">
                        <i class="bi bi-hourglass-split sidebar-icon"></i>
                        <span class="sidebar-text ms-2">Pendentes</span>
                    </a>
                </div>

            </div>
        @endrole


        {{-- ---------------------
             ADMINISTRAÇÃO
        ---------------------- --}}
        @hasanyrole('admin|coordenador_geral|coordenador_adjunto')

            <div class="mt-4 px-3 text-uppercase small text-secondary sidebar-text">
                Administração
            </div>

            {{-- LISTA DE ITENS ADMIN --}}
            @php
                $adminMenu = [
                    ['route' => 'admin.dashboard', 'icon' => 'bi-speedometer'],
                    ['route' => 'admin.units.index', 'icon' => 'bi-building'],
                    ['route' => 'admin.positions.index', 'icon' => 'bi-briefcase'],
                    ['route' => 'admin.scholarship_holders.index', 'icon' => 'bi-people'],
                    ['route' => 'admin.projects.index', 'icon' => 'bi-kanban'],
                    ['route' => 'admin.courses.index', 'icon' => 'bi-mortarboard'],
                    ['route' => 'admin.attendance_records.index', 'icon' => 'bi-journal-check'],
                    ['route' => 'admin.homologations.index', 'icon' => 'bi-check2-square'],
                    ['route' => 'admin.users.index', 'icon' => 'bi-person-gear'],
                    ['route' => 'admin.roles.index', 'icon' => 'bi-key'],
                    ['route' => 'admin.permissions.index', 'icon' => 'bi-shield-check'],
                ];
            @endphp

            @foreach ($adminMenu as $item)
                <a href="{{ route($item['route']) }}" class="sidebar-mobile-link d-flex align-items-center">
                    <i class="bi {{ $item['icon'] }} sidebar-icon"></i>
                    <span class="sidebar-text ms-2">{{ ucfirst(last(explode('.', $item['route']))) }}</span>
                </a>
            @endforeach

        @endhasanyrole


        {{-- ---------------------
             RELATÓRIOS
        ---------------------- --}}
        <div class="mt-4 px-3 text-uppercase small text-secondary sidebar-text">
            Relatórios
        </div>

        {{-- ADMIN + GERAL --}}
        @role(['admin','coordenador_geral'])
            <a href="{{ route('admin.reports.unit_detail') }}" class="sidebar-mobile-link d-flex align-items-center">
                <i class="bi bi-funnel sidebar-icon"></i>
                <span class="sidebar-text ms-2">Por Unidade</span>
            </a>
            <a href="{{ route('admin.reports.report') }}" class="sidebar-mobile-link d-flex align-items-center">
                <i class="bi bi-bar-chart sidebar-icon"></i>
                <span class="sidebar-text ms-2">Consolidado</span>
            </a>
        @endrole

        {{-- COORD. ADJUNTO --}}
        @role('coordenador_adjunto')
            <a href="{{ route('admin.reports.unit_detail') }}" class="sidebar-mobile-link d-flex align-items-center">
                <i class="bi bi-building sidebar-icon"></i>
                <span class="sidebar-text ms-2">Minhas Unidades</span>
            </a>
        @endrole

        {{-- BOLSISTA --}}
        @role('bolsista')
            <a href="{{ route('reports.myReport') }}" class="sidebar-mobile-link d-flex align-items-center">
                <i class="bi bi-file-earmark-person sidebar-icon"></i>
                <span class="sidebar-text ms-2">Meu Relatório</span>
            </a>
        @endrole

    </div>
</div>


{{-- ===========================================
     ESTILOS DO MOBILE
     (ícones centralizados, textos ocultos)
=========================================== --}}
<style>
    .sidebar-mobile-link {
        display: flex;
        align-items: center;
        padding: 12px 18px;
        text-decoration: none;
        color: #fff;
        border-bottom: 1px solid #444;
    }
    .sidebar-mobile-link:hover {
        background: #333;
    }

    .sidebar-submenu-toggle {
        background: transparent;
        border: 0;
        width: 100%;
        padding: 10px 18px;
        color: #fff;
        text-align: left;
        display: flex;
        align-items: center;
    }

    .sidebar-submenu-items {
        display: none;
        background: #222;
    }
    .sidebar-submenu.open .sidebar-submenu-items {
        display: block;
    }

    .submenu-item {
        padding-left: 40px !important;
    }
</style>
