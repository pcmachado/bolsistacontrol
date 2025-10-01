@extends('layouts.app')

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
            <div class="card shadow-lg border-start border-5 border-primary rounded-3 h-100">
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
                        <p class="text-secondary fw-semibold mb-0">Receita Média</p>
                        <span class="badge text-bg-success fw-bold">+7.8%</span>
                    </div>
                    <h2 class="card-title h3 fw-bolder text-dark">R$ 135.50</h2>
                    <p class="card-text small text-muted mt-2">Valor por transação</p>
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

    <!-- Relatórios e Gráficos (Grid 2/3 e 1/3 no Desktop) -->
    <section class="row g-4 mb-4">
        
        <!-- Coluna Principal para Gráfico (8 colunas no desktop, 12 no mobile) -->
        <div class="col-lg-8">
            <div class="card shadow-lg rounded-3 h-100">
                <div class="card-body p-4">
                    <h2 class="h5 card-title fw-semibold mb-4">Gráfico de Linha de Desempenho</h2>
                    <!-- Placeholder para a biblioteca de gráficos -->
                    <div class="d-flex align-items-center justify-content-center bg-light border border-dashed border-secondary-subtle rounded-3 text-secondary" style="height: 250px;">
                        Placeholder do Gráfico (Biblioteca Chart.js seria integrada aqui)
                    </div>
                    <button class="btn btn-primary mt-4 px-4 py-2 rounded-3 shadow-sm">
                        Gerar Relatório Detalhado
                    </button>
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
