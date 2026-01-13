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

        {{-- VISÃO GERAL --}}
        <h6 class="sidebar-section-title">Visão Geral</h6>
        <x-sidebar-item route="dashboard" icon="bi bi-speedometer2" title="Dashboard"/>

        {{-- ================================================= --}}
        {{-- ACADÊMICO --}}
        {{-- ================================================= --}}
        @hasanyrole('admin|coordenador_geral|coordenador_adjunto_geral|coordenador_adjunto')
        <h6 class="sidebar-section-title mt-4">Acadêmico</h6>

        <x-sidebar-item route="admin.projects.index" icon="bi bi-kanban" title="Projetos"/>
        <x-sidebar-item route="admin.courses.index" icon="bi bi-mortarboard" title="Cursos"/>
        <x-sidebar-item route="admin.disciplines.index" icon="bi bi-journal-text" title="Disciplinas"/>
        <x-sidebar-item route="admin.class-offerings.index" icon="bi bi-collection" title="Turmas"/>
        <x-sidebar-item route="admin.attendance_records.index" icon="bi bi-calendar-week" title="Frequências"/>
        <x-sidebar-item route="admin.homologations.index" icon="bi bi-check2-square" title="Homologações"/>
        @endhasanyrole

        {{-- ================================================= --}}
        {{-- FINANCEIRO --}}
        {{-- ================================================= --}}
        @hasanyrole('admin|coordenador_geral|coordenador_adjunto_geral')
        <h6 class="sidebar-section-title mt-4">Financeiro</h6>

        <x-sidebar-item route="admin.payments.create" icon="bi bi-cash-coin" title="Gerar Pagamentos"/>
        {{-- <x-sidebar-item route="admin.payments.execution" icon="bi bi-currency-exchange" title="Execução Financeira"/> --}}
        <x-sidebar-item route="admin.payments.dashboard" icon="bi bi-graph-up" title="Dashboard Financeiro"/>
        {{-- <x-sidebar-item route="admin.payments.receipts" icon="bi bi-receipt" title="Recibos"/> --}}
        @endhasanyrole

        {{-- ================================================= --}}
        {{-- RELATÓRIOS --}}
        {{-- ================================================= --}}
        <h6 class="sidebar-section-title mt-4">Relatórios</h6>

        {{-- @hasanyrole('admin|coordenador_geral|coordenador_adjunto_geral|coordenador_adjunto')
            <x-sidebar-item route="admin.reports.academic" icon="bi bi-bar-chart" title="Acadêmicos"/>
            <x-sidebar-item route="admin.reports.financial" icon="bi bi-cash-stack" title="Financeiros"/>
        @endhasanyrole --}}

        {{-- ================================================= --}}
        {{-- ÁREA DO BOLSISTA --}}
        {{-- ================================================= --}}
        @hasanyrole('bolsista|coordenador_adjunto_geral|coordenador_adjunto')
        <h6 class="sidebar-section-title mt-4">Minha Área</h6>

        <x-sidebar-item route="attendance.my" icon="bi bi-clock-history" title="Minhas Frequências"/>
        <x-sidebar-item route="payments.my" icon="bi bi-wallet2" title="Meus Pagamentos"/>
        {{-- <x-sidebar-item route="payments.my.receipts" icon="bi bi-receipt-cutoff" title="Meus Recibos"/> --}}
        <x-sidebar-item route="reports.myReport" icon="bi bi-file-earmark-person" title="Meu Relatório"/>
        @endhasanyrole

        {{-- ================================================= --}}
        {{-- ADMINISTRAÇÃO --}}
        {{-- ================================================= --}}
        @hasanyrole('admin|coordenador_geral|coordenador_adjunto_geral')
        <h6 class="sidebar-section-title mt-4">Administração</h6>

        <x-sidebar-item route="admin.dashboard" icon="bi bi-speedometer" title="Dashboard Admin"/>
        <x-sidebar-item route="admin.units.index" icon="bi bi-building" title="Unidades"/>
        <x-sidebar-item route="admin.positions.index" icon="bi bi-briefcase" title="Cargos"/>
        <x-sidebar-item route="admin.scholarship_holders.index" icon="bi bi-people" title="Bolsistas"/>
        <x-sidebar-item route="admin.users.index" icon="bi bi-person-gear" title="Usuários"/>
        <x-sidebar-item route="admin.roles.index" icon="bi bi-key" title="Funções"/>
        <x-sidebar-item route="admin.permissions.index" icon="bi bi-shield-lock" title="Permissões"/>
        {{-- <x-sidebar-item route="admin.funding-sources.index" icon="bi bi-piggy-bank" title="Fontes de Fomento"/> --}}
        @endhasanyrole

        @role('admin|coordenador_geral')
        <x-sidebar-item route="admin.institutions.index" icon="bi bi-bank" title="Instituições"/>
        @endrole

    </div>

</aside>
