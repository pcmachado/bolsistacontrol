<div class="sidebar mobile-sidebar bg-dark text-white">

    <div class="p-3 border-bottom border-secondary d-flex align-items-center">
        <i class="bi bi-calendar-check fs-4 me-2"></i>
        <span class="fw-bold fs-5">ProBolsas</span>
    </div>

    <div class="p-3 border-bottom border-secondary d-flex align-items-center">
        <img src="https://www.gravatar.com/avatar/{{ md5(strtolower(trim(auth()->user()->email))) }}?d=mp"
             width="36" height="36" class="rounded-circle me-2">
        <div>
            <strong>{{ auth()->user()->name }}</strong>
            <div class="small text-muted">
                {{ ucfirst(str_replace('_',' ', auth()->user()->roles->first()->name ?? '')) }}
            </div>
        </div>
    </div>

    <div class="sidebar-content mt-3">
        <h6 class="sidebar-section-title">Visao Geral</h6>
        <x-sidebar-item route="dashboard" icon="bi bi-speedometer2" title="Dashboard"/>

        @hasanyrole('coordenador_adjunto|coordenador_adjunto_geral|coordenador_geral|bolsista')
            <h6 class="sidebar-section-title mt-4">Minha Area</h6>

            <x-sidebar-item
                route="attendance.my"
                icon="bi bi-calendar-week"
                title="Registros de Frequencia"/>

            <x-sidebar-item
                route="attendance.submissions.my"
                icon="bi bi-send-check"
                title="Submissoes Mensais"/>

            <x-sidebar-item
                route="payments.my"
                icon="bi bi-wallet2"
                title="Meus Pagamentos"/>

            <h6 class="sidebar-section-title mt-4">Relatorios</h6>

            <x-sidebar-item
                route="attendance.reports.index"
                icon="bi bi-file-earmark-text"
                title="Relatorio Mensal"/>

            <x-sidebar-item
                route="attendance.reports.final.create"
                icon="bi bi-file-earmark-person"
                title="Relatorio Final"/>
        @endhasanyrole

        @hasanyrole('coordenador_adjunto|coordenador_adjunto_geral|coordenador_geral')
            <h6 class="sidebar-section-title mt-4">Coordenacao</h6>

            <x-sidebar-item route="attendance.submissions.index"
                            icon="bi bi-calendar-week"
                            title="Frequencias"/>

            <x-sidebar-item route="admin.homologations.index"
                            icon="bi bi-check2-square"
                            title="Homologacoes"/>

            <x-sidebar-item route="admin.payments.dashboard"
                            icon="bi bi-graph-up"
                            title="Financeiro"/>
        @endhasanyrole

        @hasanyrole('admin|coordenador_geral|coordenador_adjunto_geral')
            <h6 class="sidebar-section-title mt-4">Academico</h6>

            <x-sidebar-item route="admin.projects.index" icon="bi bi-kanban" title="Projetos"/>
            <x-sidebar-item route="admin.courses.index" icon="bi bi-mortarboard" title="Cursos"/>
            <x-sidebar-item route="admin.disciplines.index" icon="bi bi-journal-text" title="Disciplinas"/>
            <x-sidebar-item route="admin.class-offerings.index" icon="bi bi-collection" title="Turmas"/>
        @endhasanyrole

        @hasanyrole('admin|coordenador_geral')
            <h6 class="sidebar-section-title mt-4">Administracao</h6>

            <x-sidebar-item route="admin.dashboard" icon="bi bi-speedometer" title="Dashboard Admin"/>
            <x-sidebar-item route="admin.units.index" icon="bi bi-building" title="Unidades"/>
            <x-sidebar-item route="admin.positions.index" icon="bi bi-briefcase" title="Cargos"/>
            <x-sidebar-item route="admin.scholarship_holders.index" icon="bi bi-people" title="Bolsistas"/>
            <x-sidebar-item route="admin.users.index" icon="bi bi-person-gear" title="Usuarios"/>
            <x-sidebar-item route="admin.roles.index" icon="bi bi-key" title="Funcoes"/>
            <x-sidebar-item route="admin.permissions.index" icon="bi bi-shield-lock" title="Permissoes"/>
            <x-sidebar-item route="admin.institutions.index" icon="bi bi-bank" title="Instituicoes"/>
        @endhasanyrole
    </div>
</div>
