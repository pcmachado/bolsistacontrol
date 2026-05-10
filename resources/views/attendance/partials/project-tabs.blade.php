@php
    $projects = $projects ?? collect();
    $activeProjectId = $activeProjectId ?? $activeProject?->id;
@endphp

<div class="card shadow-sm border-0 mb-4">
    <div class="card-body">
        @if($projects->isEmpty())
            <div class="alert alert-warning mb-0">
                Nenhum projeto vinculado ao bolsista.
            </div>
        @else
            <div class="d-flex flex-wrap gap-2 mb-3">
                @foreach($projects as $project)
                    <a
                        href="{{ request()->fullUrlWithQuery(['project_id' => $project->id]) }}"
                        class="btn {{ (string) $activeProjectId === (string) $project->id ? 'btn-primary' : 'btn-outline-primary' }}"
                    >
                        {{ $project->name }}
                    </a>
                @endforeach
            </div>

            <div class="row g-3">
                <div class="col-md-6">
                    <div class="small text-muted">Projeto Atual</div>
                    <strong>{{ $activeProject?->name ?? 'Selecione um projeto' }}</strong>
                </div>

                <div class="col-md-3">
                    <div class="small text-muted">InstituiÃ§Ã£o</div>
                    <strong>{{ $activeProject?->institution?->name ?? '-' }}</strong>
                </div>

                <div class="col-md-3">
                    <div class="small text-muted">SituaÃ§Ã£o</div>
                    <span class="badge {{ $activeProject ? 'bg-success' : 'bg-secondary' }}">
                        {{ $activeProject ? 'Ativo' : 'Sem contexto' }}
                    </span>
                </div>
            </div>
        @endif
    </div>
</div>
