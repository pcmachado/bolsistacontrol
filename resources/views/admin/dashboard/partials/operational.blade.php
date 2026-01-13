<div class="container-fluid">

    <h5 class="mb-4 text-muted">
        <i class="bi bi-mortarboard"></i> Gestão Operacional
    </h5>

    {{-- ========================= --}}
    {{-- STATUS DE FREQUÊNCIAS --}}
    {{-- ========================= --}}
    <div class="row g-3 mb-4">
        
        <div class="col-md-3">
            <a href="{{ route('attendance.card.submitted') }}" class="text-decoration-none">
                <div class="card border-start border-4 border-info shadow-sm h-100">
                    <div class="card-body">
                        <small class="text-muted">Pendentes</small>
                        <h3 id="card-submitted" class="text-info">{{ $counts['submitted'] ?? 0 }}</h3>
                        <span class="small text-muted">Aguardando homologação</span>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-md-3">
            <a href="{{ route('attendance.card.approved') }}" class="text-decoration-none">
                <div class="card border-start border-4 border-success shadow-sm h-100">
                    <div class="card-body">
                        <small class="text-muted">Homologadas</small>
                        <h3 id="card-approved" class="text-success">{{ $counts['approved'] ?? 0 }}</h3>
                        <span class="small text-muted">No período</span>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-md-3">
            <a href="{{ route('attendance.card.rejected') }}" class="text-decoration-none">
                <div class="card border-start border-4 border-danger shadow-sm h-100">
                    <div class="card-body">
                        <small class="text-muted">Rejeitadas</small>
                        <h3 id="card-rejected" class="text-danger">{{ $counts['rejected'] ?? 0 }}</h3>
                        <span class="small text-muted">Com ajustes</span>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-md-3">
            <a href="{{ route('attendance.card.late') }}" class="text-decoration-none">
                <div class="card border-start border-4 border-warning shadow-sm h-100">
                    <div class="card-body">
                        <small class="text-muted">Em atraso</small>
                        <h3 id="card-late" class="text-warning">{{ $counts['late'] ?? 0 }}</h3>
                        <span class="small text-muted">Fora do prazo</span>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <section class="row g-4 mb-4">
                    
        {{-- Gráfico --}}
        <div class="col-lg-8">
            <div class="card shadow-sm rounded-0 border-0 h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="card-title mb-0 text-muted">
                            Distribuição dos Registros de Frequência
                            @if($unitName)
                                — <span class="fw-normal">{{ $unitName }}</span>
                            @endif
                        </h5>
                        <div>
                            <button id="toggleChart" type="button" class="btn btn-sm btn-outline-secondary rounded-0">
                                Alternar Gráfico
                            </button>
                        </div>
                    </div>
                    <canvas id="attendanceChart" style="max-height: 220px;"></canvas>
                </div>
            </div>
        </div>

        {{-- Indicadores --}}
        <div class="col-lg-4">
            <div class="card shadow-sm rounded-0 border-0 h-100">
                <div class="card-body p-4">
                    <h2 class="h6 card-title fw-semibold mb-4 text-muted">Frequências no mês</h2>
                    {{-- Homologados --}}
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1 small"
                        id="approved-percent">
                            <span class="text-success">Homologados</span>
                            <span class="text-success">{{ $percentages['approved'] }}%</span>
                        </div>
                        <div class="progress rounded-0" style="height: 6px;">
                            <div class="progress-bar bg-success-subtle text-success"
                                id="approved-bar"
                                role="progressbar"
                                style="width: {{ $percentages['approved'] }}%;"></div>
                        </div>
                    </div>

                    {{-- Submetidos --}}
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1 small"
                        id="submitted-percent">
                            <span class="text-info">Submetidos</span>
                            <span class="text-info">{{ $percentages['submitted'] }}%</span>
                        </div>
                        <div class="progress rounded-0" style="height: 6px;">
                            <div class="progress-bar bg-info-subtle text-info"
                                id="submitted-bar"
                                role="progressbar"
                                style="width: {{ $percentages['submitted'] }}%;"></div>
                        </div>
                    </div>

                    {{-- Rejeitados --}}
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1 small"
                        id="rejected-percent">
                            <span class="text-danger">Rejeitados</span>
                            <span class="text-danger">{{ $percentages['rejected'] }}%</span>
                        </div>
                        <div class="progress rounded-0" style="height: 6px;">
                            <div class="progress-bar bg-danger-subtle text-danger"
                                id="rejected-bar"
                                role="progressbar"
                                style="width: {{ $percentages['rejected'] }}%;"></div>
                        </div>
                    </div>

                    {{-- Rascunhos --}}
                    <div>
                        <div class="d-flex justify-content-between mb-1 small"
                        id="draft-percent">
                            <span class="text-secondary">Rascunhos</span>
                            <span class="text-secondary">{{ $percentages['draft'] }}%</span>
                        </div>
                        <div class="progress rounded-0" style="height: 6px;">
                            <div class="progress-bar bg-secondary-subtle text-secondary"
                                id="draft-bar"
                                role="progressbar"
                                style="width: {{ $percentages['draft'] }}%;"></div>
                        </div>
                    </div>
                    {{-- Atrasados --}}
                    <div class="mt-3">
                        <div class="d-flex justify-content-between mb-1 small"
                        id="late-percent">
                            <span class="text-warning">Atrasados</span>
                            <span class="text-warning">{{ $percentages['late'] }}%</span>
                        </div>
                        <div class="progress rounded-0" style="height: 6px;">
                            <div class="progress-bar bg-warning-subtle text-warning"
                                id="late-bar"
                                role="progressbar"
                                style="width: {{ $percentages['late'] }}%;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ========================= --}}
    {{-- AÇÕES RÁPIDAS --}}
    {{-- ========================= --}}
    <div class="card shadow-sm">
        <div class="card-body">
            <h6 class="text-muted mb-3">Ações rápidas</h6>

            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('admin.projects.index') }}" class="btn btn-outline-primary btn-sm">
                    <i class="bi bi-kanban"></i> Projetos
                </a>

                <a href="{{ route('admin.courses.index') }}" class="btn btn-outline-success btn-sm">
                    <i class="bi bi-mortarboard"></i> Cursos
                </a>

                <a href="{{ route('admin.class-offerings.index') }}" class="btn btn-outline-info btn-sm">
                    <i class="bi bi-collection"></i> Turmas
                </a>

                <a href="{{ route('admin.disciplines.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-journal-text"></i> Disciplinas
                </a>

                <a href="{{ route('admin.homologations.index') }}" class="btn btn-outline-success btn-sm">
                    <i class="bi bi-check2-square"></i> Homologações
                </a>
            </div>
        </div>
    </div>

</div>

