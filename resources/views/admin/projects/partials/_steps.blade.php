<ul class="nav justify-content-center mb-4 wizard-steps">
    <li class="nav-item">
        <a class="nav-link {{ $step == 1 ? 'active' : ($step > 1 ? 'completed' : '') }}"
           href="{{ $step > 1 ? route('admin.projects.create.step1') : '#' }}">
            <i class="bi bi-1-circle{{ $step >= 1 ? '-fill' : '' }} me-1"></i>
            Projeto
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ $step == 2 ? 'active' : ($step > 2 ? 'completed' : '') }}"
           href="{{ $step > 2 ? route('admin.projects.create.step2', $project ?? 0) : '#' }}">
            <i class="bi bi-2-circle{{ $step >= 2 ? '-fill' : '' }} me-1"></i>
            Cargos
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ $step == 3 ? 'active' : ($step > 3 ? 'completed' : '') }}"
           href="{{ $step > 3 ? route('admin.projects.create.step3', $project ?? 0) : '#' }}">
            <i class="bi bi-3-circle{{ $step >= 3 ? '-fill' : '' }} me-1"></i>
            Bolsistas
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ $step >= 4 ? 'completed' : '' }} {{ $step == 4 ? 'active' : '' }}"
           href="{{ route('admin.projects.create.step4', $project) }}">4. Cursos</a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ $step >= 5 ? 'completed' : '' }} {{ $step == 5 ? 'active' : '' }}"
           href="{{ route('admin.projects.create.step5', $project) }}">5. Fontes de Fomento</a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ $step == 6 ? 'active' : '' }}"
           href="{{ route('admin.projects.review', $project) }}">6. Revisão Final</a>
    </li>
</ul>
<style>
    .wizard-steps .nav-link {
        border: 1px solid #dee2e6;
        border-radius: 50px;
        padding: 10px 20px;
        margin: 0 5px;
        transition: background-color 0.3s, color 0.3s;
    }
    .wizard-steps .nav-link.active {
        background-color: #0d6efd;
        color: #fff;
    }
    .wizard-steps .nav-link.completed {
        background-color: #198754; /* verde */
        color: #fff;
    }
    .wizard-steps .nav-link:hover {
        text-decoration: none;
        background-color: #0b5ed7;
        color: #fff;
    }
</style>