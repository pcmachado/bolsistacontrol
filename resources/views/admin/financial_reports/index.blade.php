@extends('layouts.app')

@section('title', 'Relatório Financeiro')

@section('content')
<div class="container-fluid">
    <h3 class="mb-4"><i class="bi bi-cash-stack"></i> Relatório Financeiro</h3>

    {{-- FILTROS --}}
    <form method="GET" class="card p-3 shadow-sm mb-4">
        <div class="row g-3">

            <div class="col-md-2">
                <label>Mês</label>
                <input type="number" name="month" class="form-control"
                       value="{{ $filters['month'] }}">
            </div>

            <div class="col-md-2">
                <label>Ano</label>
                <input type="number" name="year" class="form-control"
                       value="{{ $filters['year'] }}">
            </div>

            <div class="col-md-3">
                <label>Projeto</label>
                <select name="project_id" class="form-select">
                    <option value="">Todos</option>
                    @foreach($projects as $p)
                        <option value="{{ $p->id }}"
                            {{ $filters['project'] == $p->id ? 'selected' : '' }}>
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
                        <option value="{{ $u->id }}"
                            {{ $filters['unit'] == $u->id ? 'selected' : '' }}>
                            {{ $u->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-2">
                <label>Status</label>
                <select name="status" class="form-select">
                    <option value="">Todos</option>
                    <option value="sent_to_payment" {{ $filters['status']=='sent_to_payment'?'selected':'' }}>Enviado</option>
                    <option value="paid" {{ $filters['status']=='paid'?'selected':'' }}>Pago</option>
                    <option value="confirmed" {{ $filters['status']=='confirmed'?'selected':'' }}>Confirmado</option>
                </select>
            </div>

            <div class="col-md-3">
                <label>Data início</label>
                <input type="date" name="start_date" class="form-control"
                       value="{{ $filters['start'] }}">
            </div>

            <div class="col-md-3">
                <label>Data fim</label>
                <input type="date" name="end_date" class="form-control"
                       value="{{ $filters['end'] }}">
            </div>

            <div class="col-md-2 d-flex align-items-end">
                <button class="btn btn-primary w-100">Filtrar</button>
            </div>

            <div class="col-md-2 d-flex align-items-end">
                <a href="{{ route('admin.financial-reports.pdf', request()->query()) }}"
                   class="btn btn-danger w-100" target="_blank">
                    PDF
                </a>
            </div>

            <div class="col-md-2 d-flex align-items-end">
                <a href="{{ route('admin.financial-reports.excel', request()->query()) }}"
                   class="btn btn-success w-100">
                    Excel
                </a>
            </div>

        </div>
    </form>


    {{-- RESULTADOS --}}
    <div class="card shadow-sm">
        <div class="card-body">

            <h5>Total do período:
                <strong>R$ {{ number_format($total, 2, ',', '.') }}</strong>
            </h5>

            <table class="table table-striped mt-3 align-middle">
                <thead>
                    <tr>
                        <th>Data</th>
                        <th>Bolsista</th>
                        <th>Projeto</th>
                        <th>Unidade</th>
                        <th>Status</th>
                        <th>Valor</th>
                    </tr>
                </thead>

                <tbody>
                @foreach($payments as $p)
                    <tr>
                        <td>{{ $p->month }}/{{ $p->year }}</td>
                        <td>{{ $p->scholarshipHolder->name }}</td>
                        <td>{{ $p->project->name }}</td>
                        <td>{{ $p->unit->name }}</td>
                        <td>
                            @include('admin.financial_reports.status_badge', ['p' => $p])
                        </td>
                        <td>R$ {{ number_format($p->amount, 2, ',', '.') }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>

        </div>
    </div>

</div>
@endsection
