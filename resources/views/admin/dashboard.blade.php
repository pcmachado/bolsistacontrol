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

        <div class="col-12 col-md-6 col-lg-3">
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
        </div>
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
                    </div>
                </div>
            </a>
        </div>

        {{-- Pendentes --}}
        <div class="col-md-3">
            <a href="{{ route('attendance.card.pending') }}" class="text-decoration-none">
                <div class="card text-white bg-info shadow-sm">
                    <div class="card-body text-center">
                        <i class="bi bi-hourglass-split display-5"></i>
                        <h5 class="card-title mt-2">Pendentes</h5>
                        <h2>{{ $counts['pending'] ?? 0 }}</h2>
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
                    </div>
                </div>
            </a>
        </div>
    </div>

    <hr class="my-4">

    <!-- Relatórios e Gráficos (Grid 2/3 e 1/3 no Desktop) -->
    <section class="row g-4 mb-4">
        
        {{-- Gráfico --}}
        <div class="row">
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Distribuição dos Registros</h5>
                        <canvas id="attendanceChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Coluna Secundária (4 colunas no desktop, 12 no mobile) -->
        <div class="col-lg-4">
            <div class="card shadow-lg rounded-3 h-100">
                <div class="card-body p-4">
                    <h2 class="h5 card-title fw-semibold mb-4">Metas de Progresso</h2>
                    
                    <!-- Indicador de Progresso 1 -->
                    <div class="mb-4">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-primary fw-medium">Meta de Receita</span>
                            <span class="text-primary fw-medium">75%</span>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-primary" role="progressbar" style="width: 75%;" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>

                    <!-- Indicador de Progresso 2 -->
                    <div class="mb-4">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-success fw-medium">Novos Usuários</span>
                            <span class="text-success fw-medium">90%</span>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-success" role="progressbar" style="width: 90%;" aria-valuenow="90" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>

                    <!-- Indicador de Progresso 3 -->
                    <div class="mb-2">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-warning fw-medium">Lançamento de Produto</span>
                            <span class="text-warning fw-medium">40%</span>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-warning" role="progressbar" style="width: 40%;" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Tabela de Últimas Transações (Componente de UI Pré-construído) -->
    <section class="card shadow-lg rounded-3">
        <div class="card-body p-4">
            <h2 class="h5 card-title fw-semibold mb-4">Últimas Transações</h2>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th scope="col">ID</th>
                            <th scope="col">Cliente</th>
                            <th scope="col">Valor</th>
                            <th scope="col">Status</th>
                            <th scope="col">Data</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Linha de Dados 1 (Exemplo) -->
                        <tr>
                            <th scope="row" class="fw-bold text-dark">#001245</th>
                            <td>Ana Silva</td>
                            <td>R$ 450.00</td>
                            <td><span class="badge text-bg-success rounded-pill px-3 py-1">Concluída</span></td>
                            <td>2024-10-25</td>
                        </tr>
                        <!-- Linha de Dados 2 (Exemplo) -->
                        <tr>
                            <th scope="row" class="fw-bold text-dark">#001246</th>
                            <td>João Souza</td>
                            <td>R$ 120.50</td>
                            <td><span class="badge text-bg-warning rounded-pill px-3 py-1">Pendente</span></td>
                            <td>2024-10-25</td>
                        </tr>
                        <!-- Linha de Dados 3 (Exemplo) -->
                        <tr>
                            <th scope="row" class="fw-bold text-dark">#001247</th>
                            <td>Mariana Lima</td>
                            <td>R$ 89.90</td>
                            <td><span class="badge text-bg-danger rounded-pill px-3 py-1">Cancelada</span></td>
                            <td>2024-10-24</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </section>
@endsection
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('attendanceChart').getContext('2d');
    new Chart(ctx, {
        type: 'pie', // pode trocar para 'bar'
        data: {
            labels: ['Homologadas', 'Pendentes', 'Em Atraso', 'Rejeitadas'],
            datasets: [{
                data: [
                    {{ $counts['approved'] }},
                    {{ $counts['pending'] }},
                    {{ $counts['late'] }},
                    {{ $counts['rejected'] }}
                ],
                backgroundColor: [
                    'rgba(25, 135, 84, 0.8)',   // verde
                    'rgba(13, 202, 240, 0.8)',  // azul
                    'rgba(255, 193, 7, 0.8)',   // amarelo
                    'rgba(220, 53, 69, 0.8)'    // vermelho
                ],
                borderColor: [
                    'rgba(25, 135, 84, 1)',
                    'rgba(13, 202, 240, 1)',
                    'rgba(255, 193, 7, 1)',
                    'rgba(220, 53, 69, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'bottom' }
            }
        }
    });
</script>
@endpush