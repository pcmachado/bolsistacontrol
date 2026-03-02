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

        <ul class="nav nav-tabs mb-4" role="tablist">
            <li class="nav-item">
                <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-geral">
                    Visão Geral
                </button>
            </li>

            @hasanyrole('admin|coordenador_geral|coordenador_adjunto_geral')
            <li class="nav-item">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-operacional">
                    Operacional
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-financeiro">
                    Financeiro
                </button>
            </li>
            @endhasanyrole
        </ul>

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

        <div class="tab-content">
            {{-- VISÃO GERAL --}}
            <div class="tab-pane fade show active" id="tab-geral">
                <!-- Widgets de Estatísticas (Sistema de Grid do Bootstrap) -->
                <section class="row g-4 mb-4">

                    {{-- ========================= --}}
                    {{-- INDICADORES OPERACIONAIS --}}
                    {{-- ========================= --}}
                    <div class="row g-3 mb-4">
                        <div class="col-md-3">
                            <div class="card shadow-sm">
                                <div class="card-body text-center">
                                    <i class="bi bi-kanban fs-2 text-primary"></i>
                                    <h5 class="mt-2">{{ $academic['projects_active'] ?? '-' }}</h5>
                                    <small class="text-muted">Projetos</small>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="card shadow-sm">
                                <div class="card-body text-center">
                                    <i class="bi bi-mortarboard fs-2 text-success"></i>
                                    <h5 class="mt-2">{{ $coursesCount ?? 0 }}</h5>
                                    <small class="text-muted">Cursos</small>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="card shadow-sm">
                                <div class="card-body text-center">
                                    <i class="bi bi-collection fs-2 text-info"></i>
                                    <h5 class="mt-2">{{ $academic['class_offerings_active'] ?? '-' }}</h5>
                                    <small class="text-muted">Turmas</small>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="card shadow-sm">
                                <div class="card-body text-center">
                                    <i class="bi bi-people fs-2 text-warning"></i>
                                    <h5 class="mt-2">{{ $scholarshipHoldersCount ?? 0 }}</h5>
                                    <small class="text-muted">Bolsistas</small>
                                </div>
                            </div>
                        </div>

                    </div>
                </section>

                <!-- Tabela de Últimas Transações (Componente de UI Pré-construído) -->
                <section class="card shadow-lg rounded-3">
                    <div class="card-body p-4">
                        <h2 class="h5 card-title fw-semibold mb-4">Últimas Submissões de Frequência</h2>
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
                                                    <td>{{ str_pad($submission->month, 2, '0', STR_PAD_LEFT) }}/{{ $submission->year }}</td>
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
                                    <a href="{{ route('attendance.submissions.index') }}" class="btn btn-sm btn-outline-primary rounded-0">
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
                                                    <td>{{ str_pad($approval->month, 2, '0', STR_PAD_LEFT) }}/{{ $approval->year }}</td>
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
                                    <a href="{{ route('attendance.submissions.index') }}" class="btn btn-sm btn-outline-success rounded-0">
                                        Ver todos
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>

            {{-- OPERACIONAL --}}
            <div class="tab-pane fade" id="tab-operacional">
                @include('admin.dashboard.partials.operational')
            </div>

            {{-- FINANCEIRO --}}
            <div class="tab-pane fade" id="tab-financeiro">
                @include('admin.dashboard.partials.financial')
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener("DOMContentLoaded", () => {

    /* ======================================================
     * VARIÁVEIS GLOBAIS
     * ====================================================== */
    let attendanceChart = null;
    let financialChart  = null;
    let attendanceType  = 'pie';
    let financialType   = 'pie';

    /* ======================================================
     * HELPERS
     * ====================================================== */
    const brl = value =>
        new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' })
            .format(value ?? 0);

    /* ======================================================
     * GRÁFICO DE FREQUÊNCIAS
     * ====================================================== */
    function renderAttendanceChart(attendance) {
        const ctx = document.getElementById('attendanceChart');
        if (!ctx || !attendance) return;

        if (attendanceChart) attendanceChart.destroy();

        attendanceChart = new Chart(ctx, {
            type: attendanceType,
            data: {
                labels: ['Pendentes', 'Homologadas', 'Rejeitadas', 'Atrasadas'],
                datasets: [{
                    data: [
                        attendance.submitted  ?? 0,
                        attendance.approved ?? 0,
                        attendance.rejected ?? 0,
                        attendance.late     ?? 0
                    ],
                    backgroundColor: [
                        '#0dcaf0cc',
                        '#198754cc',
                        '#dc3545cc',
                        '#ffc107cc'
                    ],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });
    }

    /* ======================================================
     * GRÁFICO FINANCEIRO
     * ====================================================== */
    function renderFinancialChart(financial) {
        const ctx = document.getElementById('financialChart');
        if (!ctx || !financial) return;

        if (financialChart) financialChart.destroy();

        financialChart = new Chart(ctx, {
            type: financialType,
            data: {
                labels: ['Gerados', 'Pagos', 'Confirmados'],
                datasets: [{
                    label: 'Valor (R$)',
                    data: [
                        financial.counts.generated  ?? 0,
                        financial.counts.paid       ?? 0,
                        financial.counts.confirmed  ?? 0,
                    ],
                    backgroundColor: [
                        '#6c757d',
                        '#ffc107',
                        '#198754'
                    ],
                    borderRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                indexAxis: financialType === 'bar' ? 'y' : 'x',
                plugins: {
                    legend: {
                        display: financialType === 'pie',
                        position: 'bottom'
                    },
                    tooltip: {
                        callbacks: {
                            label: ctx => brl(ctx.raw)
                        }
                    }
                },
                scales: financialType === 'bar'
                ? { x: { grid: { display: false } }, y: { grid: { display: false } } }
                : { y: { ticks: { callback: value => brl(value) } } }
            }
        });
    }

    /* ======================================================
     * ATUALIZA UI
     * ====================================================== */
    function updateCards(data) {
        if (data.attendance) {
            document.getElementById('card-submitted').innerText = data.attendance.submitted ?? 0;
            document.getElementById('card-approved').innerText  = data.attendance.approved ?? 0;
            document.getElementById('card-rejected').innerText  = data.attendance.rejected ?? 0;
            document.getElementById('card-late').innerText      = data.attendance.late ?? 0;
        }

        if (data.financial) {
            document.getElementById('card-fin-generated').innerText  = data.financial.counts.generated;
            document.getElementById('card-fin-paid').innerText       = data.financial.counts.paid;
            document.getElementById('card-fin-confirmed').innerText  = data.financial.counts.confirmed;
            document.getElementById('card-fin-total').innerText      = brl(data.financial.totals.paid);
        }
    }

    function updateIndicators(percentages) {
        ['approved','submitted','rejected','draft','late'].forEach(key => {
            const percentEl = document.getElementById(`${key}-percent`);
            const barEl     = document.getElementById(`${key}-bar`);

            if (percentEl) {
                percentEl.innerText = (percentages[key] ?? 0) + '%';
            }

            if (barEl) {
                barEl.style.width = (percentages[key] ?? 0) + '%';
            }
        });
    }

    /* ======================================================
     * FETCH
     * ====================================================== */
    function loadStats(params) {
        fetch(`{{ route('admin.dashboard.stats') }}?${params}`)
            .then(res => res.json())
            .then(data => {
                updateCards(data);
                updateIndicators(data.general.percentages);
                renderAttendanceChart(data.attendance);
                renderFinancialChart(data.financial);
            });
    }

    /* ======================================================
     * FILTROS
     * ====================================================== */
    const filterType  = document.getElementById('filterType');
    const monthInput  = document.getElementById('monthInput');
    const rangeInputs = document.getElementById('rangeInputs');
    const startDate   = document.getElementById('startDate');
    const endDate     = document.getElementById('endDate');
    const toggleChart = document.getElementById('toggleChart');

    toggleChart?.addEventListener('click', () => {
        attendanceType = attendanceType === 'pie' ? 'bar' : 'pie';
        monthInput?.value && loadStats(`month=${monthInput.value}`);
    });

    toggleFinancialChart?.addEventListener('click', () => {
        financialType  = financialType === 'pie' ? 'bar' : 'pie';
        monthInput?.value && loadStats(`month=${monthInput.value}`);
    });

    filterType?.addEventListener('change', () => {
        const isMonth = filterType.value === 'month';
        monthInput.style.display  = isMonth ? 'block' : 'none';
        rangeInputs.style.display = isMonth ? 'none'  : 'flex';
    });

    monthInput?.addEventListener('change', () =>
        monthInput.value && loadStats(`month=${monthInput.value}`)
    );

    [startDate, endDate].forEach(el =>
        el?.addEventListener('change', () => {
            if (startDate.value && endDate.value) {
                loadStats(`start_date=${startDate.value}&end_date=${endDate.value}`);
            }
        })
    );

    /* ======================================================
     * INIT
     * ====================================================== */
    if (monthInput?.value) {
        loadStats(`month=${monthInput.value}`);
    }
});
</script>
@endpush
