<aside id="sidebar" class="sidebar bg-dark text-white">

    {{-- HEADER --}}
    <div class="sidebar-header px-3 py-2 d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center">
            <i class="bi bi-calendar-check sidebar-logo me-2"></i>
            <span class="sidebar-title">ProBolsas</span>
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

        {{-- ================================================= --}}
        {{-- VISÃO GERAL --}}
        {{-- ================================================= --}}
        <h6 class="sidebar-section-title">Visão Geral</h6>
        <x-sidebar-item route="dashboard" icon="bi bi-speedometer2" title="Dashboard"/>

        {{-- ================================================= --}}
        {{-- ÁREA DO BOLSISTA --}}
        {{-- ================================================= --}}
        @hasanyrole('coordenador_adjunto|coordenador_adjunto_geral|coordenador_geral|bolsista')
            <h6 class="sidebar-section-title mt-4">Minha Área</h6>

            {{-- Frequências --}}
            <x-sidebar-item route="attendance.my" icon="bi bi-calendar-week" title="Registros de Frequência"/>
            <x-sidebar-item route="my-attendance.submissions.my" icon="bi bi-send-check" title="Submissões Mensais"/>

            {{-- Pagamentos --}}
            <x-sidebar-item route="payments.my" icon="bi bi-wallet2" title="Meus Pagamentos"/>

            {{-- Relatórios --}}
            <h6 class="sidebar-section-title mt-4">Meus Relatórios</h6>
            <x-sidebar-item route="attendance.reports.index" icon="bi bi-file-earmark-text" title="Relatório Mensal"/>
            <x-sidebar-item route="attendance.reports.final.create" icon="bi bi-file-earmark-person" title="Relatório Final"/>
        @endhasanyrole

        {{-- ================================================= --}}
        {{-- COORDENAÇÃO --}}
        {{-- ================================================= --}}
        @hasanyrole('coordenador_adjunto|coordenador_adjunto_geral|coordenador_geral')
            <h6 class="sidebar-section-title mt-4">Coordenação</h6>

            {{-- Frequência --}}
            <x-sidebar-item route="attendance.submissions.index" icon="bi bi-calendar-week" title="Frequências"/>

            {{-- Homologações --}}
            <x-sidebar-item route="admin.homologations.index" icon="bi bi-check2-square" title="Homologações"/>

            <x-sidebar-item route="admin.financial-reports.index" icon="bi bi-graph-up" title="Financeiro"/>

            {{-- Financeiro Bolsistas --}}
            <x-sidebar-item route="admin.payments.dashboard" icon="bi bi-graph-up" title="Financeiro (Bolsistas)"/>      
            <x-sidebar-item route="admin.payments.index" icon="bi bi-wallet2" title="Pagamentos"/>

            {{-- Financeiro Alunos --}}
            <x-sidebar-item route="admin.student-payments.dashboard" icon="bi bi-graph-up-arrow" title="Financeiro (Alunos)"/>
            <x-sidebar-item route="admin.student-payments.index" icon="bi bi-cash-stack" title="Pagamentos Alunos"/>

        @endhasanyrole

        {{-- ================================================= --}}
        {{-- ACADÊMICO --}}
        {{-- ================================================= --}}
        @hasanyrole('admin|coordenador_geral|coordenador_adjunto_geral')
            <h6 class="sidebar-section-title mt-4">Acadêmico</h6>

            <x-sidebar-item route="admin.projects.index" icon="bi bi-kanban" title="Projetos"/>
            <x-sidebar-item route="admin.courses.index" icon="bi bi-mortarboard" title="Cursos"/>
            <x-sidebar-item route="admin.disciplines.index" icon="bi bi-journal-text" title="Disciplinas"/>
            <x-sidebar-item route="admin.class-offerings.index" icon="bi bi-collection" title="Turmas"/>

        @endhasanyrole

        {{-- ================================================= --}}
        {{-- ADMINISTRAÇÃO --}}
        {{-- ================================================= --}}
        @hasanyrole('admin|coordenador_geral')
            <h6 class="sidebar-section-title mt-4">Administração</h6>

            <x-sidebar-item route="admin.dashboard" icon="bi bi-speedometer" title="Dashboard Admin"/>
            <x-sidebar-item route="admin.units.index" icon="bi bi-building" title="Unidades"/>
            <x-sidebar-item route="admin.positions.index" icon="bi bi-briefcase" title="Cargos"/>
            <x-sidebar-item route="admin.scholarship_holders.index" icon="bi bi-people" title="Bolsistas"/>
            <x-sidebar-item route="admin.users.index" icon="bi bi-person-gear" title="Usuários"/>
            <x-sidebar-item route="admin.roles.index" icon="bi bi-key" title="Funções"/>
            <x-sidebar-item route="admin.permissions.index" icon="bi bi-shield-lock" title="Permissões"/>
            <x-sidebar-item route="admin.institutions.index" icon="bi bi-bank" title="Instituições"/>
        @endhasanyrole

    </div>
</aside>
