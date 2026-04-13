@php
    $user = auth()->user();

    $isAdmin = $user->hasAnyRole([
        'admin',
        'coordenador_geral',
        'coordenador_adjunto_geral'
    ]);

    $isCoordinator = $user->hasRole('coordenador_adjunto');
@endphp

<form method="GET" action="{{ route('admin.courses.index') }}" id="filters-form" class="row g-3 mb-4">

    {{-- PROJETO --}}
    @if($isAdmin)
        <div class="col-md-3">
            <select id="filter-project" name="project_id" class="form-select">
                <option value="">Projeto</option>
                @foreach($projects as $project)
                    <option value="{{ $project->id }}" @selected(request('project_id') == $project->id)>
                        {{ $project->name }}
                    </option>
                @endforeach
            </select>
        </div>
    @endif

    {{-- UNIDADE --}}
    @if($isAdmin)
        <div class="col-md-3">
            <select id="filter-unit" name="unit_id" class="form-select">
                <option value="">Unidade</option>
                @foreach($units as $unit)
                    <option value="{{ $unit->id }}" @selected(request('unit_id') == $unit->id)>
                        {{ $unit->name }}
                    </option>
                @endforeach
            </select>
        </div>
    @endif

    <div class="col-md-2">
        <button type="submit" id="filter-button" class="btn btn-primary w-100">
            Filtrar
        </button>
    </div>

    <div class="col-md-2">
        <a href="{{ route('admin.homologations.index') }}" class="btn btn-outline-secondary w-100">
            Limpar
        </a>
    </div>

</form>
