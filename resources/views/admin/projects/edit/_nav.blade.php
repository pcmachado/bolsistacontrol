<div class="list-group rounded-0">
    <a href="{{ route('admin.projects.edit.general', $project) }}"
       class="list-group-item list-group-item-action {{ $active === 'general' ? 'active' : '' }}">
        Dados Gerais
    </a>
    <a href="{{ route('admin.projects.edit.positions', $project) }}"
       class="list-group-item list-group-item-action {{ $active === 'positions' ? 'active' : '' }}">
        Cargos
    </a>
    <a href="{{ route('admin.projects.edit.scholars', $project) }}"
       class="list-group-item list-group-item-action {{ $active === 'scholars' ? 'active' : '' }}">
        Bolsistas
    </a>
    <a href="{{ route('admin.projects.edit.courses', $project) }}"
       class="list-group-item list-group-item-action {{ $active === 'courses' ? 'active' : '' }}">
        Cursos
    </a>
    <a href="{{ route('admin.projects.edit.funding', $project) }}"
       class="list-group-item list-group-item-action {{ $active === 'funding' ? 'active' : '' }}">
        Fontes de Fomento
    </a>
</div>
