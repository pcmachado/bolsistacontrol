@php
    $isSelf = request()->routeIs('my-attendance.submissions.*');
    $action = $isSelf ? route('my-attendance.submissions.my') : route('attendance.submissions.index');
@endphp

<form method="GET" action="{{ $action }}" class="card filter-card">
    <div class="card-body">
        <div class="row g-3 align-items-end">
            @if(isset($projects) && $projects->isNotEmpty())
                <div class="col-md-3">
                    <label for="filter-project" class="form-label">Projeto</label>
                    <select id="filter-project" name="project_id" class="form-select">
                        @if(! $isSelf)
                            <option value="">Todos</option>
                        @endif
                        @foreach($projects as $project)
                            <option value="{{ $project->id }}" @selected(request('project_id', $activeProjectId ?? null) == $project->id)>
                                {{ $project->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif

            <div class="col-md-3">
                <label for="filter-month" class="form-label">Mês</label>
                <input
                    id="filter-month"
                    type="month"
                    name="month"
                    value="{{ request('month') }}"
                    class="form-control"
                >
            </div>

            <div class="col-md-3">
                <label for="filter-status" class="form-label">Status</label>
                <select id="filter-status" name="status" class="form-select">
                    <option value="">Todos</option>
                    <option value="submitted" @selected(request('status') === 'submitted')>Enviadas</option>
                    <option value="approved" @selected(request('status') === 'approved')>Homologadas</option>
                    <option value="rejected" @selected(request('status') === 'rejected')>Rejeitadas</option>
                    <option value="late" @selected(request('status') === 'late')>Atrasadas</option>
                    <option value="draft" @selected(request('status') === 'draft')>Rascunhos</option>
                </select>
            </div>

            @if(! $isSelf && isset($units) && $units->isNotEmpty())
                <div class="col-md-3">
                    <label class="form-label">Unidade</label>
                    <select name="unit_id" class="form-select">
                        <option value="">Todas</option>
                        @foreach($units as $unit)
                            <option value="{{ $unit->id }}" @selected(request('unit_id') == $unit->id)>
                                {{ $unit->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif

            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary flex-grow-1">
                    <i class="bi bi-funnel me-1"></i> Filtrar
                </button>

                <a href="{{ $action }}" class="btn btn-outline-secondary">
                    Limpar
                </a>
            </div>
        </div>
    </div>
</form>
