@extends('layouts.app')

<style>
.border-orange {
    border-color: orange !important;
}
.border-purple {
    border-color: purple !important;
}
.border-teal {
    border-color: teal !important;
}
.border-pink {
    border-color: pink !important;
}
.sidebar-text {
    font-size: 0.95rem; /* Ajuste o tamanho conforme necessário */
}
.user-profile img {
    width: 40px;
    height: 40px;
}
.user-profile {
    margin-top: 10px;
}
</style>

@section('title', 'Visão Geral - Admin')

@section('content')
{{-- 🔔 Faixa institucional abaixo da navbar --}}
        @if($unitName)
            <div class="bg-info text-white py-2 shadow-sm" style="margin-top: -1.5rem; margin-left: -1.5rem; margin-right: -1.5rem; margin-bottom: 1.5rem;">
                <div class="container d-flex align-items-center">
                    <i class="bi bi-building me-2"></i>
                    <span>
                        Você está visualizando os dados da <strong>{{ $unitName }}</strong>
                    </span>
                </div>
            </div>
        @endif
    <div class="container-fluid">
        <h1 class="mb-4">Dashboard Administrativo</h1>
        <div class="d-flex justify-content-end align-items-center mb-4 gap-3">
            <select id="filterType" class="form-select form-select-sm w-auto rounded-0 border-secondary-subtle">
                <option value="month">Por mês</option>
                <option value="range">Por período</option>
            </select>

            {{-- input mês --}}
            <input type="month" id="monthInput" name="month" value="{{ $currentMonth }}" class="form-control">

            {{-- input intervalo --}}
            <div id="rangeInputs" class="d-flex gap-2" style="display:none;">
                <input type="date" id="startDate" class="form-control form-control-sm rounded-0 border-secondary-subtle">
                <input type="date" id="endDate" class="form-control form-control-sm rounded-0 border-secondary-subtle">
            </div>
        </div>
        <!-- Widgets de Estatísticas (Sistema de Grid do Bootstrap) -->
        <section class="row g-4 mb-4">
            <div class="col-12 col-md-6 col-lg-3">
                <div class="card shadow-lg border-start border-5 border-primary rounded-3 h-100">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <p class="text-secondary fw-semibold mb-0">Usuários</p>
                        </div>
                        <h2 class="card-title h3 fw-bolder text-dark">{{ $usersCount ?? 0 }}</h2>
                        <p class="card-text small text-muted mt-2">cadastrados</p>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6 col-lg-3">
                <div class="card shadow-lg border-start border-5 border-danger rounded-3 h-100">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <p class="text-secondary fw-semibold mb-0">Bolsistas</p>
                        </div>
                        <h2 class="card-title h3 fw-bolder text-dark">{{ $scholarshipHoldersCount ?? 0 }}</h2>
                        <p class="card-text small text-muted mt-2">ativos</p>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-6 col-lg-3">
                <div class="card shadow-lg border-start border-5 border-warning rounded-3 h-100">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <p class="text-secondary fw-semibold mb-0">Cursos</p>
                            <span class="badge text-bg-success fw-bold"></span>
                        </div>
                        <h2 class="card-title h3 fw-bolder text-dark">{{ $coursesCount ?? 0 }}</h2>
                        <p class="card-text small text-muted mt-2">cadastrados</p>
                    </div>
                </div>
            </div>

            <!--<div class="col-12 col-md-6 col-lg-3">
                <div class="card shadow-lg border-start border-5 border-secondary rounded-3 h-100">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <p class="text-secondary fw-semibold mb-0">Taxa de Conversão</p>
                            <span class="badge text-bg-success fw-bold">+2.1%</span>
                        </div>
                        <h2 class="card-title h3 fw-bolder text-dark">4.5%</h2>
                        <p class="card-text small text-muted mt-2">Visitantes para vendas</p>
                    </div>
                </div>
            </div>-->
        </section>

        <div class="row g-3">
            {{-- Homologadas --}}
            <div class="col-md-3">
                <a href="{{ route('attendance.card.approved') }}" class="text-decoration-none">
                    <div class="card text-white bg-success shadow-sm">
                        <div class="card-body text-center">
                            <i class="bi bi-check-circle display-5"></i>
                            <h5 class="card-title mt-2">Homologadas</h5>
                            <h2>{{ $counts['approved'] ?? 0 }}</h2>
                            <small class="text-muted">Registros aprovados</small>
                        </div>
                    </div>
                </a>
            </div>

            {{-- Pendentes --}}
            <div class="col-md-3">
                <a href="{{ route('attendance.card.submitted') }}" class="text-decoration-none">
                    <div class="card text-white bg-info shadow-sm">
                        <div class="card-body text-center">
                            <i class="bi bi-hourglass-split display-5"></i>
                            <h5 class="card-title mt-2">Pendentes</h5>
                            <h2>{{ $counts['submitted'] ?? 0 }}</h2>
                            <small class="text-muted">Registros pendentes de homologação</small>
                        </div>
                    </div>
                </a>
            </div>

            {{-- Em atraso --}}
            <div class="col-md-3">
                <a href="{{ route('attendance.card.late') }}" class="text-decoration-none">
                    <div class="card text-dark bg-warning shadow-sm">
                        <div class="card-body text-center">
                            <i class="bi bi-exclamation-triangle display-5"></i>
                            <h5 class="card-title mt-2">Em Atraso</h5>
                            <h2>{{ $counts['late'] ?? 0 }}</h2>
                            <small class="text-muted">Registros não enviados no prazo</small>
                        </div>
                    </div>
                </a>
            </div>

            {{-- Rejeitadas --}}
            <div class="col-md-3">
                <a href="{{ route('attendance.card.rejected') }}" class="text-decoration-none">
                    <div class="card text-white bg-danger shadow-sm">
                        <div class="card-body text-center">
                            <i class="bi bi-x-circle display-5"></i>
                            <h5 class="card-title mt-2">Rejeitadas</h5>
                            <h2>{{ $counts['rejected'] ?? 0 }}</h2>
                            <small class="text-muted">Registros rejeitados</small>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        <hr class="my-4">

        <!-- Relatórios e Gráficos (Grid 2/3 e 1/3 no Desktop) -->
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

        <!-- Tabela de Últimas Transações (Componente de UI Pré-construído) -->
       <section class="card shadow-lg rounded-3">
            <div class="card-body p-4">
                <h2 class="h5 card-title fw-semibold mb-4">Últimos Registros</h2>
                <div class="row">
                    <!-- Coluna 1: Últimos envios -->
                    <div class="col-md-6 mb-4 mb-md-0">
                        <h6 class="fw-semibold mb-3">Últimos Envios de Frequência</h6>
                        <div class="table-responsive">
                            <table class="table table-sm table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Bolsista</th>
                                        <th>Data</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($lastSubmissions as $submission)
                                        <tr>
                                            <td>{{ $submission->scholarshipHolder->user->name }}</td>
                                            <td>{{ \Carbon\Carbon::parse($submission->date)->format('d/m/Y') }}</td>
                                            <td>
                                                <span class="badge text-bg-info rounded-pill px-3 py-1">
                                                    Enviado
                                                </span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="3" class="text-muted">Nenhum envio recente</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="text-end mt-2">
                            <a href="{{ route('attendance.submissions') }}" class="btn btn-sm btn-outline-primary rounded-0">
                                Ver todos
                            </a>
                        </div>
                    </div>

                    <!-- Coluna 2: Últimas homologações -->
                    <div class="col-md-6">
                        <h6 class="fw-semibold mb-3">Últimas Homologações</h6>
                        <div class="table-responsive">
                            <table class="table table-sm table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Bolsista</th>
                                        <th>Data</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($lastApprovals as $approval)
                                        <tr>
                                            <td>{{ $approval->scholarshipHolder->user->name }}</td>
                                            <td>{{ \Carbon\Carbon::parse($approval->date)->format('d/m/Y') }}</td>
                                            <td>
                                                <span class="badge text-bg-success rounded-pill px-3 py-1">
                                                    Homologado
                                                </span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="3" class="text-muted">Nenhuma homologação recente</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="text-end mt-2">
                            <a href="{{ route('attendance.approvals') }}" class="btn btn-sm btn-outline-success rounded-0">
                                Ver todos
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    let chart;
    let currentType = 'pie'; // começa como pizza

    function renderChart(data) {
        const ctx = document.getElementById('attendanceChart').getContext('2d');

        if (chart) chart.destroy();

        chart = new Chart(ctx, {
            type: currentType,
            data: {
                labels: ['Homologadas', 'Submetidas', 'Rejeitadas', 'Rascunhos', 'Atrasadas'],
                datasets: [{
                    data: [
                        data.counts.approved ?? 0,
                        data.counts.submitted ?? 0,
                        data.counts.rejected ?? 0,
                        data.counts.draft ?? 0,
                        data.counts.late ?? 0
                    ],
                    backgroundColor: [
                        'rgba(25, 135, 84, 0.7)',
                        'rgba(13, 202, 240, 0.7)',
                        'rgba(220, 53, 69, 0.7)',
                        'rgba(108, 117, 125, 0.7)',
                        'rgba(255, 193, 7, 0.7)'
                    ],
                    borderWidth: 0,
                    //barThickness: currentType === 'bar' ? 20 : undefined
                }]
            },
            options: {
                indexAxis: currentType === 'bar' ? 'y' : 'x',
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { 
                        display: currentType === 'pie',
                        position: 'bottom',
                        labels: { font: { size: 12 }, color: '#6c757d' }
                    }
                },
                scales: currentType === 'bar' ? {
                    x: { ticks: { color: '#6c757d' }, grid: { display: false } },
                    y: { ticks: { color: '#6c757d' }, grid: { display: false } }
                } : {}
            }
        });
    }

    // 🔹 Função para carregar dados via AJAX
    function loadStats(params) {
        fetch(`{{ route('admin.dashboard.stats') }}?${params}`)
            .then(res => res.json())
            .then(data => {
                // Atualiza indicadores
                document.getElementById('approved-percent').innerText = data.percentages.approved + '%';
                document.getElementById('approved-bar').style.width = data.percentages.approved + '%';

                document.getElementById('submitted-percent').innerText = data.percentages.submitted + '%';
                document.getElementById('submitted-bar').style.width = data.percentages.submitted + '%';

                document.getElementById('rejected-percent').innerText = data.percentages.rejected + '%';
                document.getElementById('rejected-bar').style.width = data.percentages.rejected + '%';

                document.getElementById('draft-percent').innerText = data.percentages.draft + '%';
                document.getElementById('draft-bar').style.width = data.percentages.draft + '%';

                document.getElementById('late-percent').innerText = data.percentages.late + '%';
                document.getElementById('late-bar').style.width = data.percentages.late + '%';

                // Renderiza gráfico
                renderChart(data);
            });
    }

    // 🔹 Elementos do filtro
    const filterType = document.getElementById('filterType');
    const monthInput = document.getElementById('monthInput');
    const rangeInputs = document.getElementById('rangeInputs');
    const startDate = document.getElementById('startDate');
    const endDate = document.getElementById('endDate');
    const toggleChartBtn = document.getElementById('toggleChart');

    // Alternar tipo de gráfico
    if (toggleChartBtn) {
        toggleChartBtn.addEventListener('click', () => {
            currentType = currentType === 'pie' ? 'bar' : 'pie';
            if (monthInput && monthInput.value) {
                loadStats(`month=${monthInput.value}`);
            } else if (startDate && endDate && startDate.value && endDate.value) {
                loadStats(`start_date=${startDate.value}&end_date=${endDate.value}`);
            }
        });
    }

    // Alterna entre mês e intervalo
    if (filterType) {
        filterType.addEventListener('change', () => {
            if (filterType.value === 'month') {
                if (monthInput) monthInput.style.display = 'block';
                if (rangeInputs) rangeInputs.style.display = 'none';
            } else {
                if (monthInput) monthInput.style.display = 'none';
                if (rangeInputs) rangeInputs.style.display = 'flex';
            }
        });
    }

    // Eventos de mudança
    if (monthInput) {
        monthInput.addEventListener('change', () => {
            if (monthInput.value) {
                loadStats(`month=${monthInput.value}`);
            }
        });
    }

    if (startDate && endDate) {
        [startDate, endDate].forEach(el => {
            el.addEventListener('change', () => {
                if (startDate.value && endDate.value) {
                    loadStats(`start_date=${startDate.value}&end_date=${endDate.value}`);
                }
            });
        });
    }

    // 🔹 Inicializa com o valor atual
    if (monthInput && monthInput.value) {
        loadStats(`month=${monthInput.value}`);
    }
});
</script>
@endpush
