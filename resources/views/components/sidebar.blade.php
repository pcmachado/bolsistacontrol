 <!-- Sidebar (Offcanvas para Mobile) -->
<nav id="sidebar" class="bg-dark text-white">
    
    <!-- Header da Sidebar -->
    <div class="p-4 border-bottom border-secondary-subtle">
        <h4 class="mb-0">
            <i class="bi bi-gear-fill text-light me-2"></i>
            <span class="text-light">Painel Administrativo</span>
            <span class="text-light">{{ auth()->user()->name }}</span>
        </h4>
          <!-- Botão de Fechar no Mobile (do Offcanvas) -->
        <button type="button" class="btn-close btn-close-white d-lg-none" data-bs-dismiss="offcanvas" data-bs-target="#sidebar" aria-label="Close"></button>
    </div>

    <!-- Links de Navegação (Menu) -->
    <div class="flex-grow-1 p-3">
        <ul class="nav nav-pills flex-column mb-auto">
            <li class="nav-item mb-2">
                <a href="{{ route('admin.dashboard') }}" class="nav-link active rounded-3 bg-primary" aria-current="page">
                    <i class="bi bi-house-door-fill me-2"></i>Dashboard
                </a>
            </li>
            <li class="mb-2">
                <a href="{{ route('admin.reports.index') }}" class="nav-link text-white rounded-3 hover-bg-secondary-subtle">
                    <i class="bi bi-bar-chart-line-fill me-2"></i>Relatórios
                </a>
            </li>
            <li class="mb-2">
                <a href="{{ route('admin.users.index') }}" class="nav-link text-white rounded-3 hover-bg-secondary-subtle">
                    <i class="bi bi-people-fill me-2"></i>Usuários
                </a>
            </li>
            <li class="mb-2">
                <a href="{{ route('admin.scholarship_holders.index') }}" class="nav-link text-white rounded-3 hover-bg-secondary-subtle">
                    <i class="bi bi-mortarboard-fill me-2"></i>Bolsistas
                </a>
            </li>
            <li class="mb-2">
                <a href="{{ route('admin.projects.index') }}" class="nav-link text-white rounded-3 hover-bg-secondary-subtle">
                    <i class="bi bi-folder-fill me-2"></i>Projetos
                </a>
            </li>
            <li class="mb-2">
                <a href="{{ route('attendance.index') }}" class="nav-link text-white rounded-3 hover-bg-secondary-subtle">
                    <i class="bi bi-calendar-check-fill me-2"></i>Frequência
                </a>
            </li>
            <li class="mb-2">
                <a href="#" class="nav-link text-white rounded-3 hover-bg-secondary-subtle">
                    <i class="bi bi-gear-fill me-2"></i>Configurações
                </a>
            </li>
        </ul>
    </div>

    <!-- Indicador de Personalização -->
    <div class="p-4 border-top border-secondary-subtle">
        <p class="text-secondary small mb-0">
            Tema: <span class="fw-bold text-info">Personalizado com Sass</span>
        </p>
    </div>
</nav>
