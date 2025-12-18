<div class="sidebar bg-dark text-white">

    <div class="p-3 border-bottom border-secondary d-flex align-items-center">
        <i class="bi bi-calendar-check fs-4 me-2"></i>
        <span class="fw-bold fs-5">BolsistaControl</span>
    </div>

    <div class="p-3 border-bottom border-secondary d-flex align-items-center">
        <img src="https://www.gravatar.com/avatar/{{ md5(strtolower(trim(auth()->user()->email))) }}?d=mp"
             width="36" height="36" class="rounded-circle me-2">
        <div>
            <strong>{{ auth()->user()->name }}</strong>
            <div class="text-muted small">{{ auth()->user()->email }}</div>
        </div>
    </div>

    <div class="mt-3">

        {{-- BOLSISTA --}}
        @role('bolsista')
            <h6 class="sidebar-section">Bolsista</h6>

            <a href="{{ route('dashboard') }}" class="sidebar-link">
                <i class="bi bi-speedometer2 me-2"></i> Dashboard
            </a>
            <a href="{{ route('attendance.my') }}" class="sidebar-link">
                <i class="bi bi-clock-history me-2"></i> Minhas Frequências
            </a>
            <a href="{{ route('attendance.create') }}" class="sidebar-link">
                <i class="bi bi-plus-circle me-2"></i> Registrar
            </a>
            <a href="{{ route('attendance.pending') }}" class="sidebar-link">
                <i class="bi bi-hourglass-split me-2"></i> Pendentes
            </a>
        @endrole

        {{-- ADMIN --}}
        @hasanyrole('admin|coordenador_geral|coordenador_adjunto')
            <h6 class="sidebar-section mt-3">Administração</h6>

            <a href="{{ route('admin.dashboard') }}" class="sidebar-link">
                <i class="bi bi-speedometer me-2"></i> Dashboard Admin
            </a>
            <a href="{{ route('admin.units.index') }}" class="sidebar-link">
                <i class="bi bi-building me-2"></i> Unidades
            </a>
            <a href="{{ route('admin.projects.index') }}" class="sidebar-link">
                <i class="bi bi-kanban me-2"></i> Projetos
            </a>
            <a href="{{ route('admin.courses.index') }}" class="sidebar-link">
                <i class="bi bi-mortarboard me-2"></i> Cursos
            </a>
            <a href="{{ route('admin.users.index') }}" class="sidebar-link">
                <i class="bi bi-person-gear me-2"></i> Usuários
            </a>
        @endhasanyrole

        {{-- RELATÓRIOS --}}
        <h6 class="sidebar-section mt-3">Relatórios</h6>

        @role(['admin','coordenador_geral','coordenador_adjunto'])
            <a href="{{ route('admin.reports.unit_detail') }}" class="sidebar-link">
                <i class="bi bi-funnel me-2"></i> Por Unidade
            </a>
        @endrole

        @role('bolsista')
            <a href="{{ route('reports.myReport') }}" class="sidebar-link">
                <i class="bi bi-file-earmark-person me-2"></i> Meu Relatório
            </a>
        @endrole

    </div>
</div>
