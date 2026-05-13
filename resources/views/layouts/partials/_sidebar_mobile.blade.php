<div class="sidebar mobile-sidebar d-flex flex-column flex-shrink-0 p-3 border-end border-secondary" style="background-color: #0b204e; min-height: 100vh;">

    {{-- LOGO --}}
    <a href="{{ route('dashboard') }}"
       class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-decoration-none px-2">

        {{-- Usando a mesma logo da versão desktop --}}
        <img src="{{ asset('images/probolsas_fundo_escuro.png') }}"
             alt="ProBolsas"
             width="200"
             class="me-2 sidebar-logo">
    </a>

    <hr class="my-3 border-secondary">

    {{-- PERFIL --}}
    <div class="d-flex align-items-center px-2 mb-3 sidebar-profile">
        <img src="https://www.gravatar.com/avatar/{{ md5(strtolower(trim(auth()->user()->email))) }}?d=mp"
             width="38"
             height="38"
             class="rounded-circle me-2" style="border: 2px solid #2563eb;">

        <div class="sidebar-profile-text">
            <strong class="d-block text-truncate text-white" style="max-width:160px;">
                {{ auth()->user()->name }}
            </strong>
            <small class="text-secondary" style="color: #d1d5db !important;">
                {{ ucfirst(str_replace('_',' ', auth()->user()->roles->first()->name ?? '')) }}
            </small>
        </div>
    </div>

    <hr class="my-2 border-secondary">

    {{-- MENU --}}
    <div class="sidebar-content flex-grow-1 overflow-auto mt-2">

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
        @hasanyrole('coordenador_adjunto|coordenador_adjunto_geral|coordenador_geral|bolsista')
            <h6 class="sidebar-section-title text-uppercase small fw-bold text-body-secondary px-2 mt-3">
                Minha Área
            </h6>
            <ul class="nav nav-pills flex-column mb-3">
                <li class="nav-item"><x-sidebar-item route="attendance.my" icon="bi bi-calendar-week" title="Registros de Frequência"/></li>
                <li class="nav-item"><x-sidebar-item route="my-attendance.submissions.my" icon="bi bi-send-check" title="Submissões Mensais"/></li>
                <li class="nav-item"><x-sidebar-item route="payments.my" icon="bi bi-wallet2" title="Meus Pagamentos"/></li>
            </ul>

            <h6 class="sidebar-section-title text-uppercase small fw-bold text-body-secondary px-2 mt-3">
                Relatórios
            </h6>
            <ul class="nav nav-pills flex-column mb-3">
                <li class="nav-item"><x-sidebar-item route="attendance.reports.index" icon="bi bi-file-earmark-text" title="Relatório Mensal"/></li>
                <li class="nav-item"><x-sidebar-item route="attendance.reports.final.create" icon="bi bi-file-earmark-person" title="Relatório Final"/></li>
            </ul>
        @endhasanyrole

        {{-- COORDENAÇÃO --}}
        @hasanyrole('coordenador_adjunto|coordenador_adjunto_geral|coordenador_geral')
            <h6 class="sidebar-section-title text-uppercase small fw-bold text-body-secondary px-2 mt-3">
                Coordenação
            </h6>
            <ul class="nav nav-pills flex-column mb-3">
                <li class="nav-item"><x-sidebar-item route="attendance.submissions.index" icon="bi bi-calendar-week" title="Frequências"/></li>
                <li class="nav-item"><x-sidebar-item route="admin.homologations.index" icon="bi bi-check2-square" title="Homologações"/></li>
                <li class="nav-item"><x-sidebar-item route="admin.payments.dashboard" icon="bi bi-graph-up" title="Financeiro"/></li>
                <li class="nav-item"><x-sidebar-item route="admin.student-payments.dashboard" icon="bi bi-graph-up-arrow" title="Financeiro Alunos"/></li>
                <li class="nav-item"><x-sidebar-item route="admin.student-payments.index" icon="bi bi-cash-stack" title="Pagamentos Alunos"/></li>
            </ul>
        @endhasanyrole

        {{-- ACADÊMICO --}}
        @hasanyrole('admin|coordenador_geral|coordenador_adjunto_geral')
            <h6 class="sidebar-section-title text-uppercase small fw-bold text-body-secondary px-2 mt-3">
                Acadêmico
            </h6>
            <ul class="nav nav-pills flex-column mb-3">
                <li class="nav-item"><x-sidebar-item route="admin.projects.index" icon="bi bi-kanban" title="Projetos"/></li>
                <li class="nav-item"><x-sidebar-item route="admin.courses.index" icon="bi bi-mortarboard" title="Cursos"/></li>
                <li class="nav-item"><x-sidebar-item route="admin.disciplines.index" icon="bi bi-journal-text" title="Disciplinas"/></li>
                <li class="nav-item"><x-sidebar-item route="admin.class-offerings.index" icon="bi bi-collection" title="Turmas"/></li>
                <li class="nav-item"><x-sidebar-item route="admin.funding-sources.index" icon="bi bi-piggy-bank" title="Formas de Fomento"/></li>
            </ul>
        @endhasanyrole

        {{-- ADMINISTRAÇÃO --}}
        @hasanyrole('admin|coordenador_geral')
            <h6 class="sidebar-section-title text-uppercase small fw-bold text-body-secondary px-2 mt-3">
                Administração
            </h6>
            <ul class="nav nav-pills flex-column">
                <li class="nav-item"><x-sidebar-item route="admin.dashboard" icon="bi bi-speedometer" title="Dashboard Admin"/></li>
                <li class="nav-item"><x-sidebar-item route="admin.units.index" icon="bi bi-building" title="Unidades"/></li>
                <li class="nav-item"><x-sidebar-item route="admin.positions.index" icon="bi bi-briefcase" title="Cargos"/></li>
                <li class="nav-item"><x-sidebar-item route="admin.scholarship_holders.index" icon="bi bi-people" title="Bolsistas"/></li>
                <li class="nav-item"><x-sidebar-item route="admin.users.index" icon="bi bi-person-gear" title="Usuários"/></li>
                <li class="nav-item"><x-sidebar-item route="admin.roles.index" icon="bi bi-key" title="Funções"/></li>
                <li class="nav-item"><x-sidebar-item route="admin.permissions.index" icon="bi bi-shield-lock" title="Permissões"/></li>
                <li class="nav-item"><x-sidebar-item route="admin.institutions.index" icon="bi bi-bank" title="Instituições"/></li>
                <li class="nav-item"><x-sidebar-item route="admin.email-templates.index" icon="bi bi-envelope-paper" title="Templates de Email"/></li>
                <li class="nav-item"><x-sidebar-item route="admin.notification-settings.index" icon="bi bi-gear" title="Config. Notificações"/></li>
            </ul>
        @endhasanyrole
    </div>
</div>
