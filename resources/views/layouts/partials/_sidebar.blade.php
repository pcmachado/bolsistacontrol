<aside id="sidebar" class="sidebar d-flex flex-column flex-shrink-0 p-3 border-end border-secondary">

    {{-- LOGO --}}
    <a href="{{ route('dashboard') }}"
       class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-decoration-none px-2">

        <img src="{{ asset('images/probolsas_fundo_escuro.png') }}"
             alt="ProBolsas"
             width="200"
             class="me-2 sidebar-logo">
    </a>

    <hr class="my-3">

    {{-- PERFIL --}}
    <div class="d-flex align-items-center px-2 mb-3 sidebar-profile">
        <img src="https://www.gravatar.com/avatar/{{ md5(strtolower(trim(auth()->user()->email))) }}?d=mp"
             width="38"
             height="38"
             class="rounded-circle me-2">

        <div class="sidebar-profile-text">
            <strong class="d-block text-truncate" style="max-width:160px;">
                {{ auth()->user()->name }}
            </strong>
            <small class="text-secondary">
                {{ ucfirst(str_replace('_',' ', auth()->user()->roles->first()->name ?? '')) }}
            </small>
        </div>
    </div>

    <hr class="my-2">

    {{-- MENU --}}
    <div class="sidebar-content flex-grow-1 overflow-auto">

        {{-- VISÃO GERAL --}}
        <h6 class="sidebar-section-title text-uppercase small fw-bold text-body-secondary px-2">
            Visão Geral
        </h6>

        <ul class="nav nav-pills flex-column mb-3">
            <li class="nav-item">
                <x-sidebar-item route="dashboard" icon="bi bi-speedometer2" title="Dashboard"/>
            </li>
        </ul>

        {{-- ÁREA DO BOLSISTA --}}
        <h6 class="sidebar-section-title text-uppercase small fw-bold text-body-secondary px-2 mt-3">
            Minha Área
        </h6>

        <ul class="nav nav-pills flex-column mb-3">
            <li><x-sidebar-item route="attendance.my" icon="bi bi-calendar-week" title="Registros de Frequência"/></li>
            <li><x-sidebar-item route="my-attendance.submissions.my" icon="bi bi-send-check" title="Submissões Mensais"/></li>
            <li><x-sidebar-item route="payments.my" icon="bi bi-wallet2" title="Meus Pagamentos"/></li>
        </ul>

        <h6 class="sidebar-section-title text-uppercase small fw-bold text-body-secondary px-2 mt-3">
            Meus Relatórios
        </h6>

        <ul class="nav nav-pills flex-column mb-3">
            <li><x-sidebar-item route="attendance.reports.index" icon="bi bi-file-earmark-text" title="Relatório Mensal"/></li>
            <li><x-sidebar-item route="attendance.reports.final.index" icon="bi bi-file-earmark-person" title="Relatório Final"/></li>
        </ul>

        @if(auth()->user()->canAccessTeacher())
        <h6 class="sidebar-section-title mt-4">
            Professor
        </h6>
            <ul class="nav nav-pills flex-column mb-3">
                <li><x-sidebar-item route="teacher.dashboard" icon="bi bi-easel2" title="Meu Dashboard"/></li>
                <li><x-sidebar-item route="teacher.classes" icon="bi bi-journal-check" title="Minhas Turmas"/></li>
            </ul>
        @endif

        {{-- COORDENAÇÃO --}}
        @if(auth()->user()->canAccessCoordination())
            <h6 class="sidebar-section-title text-uppercase small fw-bold text-body-secondary px-2 mt-3">
                Coordenação
            </h6>

            <ul class="nav nav-pills flex-column mb-3">
                <li><x-sidebar-item route="attendance.submissions.index" icon="bi bi-calendar-week" title="Frequências"/></li>
                <li><x-sidebar-item route="admin.homologations.index" icon="bi bi-check2-square" title="Homologações"/></li>
                {{-- <li><x-sidebar-item route="admin.homologations.pending" icon="bi bi-hourglass-split" title="Pendentes"/></li>
                <li><x-sidebar-item route="admin.homologations.late" icon="bi bi-exclamation-circle" title="Atrasados"/></li> --}}
            </ul>
        @endif

        @if(auth()->user()->hasAnyRole(['admin','coordenador_geral','coordenador_adjunto_geral','coordenador_adjunto']))
            <h6 class="sidebar-section-title mt-4">
                Gestão
            </h6>
            <ul class="nav nav-pills flex-column mb-3">
                <li><x-sidebar-item route="admin.scholarship_holders.impersonate" icon="bi bi-people" title="Bolsistas" /></li>
                <li><x-sidebar-item route="attendance.submissions.index" icon="bi bi-calendar-check" title="Frequências" /></li>
                <li><x-sidebar-item route="admin.payments.index" icon="bi bi-cash-stack" title="Pagamentos" /></li>
            </ul>
        @endif

        {{-- Financeiro --}}
        @if(auth()->user()->canAccessFinancial())
            <h6 class="sidebar-section-title text-uppercase small fw-bold text-body-secondary px-2 mt-3">
                Financeiro
            </h6>
                <li><x-sidebar-item route="admin.financial-reports.index" icon="bi bi-graph-up" title="Financeiro"/></li>
                <li><x-sidebar-item route="admin.payments.dashboard" icon="bi bi-graph-up" title="Financeiro (Bolsistas)"/></li>
                <li><x-sidebar-item route="admin.payments.index" icon="bi bi-wallet2" title="Pagamentos"/></li>
                <li><x-sidebar-item route="admin.student-payments.dashboard" icon="bi bi-graph-up-arrow" title="Financeiro (Alunos)"/></li>
                <li><x-sidebar-item route="admin.student-payments.index" icon="bi bi-cash-stack" title="Pagamentos Alunos"/></li>
                <li><x-sidebar-item route="admin.payments.reports.monthly" icon="bi bi-calendar3" title="Fechamento Mensal"/></li>
                <li><x-sidebar-item route="admin.financial-closures.index" icon="bi bi-lock" title="Fechamentos"/></li>
            </ul>
        @endif

        {{-- ACADÊMICO --}}
        @if(auth()->user()->canAccessCoordination())
            <h6 class="sidebar-section-title text-uppercase small fw-bold text-body-secondary px-2 mt-3">
                Acadêmico
            </h6>

            <ul class="nav nav-pills flex-column mb-3">
                <li><x-sidebar-item route="admin.dashboard.academic" icon="bi bi-bar-chart" title="Dashboard Acadêmico"/></li>
                <li><x-sidebar-item route="admin.academic-reports.class-sessions.global" icon="bi bi-file-earmark-bar-graph" title="Rel. de Aulas"/></li>
                <li><x-sidebar-item route="admin.projects.index" icon="bi bi-kanban" title="Projetos"/></li>
                <li><x-sidebar-item route="admin.courses.index" icon="bi bi-mortarboard" title="Cursos"/></li>
                <li><x-sidebar-item route="admin.disciplines.index" icon="bi bi-journal-text" title="Disciplinas"/></li>
                <li><x-sidebar-item route="admin.class-offerings.index" icon="bi bi-collection" title="Turmas"/></li>
            </ul>
        @endif

        {{-- ADMINISTRAÇÃO --}}
        @if(auth()->user()->canAccessAdministrative())
            <h6 class="sidebar-section-title text-uppercase small fw-bold text-body-secondary px-2 mt-3">
                Administração
            </h6>

            <ul class="nav nav-pills flex-column">
                <li><x-sidebar-item route="admin.dashboard" icon="bi bi-speedometer" title="Dashboard Admin"/></li>
                <li><x-sidebar-item route="admin.units.index" icon="bi bi-building" title="Unidades"/></li>
                <li><x-sidebar-item route="admin.positions.index" icon="bi bi-briefcase" title="Cargos"/></li>
                <li><x-sidebar-item route="admin.scholarship_holders.index" icon="bi bi-people" title="Bolsistas"/></li>
                <li><x-sidebar-item route="admin.users.index" icon="bi bi-person-gear" title="Usuários"/></li>
                <li><x-sidebar-item route="admin.roles.index" icon="bi bi-key" title="Funções"/></li>
                <li><x-sidebar-item route="admin.permissions.index" icon="bi bi-shield-lock" title="Permissões"/></li>
                <li><x-sidebar-item route="admin.institutions.index" icon="bi bi-bank" title="Instituições"/></li>
                <li><x-sidebar-item route="admin.settings.alerts" icon="bi bi-bell" title="Alertas"/></li>
                <li><x-sidebar-item route="admin.email-templates.index" icon="bi bi-envelope-paper" title="Templates de Email"/></li>
                <li><x-sidebar-item route="admin.notification-settings.index" icon="bi bi-gear" title="Config. Notificações"/></li>
                <li><x-sidebar-item route="admin.document-templates.index" icon="bi bi-file-earmark-richtext" title="Modelos"/></li>
                {{-- <li><x-sidebar-item route="admin.settings" icon="bi bi-gear" title="Configurações"/></li> --}}
            </ul>
        @endif

        <h6 class="sidebar-section-title mt-4">Ajuda</h6>

        <x-sidebar-item route="manual.index" icon="bi bi-book" title="Manuais"/>

    </div>
</aside>