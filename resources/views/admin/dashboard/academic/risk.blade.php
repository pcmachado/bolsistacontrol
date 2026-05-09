@extends('layouts.app')

@section('content')
<div class="container-fluid">

    {{-- CABEÇALHO --}}
    <div class="row mb-4">
        <div class="col-12">
            <h3 class="fw-bold mb-1">📊 Monitoramento de Risco Acadêmico</h3>
            <small class="text-muted">Acompanhamento automático de alunos em risco de evasão</small>
        </div>
    </div>

    {{-- RESUMO GERAL --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-danger">
                <div class="card-body text-center">
                    <h6 class="text-muted mb-2">Críticos</h6>
                    <h2 class="text-danger fw-bold">{{ $summary['critical'] ?? 0 }}</h2>
                    <small>Faltas > 25%</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-warning">
                <div class="card-body text-center">
                    <h6 class="text-muted mb-2">Em Risco</h6>
                    <h2 class="text-warning fw-bold">{{ $summary['risk'] ?? 0 }}</h2>
                    <small>Faltas 15%-25%</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-info">
                <div class="card-body text-center">
                    <h6 class="text-muted mb-2">Atenção</h6>
                    <h2 class="text-info fw-bold">{{ $summary['warning'] ?? 0 }}</h2>
                    <small>Faltas 10%-15%</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-success">
                <div class="card-body text-center">
                    <h6 class="text-muted mb-2">Regulares</h6>
                    <h2 class="text-success fw-bold">{{ $summary['ok'] ?? 0 }}</h2>
                    <small>Faltas < 10%</small>
                </div>
            </div>
        </div>
    </div>

    {{-- FILTROS --}}
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.dashboard.academic') }}" class="row g-2">
                <div class="col-md-4">
                    <label class="form-label">Nível de Risco</label>
                    <select name="level" class="form-select">
                        <option value="">Todos</option>
                        <option value="critical" {{ request('level') === 'critical' ? 'selected' : '' }}>Críticos</option>
                        <option value="risk" {{ request('level') === 'risk' ? 'selected' : '' }}>Em Risco</option>
                        <option value="warning" {{ request('level') === 'warning' ? 'selected' : '' }}>Atenção</option>
                        <option value="ok" {{ request('level') === 'ok' ? 'selected' : '' }}>Regulares</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Buscar Aluno</label>
                    <input type="text" name="student" class="form-control" placeholder="Nome ou matrícula" 
                           value="{{ request('student') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">&nbsp;</label>
                    <button type="submit" class="btn btn-primary w-100">Filtrar</button>
                </div>
                <div class="col-md-2">
                    <label class="form-label">&nbsp;</label>
                    <a href="{{ route('admin.dashboard.academic') }}" class="btn btn-outline-secondary w-100">Limpar</a>
                </div>
            </form>
        </div>
    </div>

    {{-- TABELA DE ALUNOS --}}
    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Aluno</th>
                        <th>Turma</th>
                        <th>Total de Faltas</th>
                        <th>Percentual</th>
                        <th>Nível</th>
                        <th>Tendência</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($data as $d)
                        @php
                            $trend = $d['trend'] ?? 'stable';
                            $trendIcon = match($trend) {
                                'increasing' => '📈 Piorando',
                                'decreasing' => '📉 Melhorando',
                                default => '➡️ Estável'
                            };
                        @endphp
                        <tr>
                            <td>
                                <strong>{{ $d['student_name'] ?? "Aluno {$d['student_id']}" }}</strong>
                                <br>
                                <small class="text-muted">ID: {{ $d['student_id'] }}</small>
                            </td>
                            <td>
                                <small>{{ $d['class_name'] ?? '-' }}</small>
                            </td>
                            <td>
                                <span class="font-monospace">{{ $d['absences'] }} / {{ $d['total'] }}</span>
                            </td>
                            <td>
                                <span class="badge bg-{{ match($d['level']) {
                                    'critical' => 'danger',
                                    'risk' => 'warning',
                                    'warning' => 'info',
                                    default => 'success'
                                } }}">
                                    {{ number_format($d['percent'], 1) }}%
                                </span>
                            </td>
                            <td>
                                @switch($d['level'])
                                    @case('critical')
                                        <span class="badge bg-danger">🚨 Crítico</span>
                                    @break
                                    @case('risk')
                                        <span class="badge bg-warning text-dark">⚠️ Risco</span>
                                    @break
                                    @case('warning')
                                        <span class="badge bg-info">ℹ️ Atenção</span>
                                    @break
                                    @default
                                        <span class="badge bg-success">✓ OK</span>
                                @endswitch
                            </td>
                            <td>
                                <small>{{ $trendIcon }}</small>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm" role="group">
                                    <a href="#" class="btn btn-outline-primary" title="Ver detalhes">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <button type="button" class="btn btn-outline-warning" title="Notificar" 
                                            data-student="{{ $d['student_id'] }}">
                                        <i class="bi bi-bell"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                Nenhum aluno encontrado com os filtros selecionados
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- RODAPÉ COM DOCUMENTAÇÃO --}}
    <div class="alert alert-info mt-4 mb-0">
        <small>
            <strong>📋 Nota:</strong> Este monitor acompanha em tempo real alunos com potencial risco de evasão.
            Coordenadores recebem notificações automáticas quando um aluno ultrapassa os limiares de faltas.
        </small>
    </div>

</div>
@endsection