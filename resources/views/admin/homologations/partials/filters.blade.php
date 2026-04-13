@php
    $user = auth()->user();

    $isAdmin = $user->hasAnyRole([
        'admin',
        'coordenador_geral',
        'coordenador_adjunto_geral'
    ]);

    $isCoordinator = $user->hasRole('coordenador_adjunto');
@endphp

<form method="GET" action="{{ route('admin.homologations.index') }}" id="filters-form" class="row g-3 mb-4">

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

    {{-- CARGO --}}
    @if($isAdmin || $isCoordinator)
        <div class="col-md-2">
            <select id="filter-role" name="role" class="form-select">
                <option value="">Cargo</option>
                <option value="monitor" @selected(request('role') == 'monitor')>Monitor</option>
                <option value="pesquisador" @selected(request('role') == 'pesquisador')>Pesquisador</option>
            </select>
        </div>
    @endif

    {{-- BOLSISTA --}}
    @if($isAdmin || $isCoordinator)
        <div class="col-md-3">
            <select id="filter-scholarship-holder" name="scholarship_holder_id" class="form-select">
                <option value="">Bolsista</option>
                @foreach($scholarship_holders as $holder)
                    <option value="{{ $holder->id }}" @selected(request('scholarship_holder_id') == $holder->id)>
                        {{ $holder->user->name }}
                    </option>
                @endforeach
            </select>
        </div>
    @endif

    {{-- MES --}}
    <div class="col-md-2">
        <input
            type="month"
            id="filter-month"
            name="month"
            class="form-control"
            value="{{ request('month', now()->format('Y-m')) }}"
        >
    </div>

    {{-- STATUS --}}
    <div class="col-md-2">
        <select id="filter-status" name="status" class="form-select">
            <option value="all" @selected(request('status') === 'all')>Todos status</option>
            <option value="submitted" @selected(request('status') == 'submitted')>Enviado</option>
            <option value="approved" @selected(request('status') == 'approved')>Homologado</option>
            <option value="rejected" @selected(request('status') == 'rejected')>Rejeitado</option>
        </select>
    </div>

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
