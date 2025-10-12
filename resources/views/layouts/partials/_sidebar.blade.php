<div class="bg-dark border-end" id="sidebar-wrapper">
    <div class="sidebar-heading border-bottom bg-dark text-white p-3">
        <a href="{{ route('home') }}" class="text-white text-decoration-none d-flex align-items-center">
            <i class="bi bi-calendar-check-fill me-2 fs-4"></i>
            <span class="sidebar-text fs-5 fw-bold">BolsistaControl</span>
        </a>
        <hr class="bg-secondary my-2">
        <div class="d-flex align-items-center user-profile">
            <img src="https://www.gravatar.com/avatar/{{ md5(strtolower(trim(auth()->user()->email))) }}?d=mp"
                 alt="Avatar" width="32" height="32" class="rounded-circle me-2">
            <span class="text-light small sidebar-text">{{ auth()->user()->name }}</span>
        </div>
    </div>

    <div class="list-group list-group-flush">
        {{-- Links do Bolsista --}}
        <h6 class="sidebar-heading px-3 mt-3 text-secondary text-uppercase sidebar-text">Bolsista</h6>
        <a class="list-group-item list-group-item-action list-group-item-dark p-3" href="{{ route('dashboard') }}">
            <i class="bi bi-speedometer2 fa-fw me-3"></i><span class="sidebar-text">Dashboard</span>
        </a>
        <a class="list-group-item list-group-item-action list-group-item-dark p-3 d-flex justify-content-between align-items-center"
            data-bs-toggle="collapse" href="#submenuAttendances" role="button" aria-expanded="false" aria-controls="submenuAttendances">
            <span class="d-none d-lg-inline">
                <i class="bi bi-file-earmark-bar-graph fa-fw me-3 text-warning"></i>
                <span class="sidebar-text">Frequências</span>
            </span>
            <span class="d-lg-none" data-bs-toggle="tooltip" data-bs-placement="right" title="Frequências">
                <i class="bi bi-file-earmark-bar-graph fa-fw text-warning"></i>
            </span>
            <i class="bi bi-caret-down-fill"></i>
        </a>
        <div class="collapse" id="submenuAttendances">
            <ul class="list-group list-group-flush">
                <li>
                    <a class="list-group-item list-group-item-action ps-5"
                    href="{{ route('attendance.index') }}"
                    data-bs-toggle="tooltip" data-bs-placement="right" title="Minhas Frequências">
                        <i class="bi bi-clock-history me-2 text-info"></i>
                        <span class="d-none d-lg-inline">Minhas Frequências</span>
                    </a>
                </li>
                <li>
                    <a class="list-group-item list-group-item-action ps-5"
                    href="{{ route('attendance.create') }}">
                        <i class="bi bi-plus-circle me-2 text-info"></i>
                        <span class="d-none d-lg-inline">Registrar Frequência</span>
                    </a>
                </li>
                <li>
                    <a class="list-group-item list-group-item-action ps-5"
                    href="{{ route('attendance.pending') }}">
                        <i class="bi bi-clock-history me-2 text-info"></i>
                        <span class="d-none d-lg-inline">Registros Pendentes</span>
                    </a>
                </li>
            </ul>
        </div>

        {{-- Administração (somente para papéis específicos) --}}
        @hasanyrole('admin|coordenador_geral|coordenador_adjunto')
        <h6 class="sidebar-heading px-3 mt-4 text-secondary text-uppercase sidebar-text">Administração</h6>
        <a class="list-group-item list-group-item-action list-group-item-dark p-3" href="{{ route('admin.dashboard') }}">
            <i class="bi bi-gear-wide-connected fa-fw me-3"></i><span class="sidebar-text">Dashboard Admin</span>
        </a>
        <a class="list-group-item list-group-item-action list-group-item-dark p-3" href="{{ route('admin.units.index') }}">
            <i class="bi bi-building fa-fw me-3"></i><span class="sidebar-text">Unidades</span>
        </a>
        <a class="list-group-item list-group-item-action list-group-item-dark p-3" href="{{ route('admin.positions.index') }}" title="Cargos">
            <i class="bi bi-briefcase fa-fw me-3"></i><span class="sidebar-text">Cargos</span>
        </a>
        <a class="list-group-item list-group-item-action list-group-item-dark p-3" href="{{ route('admin.scholarship_holders.index') }}" title="Bolsistas">
            <i class="bi bi-people fa-fw me-3"></i><span class="sidebar-text">Bolsistas</span>
        </a>
        <a class="list-group-item list-group-item-action list-group-item-dark p-3" href="{{ route('admin.projects.index') }}" title="Projetos">
            <i class="bi bi-kanban fa-fw me-3"></i><span class="sidebar-text">Projetos</span>
        </a>
        <a class="list-group-item list-group-item-action list-group-item-dark p-3" href="{{ route('admin.courses.index') }}" title="Cursos">
            <i class="bi bi-mortarboard fa-fw me-3"></i><span class="sidebar-text">Cursos</span>
        </a>
        <a class="list-group-item list-group-item-action list-group-item-dark p-3" href="{{ route('admin.homologations.index') }}" title="Homologação">
            <i class="bi bi-check2-square fa-fw me-3"></i><span class="sidebar-text">Homologação</span>
        </a>
        <a class="list-group-item list-group-item-action list-group-item-dark p-3" href="{{ route('admin.users.index') }}" title="Utilizadores">
            <i class="bi bi-person-gear fa-fw me-3"></i><span class="sidebar-text">Usuários</span>
        </a>
        <a class="list-group-item list-group-item-action list-group-item-dark p-3" href="{{ route('admin.roles.index') }}" title="Funções">
            <i class="bi bi-key fa-fw me-3"></i><span class="sidebar-text">Funções</span>
        </a>
        <a class="list-group-item list-group-item-action list-group-item-dark p-3" href="{{ route('admin.permissions.index') }}" title="Permissões">
            <i class="bi bi-shield-check fa-fw me-3"></i><span class="sidebar-text">Permissões</span>
        </a>
        @endhasanyrole
    </div>

    <hr class="bg-secondary my-2">

    {{-- Dropdown Relatórios --}}
    <a class="list-group-item list-group-item-action list-group-item-dark p-3 d-flex justify-content-between align-items-center"
       data-bs-toggle="collapse" href="#submenuReports" role="button" aria-expanded="false" aria-controls="submenuReports">
        <span class="d-none d-lg-inline">
            <i class="bi bi-file-earmark-bar-graph fa-fw me-3 text-warning"></i>
            <span class="sidebar-text">Relatórios</span>
        </span>
        <span class="d-lg-none" data-bs-toggle="tooltip" data-bs-placement="right" title="Relatórios">
            <i class="bi bi-file-earmark-bar-graph fa-fw text-warning"></i>
        </span>
        <i class="bi bi-caret-down-fill"></i>
    </a>

    <div class="collapse" id="submenuReports">
        <ul class="list-group list-group-flush">
            @role('bolsista')
            <li>
                <a class="list-group-item list-group-item-action ps-5"
                   href="{{ route('reports.myReport') }}"
                   data-bs-toggle="tooltip" data-bs-placement="right" title="Relatório Individual">
                    <i class="bi bi-person-lines-fill me-2 text-success"></i>
                    <span class="d-none d-lg-inline">Relatório Individual</span>
                </a>
            </li>
            @endrole

            @role('coordenador_adjunto|coordenador_geral')
            <li>
                <a class="list-group-item list-group-item-action ps-5"
                   href="{{ route('admin.homologations.report') }}"
                   data-bs-toggle="tooltip" data-bs-placement="right" title="Relatório da Unidade">
                    <i class="bi bi-building me-2 text-primary"></i>
                    <span class="d-none d-lg-inline">Relatório da Unidade</span>
                </a>
            </li>
            @endrole

            @role('coordenador_geral')
            <li>
                <a class="list-group-item list-group-item-action ps-5"
                   href="{{ route('admin.reports.monthly') }}"
                   data-bs-toggle="tooltip" data-bs-placement="right" title="Relatório Consolidado">
                    <i class="bi bi-collection me-2 text-info"></i>
                    <span class="d-none d-lg-inline">Relatório Consolidado</span>
                </a>
            </li>
            @endrole
        </ul>
    </div>
</div>
