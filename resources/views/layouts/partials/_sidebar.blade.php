<aside id="sidebar" class="sidebar bg-dark text-white">

    {{-- HEADER --}}
    <div class="sidebar-header px-3 py-2 d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center">
            <i class="bi bi-calendar-check sidebar-logo me-2"></i>
            <span class="sidebar-title">BolsistaControl</span>
        </div>

        <button class="btn btn-sm btn-outline-light sidebar-collapse-btn" data-sidebar-toggle>
            <i class="bi sidebar-collapse-btn-icon"></i>
        </button>
    </div>

    {{-- PERFIL --}}
    <div class="sidebar-profile px-3 py-3 border-bottom border-secondary d-flex align-items-center">
        <img src="https://www.gravatar.com/avatar/{{ md5(strtolower(trim(auth()->user()->email))) }}?d=mp"
             width="36" height="36" class="rounded-circle me-2">

        <div class="sidebar-profile-text">
            <strong>{{ auth()->user()->name }}</strong>
            <div class="small text-muted">
                {{ ucfirst(str_replace('_',' ', auth()->user()->roles->first()->name ?? '')) }}
            </div>
        </div>
    </div>

    {{-- CONTEÚDO --}}
    <div class="sidebar-content mt-3">

        {{-- BOLSISTA --}}
        <h6 class="sidebar-section-title">Bolsista</h6>

        <x-sidebar-item route="dashboard" icon="bi bi-speedometer2" title="Dashboard"/>

        {{-- SUBMENU FREQUÊNCIAS --}}
        <div class="sidebar-submenu">
            <button class="sidebar-submenu-toggle">
                <i class="bi bi-file-earmark-bar-graph sidebar-icon me-2"></i>
                <span class="sidebar-text">Frequências</span>
                <i class="bi bi-caret-down-fill arrow-icon ms-auto"></i>
            </button>

            <div class="sidebar-submenu-items">
                <x-sidebar-item route="attendance.my" icon="bi bi-clock-history" title="Minhas Frequências"/>
                <x-sidebar-item route="attendance.create" icon="bi bi-plus-circle" title="Registrar"/>
                <x-sidebar-item route="attendance.pending" icon="bi bi-hourglass-split" title="Pendentes"/>
            </div>
        </div>

        {{-- ADMINISTRATIVO --}}
        @hasanyrole('admin|coordenador_geral|coordenador_adjunto')

        <h6 class="sidebar-section-title mt-4">Administração</h6>

        <x-sidebar-item route="admin.dashboard" icon="bi bi-speedometer" title="Dashboard Admin"/>
        <x-sidebar-item route="admin.units.index" icon="bi bi-building" title="Unidades"/>
        <x-sidebar-item route="admin.positions.index" icon="bi bi-briefcase" title="Cargos"/>
        <x-sidebar-item route="admin.scholarship_holders.index" icon="bi bi-people" title="Bolsistas"/>
        <x-sidebar-item route="admin.projects.index" icon="bi bi-kanban" title="Projetos"/>
        <x-sidebar-item route="admin.courses.index" icon="bi bi-mortarboard" title="Cursos"/>
        <x-sidebar-item route="admin.attendance_records.index" icon="bi bi-calendar-week" title="Frequências"/>
        <x-sidebar-item route="admin.homologations.index" icon="bi bi-check2-square" title="Homologações"/>
        <x-sidebar-item route="admin.users.index" icon="bi bi-person-gear" title="Usuários"/>
        <x-sidebar-item route="admin.roles.index" icon="bi bi-key" title="Funções"/>
        <x-sidebar-item route="admin.permissions.index" icon="bi bi-shield-lock" title="Permissões"/>

        @endhasanyrole

        {{-- RELATÓRIOS --}}
        <h6 class="sidebar-section-title mt-4">Relatórios</h6>

        @role(['admin','coordenador_geral','coordenador_adjunto'])
            <x-sidebar-item route="admin.reports.unit_detail" icon="bi bi-funnel" title="Por Unidade"/>
            <x-sidebar-item route="admin.reports.report" icon="bi bi-bar-chart" title="Consolidado Mensal"/>
        @endrole

        @role('bolsista')
            <x-sidebar-item route="reports.myReport" icon="bi bi-file-earmark-person" title="Meu Relatório"/>
        @endrole

    </div>

</aside>
