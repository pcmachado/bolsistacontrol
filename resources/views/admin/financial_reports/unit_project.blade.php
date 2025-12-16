@extends('layouts.app')

@section('title', 'Relatório por Unidade / Projeto')

@section('content')
<div class="container-fluid">

    <h3 class="mb-4">
        <i class="bi bi-building"></i>
        Relatório Consolidado por Unidade / Projeto
    </h3>

    {{-- FILTROS --}}
    <form class="card p-3 mb-4 shadow-sm">
        <div class="row g-3">

            <div class="col-md-3">
                <label>Unidade</label>
                <select class="form-select" name="unit_id">
                    <option value="">Todas</option>
                    @foreach($units as $u)
                        <option value="{{ $u->id }}" @selected($filters['unit_id']==$u->id)>
                            {{ $u->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-3">
                <label>Projeto</label>
                <select class="form-select" name="project_id">
                    <option value="">Todos</option>
                    @foreach($projects as $p)
                        <option value="{{ $p->id }}" @selected($filters['project_id']==$p->id)>
                            {{ $p->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-2">
                <label>Ano</label>
                <input type="number" class="form-control" name="year" value="{{ $filters['year'] }}">
            </div>

            <div class="col-md-2">
                <label>Mês</label>
                <input type="number" min="1" max="12" class="form-control" name="month" value="{{ $filters['month'] }}">
            </div>

            <div class="col-md-2">
                <label>Status</label>
                <select name="status" class="form-select">
                    <option value="">Todos</option>
                    <option value="sent_to_payment" @selected($filters['status']=='sent_to_payment')>Enviado</option>
                    <option value="paid" @selected($filters['status']=='paid')>Pago</option>
                    <option value="confirmed" @selected($filters['status']=='confirmed')>Confirmado</option>
                </select>
            </div>

            <div class="col-md-1 d-flex align-items-end">
                <button class="btn btn-primary w-100">Filtrar</button>
            </div>

        </div>
    </form>

    @if($payments->count())

        {{-- Botões PDF / Excel --}}
        <div class="mb-3">
            <a class="btn btn-danger btn-sm"
               target="_blank"
               href="{{ route('admin.financial-reports.unit-project.pdf', request()->query()) }}">
                <i class="bi bi-filetype-pdf"></i> PDF
            </a>

            <a class="btn btn-success btn-sm"
               href="{{ route('admin.financial-reports.unit-project.excel', request()->query()) }}">
                <i class="bi bi-filetype-xlsx"></i> Excel
            </a>
        </div>

        {{-- TOTAL GERAL --}}
        <div class="alert alert-primary">
            <strong>Total Geral:</strong>
            R$ {{ number_format($totals['overall'], 2, ',', '.') }}
        </div>

        {{-- TOTAL POR UNIDADE --}}
        <h5 class="mt-4">Totais por Unidade</h5>
        <ul>
            @foreach($totals['byUnit'] as $name => $value)
                <li>{{ $name }}:
                    <strong>R$ {{ number_format($value, 2, ',', '.') }}</strong>
                </li>
            @endforeach
        </ul>

        {{-- TOTAL POR PROJETO --}}
        <h5 class="mt-4">Totais por Projeto</h5>
        <ul>
            @foreach($totals['byProject'] as $name => $value)
                <li>{{ $name }}:
                    <strong>R$ {{ number_format($value, 2, ',', '.') }}</strong>
                </li>
            @endforeach
        </ul>

        {{-- LISTAGEM --}}
        <div class="card shadow-sm mt-4">
            <div class="card-body">

                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Referência</th>
                            <th>Bolsista</th>
                            <th>Projeto</th>
                            <th>Unidade</th>
                            <th>Status</th>
                            <th>Valor</th>
                            <th>Recibo</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($payments as $p)
                            <tr>
                                <td>{{ $p->month }}/{{ $p->year }}</td>
                                <td>{{ $p->scholarshipHolder->name }}</td>
                                <td>{{ $p->project->name }}</td>
                                <td>{{ $p->unit->name }}</td>
                                <td>@include('admin.financial_reports.status_badge', ['p' => $p])</td>
                                <td>R$ {{ number_format($p->amount, 2, ',', '.') }}</td>

                                <td>
                                    @if($p->receipt_number)
                                        <a href="{{ route('payments.receipt', $p) }}" target="_blank">
                                            {{ $p->receipt_number }}
                                        </a>
                                    @else
                                        —
                                    @endif
                                </td>

                            </tr>
                        @endforeach
                    </tbody>
                </table>

            </div>
        </div>

    @endif
</div>
@endsection
