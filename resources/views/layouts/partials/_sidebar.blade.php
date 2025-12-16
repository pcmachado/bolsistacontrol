{{-- resources/views/layouts/partials/_sidebar.blade.php --}}

<aside id="sidebar" class="sidebar bg-dark text-white">

    {{-- HEADER DO SIDEBAR --}}
    <div class="sidebar-header">
        <div class="d-flex align-items-center">
            <i class="bi bi-calendar-check sidebar-logo me-2"></i>
            <span class="sidebar-title">BolsistaControl</span>
        </div>

        {{-- BOTÃO PARA COLAPSAR --}}
        <button class="btn btn-sm btn-outline-light sidebar-collapse-btn" data-sidebar-toggle>
            <i class="bi bi-chevron-double-left"></i>
        </button>
    </div>

    {{-- PERFIL DO USUÁRIO --}}
    <div class="sidebar-profile">
        <img src="https://www.gravatar.com/avatar/{{ md5(strtolower(trim(auth()->user()->email))) }}?d=mp"
             class="rounded-circle me-2"
             width="36" height="36">

        <div>
            <strong class="sidebar-text">{{ auth()->user()->name }}</strong>
            <div class="sidebar-text small text-muted">
                {{ ucfirst(str_replace('_',' ', auth()->user()->roles()->pluck('name')->first())) }}
            </div>
        </div>
    </div>


    {{-- CONTEÚDO PRINCIPAL --}}
    <div class="sidebar-content">

        {{-- ========================== --}}
        {{-- BOLSISTA --}}
        {{-- ========================== --}}
        <h6 class="sidebar-section-title">Bolsista</h6>

        <x-sidebar-item 
            route="dashboard"
            icon="bi bi-speedometer2"
            title="Dashboard"
        />

        {{-- SUBMENU FREQUÊNCIAS --}}
        <div class="sidebar-submenu">

            <button class="sidebar-submenu-toggle" data-submenu="frequencias">
                <i class="bi bi-file-earmark-bar-graph sidebar-icon"></i>
                <span class="sidebar-text">Frequências</span>
                <i class="bi bi-caret-down-fill arrow-icon"></i>
            </button>

            <div id="submenu-frequencias" class="sidebar-submenu-items">
                <a href="{{ route('attendance.my') }}" class="sidebar-link submenu-item" data-title="Minhas Frequências">
                    <i class="bi bi-clock-history sidebar-icon text-info"></i>
                    <span class="sidebar-text">Minhas Frequências</span>
                </a>

                <a href="{{ route('attendance.create') }}" class="sidebar-link submenu-item" data-title="Registrar Frequência">
                    <i class="bi bi-plus-circle sidebar-icon text-info"></i>
                    <span class="sidebar-text">Registrar</span>
                </a>

                <a href="{{ route('attendance.pending') }}" class="sidebar-link submenu-item" data-title="Pendentes">
                    <i class="bi bi-clock sidebar-icon text-info"></i>
                    <span class="sidebar-text">Pendentes</span>
                </a>
            </div>

        </div>


        {{-- ========================== --}}
        {{-- ADMINISTRAÇÃO --}}
        {{-- ========================== --}}
        @hasanyrole('admin|coordenador_geral|coordenador_adjunto')

        <h6 class="sidebar-section-title">Administração</h6>

        <x-sidebar-item route="admin.dashboard" icon="bi bi-speedometer" title="Dashboard Admin" />
        <x-sidebar-item route="admin.units.index" icon="bi bi-building" title="Unidades" />
        <x-sidebar-item route="admin.positions.index" icon="bi bi-briefcase" title="Cargos" />
        <x-sidebar-item route="admin.scholarship_holders.index" icon="bi bi-people" title="Bolsistas" />
        <x-sidebar-item route="admin.projects.index" icon="bi bi-kanban" title="Projetos" />
        <x-sidebar-item route="admin.courses.index" icon="bi bi-mortarboard" title="Cursos" />
        <x-sidebar-item route="admin.attendance_records.index" icon="bi bi-calendar-week" title="Frequências" />
        <x-sidebar-item route="admin.homologations.index" icon="bi bi-check2-square" title="Homologações" />
        <x-sidebar-item route="admin.users.index" icon="bi bi-person-gear" title="Usuários" />
        <x-sidebar-item route="admin.roles.index" icon="bi bi-key" title="Funções" />
        <x-sidebar-item route="admin.permissions.index" icon="bi bi-shield-lock" title="Permissões" />

        @endhasanyrole


        {{-- ========================== --}}
        {{-- RELATÓRIOS --}}
        {{-- ========================== --}}
        <h6 class="sidebar-section-title">Relatórios</h6>

        @role(['admin','coordenador_geral','coordenador_adjunto'])
            <x-sidebar-item route="admin.reports.unit_detail" icon="bi bi-funnel" title="Por Unidade" />
            <x-sidebar-item route="admin.reports.report" icon="bi bi-bar-chart" title="Consolidado Mensal" />
        @endrole

        @role('bolsista')
            <x-sidebar-item route="reports.myReport" icon="bi bi-file-earmark-person" title="Meu Relatório" />
        @endrole

    </div>
</aside>
