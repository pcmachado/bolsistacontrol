@extends('layouts.app')

@section('title', 'Dashboard Financeiro')

@section('content')
<div class="container-fluid">

    <h3 class="mb-4">
        <i class="bi bi-graph-up"></i> Dashboard Financeiro
    </h3>

    {{-- FILTROS --}}
    <form class="row g-3 mb-4">
        <div class="col-md-2">
            <label>Mês</label>
            <input type="number" class="form-control" name="month" value="{{ $month }}" min="1" max="12">
        </div>

        <div class="col-md-2">
            <label>Ano</label>
            <input type="number" class="form-control" name="year" value="{{ $year }}">
        </div>

        <div class="col-md-3">
            <label>Projeto</label>
            <select name="project_id" class="form-select">
                <option value="">Todos</option>
                @foreach($projects as $p)
                    <option value="{{ $p->id }}" {{ request('project_id') == $p->id ? 'selected' : '' }}>
                        {{ $p->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-3">
            <label>Unidade</label>
            <select name="unit_id" class="form-select">
                <option value="">Todas</option>
                @foreach($units as $u)
                    <option value="{{ $u->id }}" {{ request('unit_id') == $u->id ? 'selected' : '' }}>
                        {{ $u->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-2 d-flex align-items-end">
            <button class="btn btn-primary w-100">Filtrar</button>
        </div>
    </form>


    {{-- CARDS --}}
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h5>Pago</h5>
                    <h3>R$ {{ number_format($totalPaid, 2, ',', '.') }}</h3>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h5>Confirmado</h5>
                    <h3>R$ {{ number_format($totalConfirmed, 2, ',', '.') }}</h3>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card bg-warning text-dark">
                <div class="card-body">
                    <h5>Pendente</h5>
                    <h3>R$ {{ number_format($totalPending, 2, ',', '.') }}</h3>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card bg-secondary text-white">
                <div class="card-body">
                    <h5>Pagamentos Pendentes</h5>
                    <h3>{{ $countPending }}</h3>
                </div>
            </div>
        </div>
    </div>


    {{-- GRÁFICOS --}}
    <div class="row">
        <div class="col-md-6">
            <canvas id="chartProject"></canvas>
        </div>
        <div class="col-md-6">
            <canvas id="chartUnit"></canvas>
        </div>
    </div>


    {{-- TABELAS --}}
    <div class="row mt-4">
        <div class="col-md-6">
            <h5>Últimos pagamentos</h5>
            <ul class="list-group">
                @foreach($latestPayments as $p)
                    <li class="list-group-item">
                        {{ $p->scholarshipHolder->name }} — 
                        R$ {{ number_format($p->amount,2,',','.') }}
                    </li>
                @endforeach
            </ul>
        </div>

        <div class="col-md-6">
            <h5>Pendentes</h5>
            <ul class="list-group">
                @foreach($pendingPayments as $p)
                    <li class="list-group-item">
                        {{ $p->scholarshipHolder->name }} — 
                        R$ {{ number_format($p->amount,2,',','.') }}
                    </li>
                @endforeach
            </ul>
        </div>
    </div>

</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
new Chart(document.getElementById('chartProject'), {
    type: 'pie',
    data: {
        labels: {!! json_encode($chartByProject->pluck('project.name')) !!},
        datasets: [{
            data: {!! json_encode($chartByProject->pluck('total')) !!},
        }]
    }
});

new Chart(document.getElementById('chartUnit'), {
    type: 'bar',
    data: {
        labels: {!! json_encode($chartByUnit->pluck('unit.name')) !!},
        datasets: [{
            data: {!! json_encode($chartByUnit->pluck('total')) !!},
        }]
    }
});
</script>
@endpush

@endsection
