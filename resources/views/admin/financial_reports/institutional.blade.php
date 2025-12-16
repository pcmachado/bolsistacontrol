@extends('layouts.app')

@section('title', 'Relatório Consolidado Institucional')

@section('content')
<div class="container-fluid">

    <h3 class="mb-4">
        <i class="bi bi-graph-up-arrow"></i>
        Relatório Financeiro Institucional – {{ $year }}
    </h3>

    {{-- FILTRO ANO --}}
    <form class="card p-3 mb-4 shadow-sm">
        <div class="row g-3">
            <div class="col-md-3">
                <label>Ano</label>
                <input type="number" class="form-control" name="year" value="{{ $year }}">
            </div>

            <div class="col-md-2 d-flex align-items-end">
                <button class="btn btn-primary w-100">Filtrar</button>
            </div>
        </div>
    </form>

    {{-- EXPORTAÇÕES --}}
    <div class="mb-3">
        <a class="btn btn-danger btn-sm" target="_blank"
            href="{{ route('admin.financial-reports.institutional.pdf', ['year'=>$year]) }}">
            <i class="bi bi-file-earmark-pdf"></i> PDF
        </a>

        <a class="btn btn-success btn-sm"
            href="{{ route('admin.financial-reports.institutional.excel', ['year'=>$year]) }}">
            <i class="bi bi-file-earmark-excel"></i> Excel
        </a>
    </div>

    {{-- CARTÕES DE RESUMO --}}
    <div class="row g-3">

        <div class="col-md-3">
            <div class="card shadow-sm p-3 border-left-primary">
                <h6>Total Pago no Ano</h6>
                <h3>R$ {{ number_format($summary['total_paid'],2,',','.') }}</h3>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm p-3 border-left-success">
                <h6>Média Mensal</h6>
                <h3>R$ {{ number_format($summary['avg_monthly'],2,',','.') }}</h3>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm p-3 border-left-info">
                <h6>Bolsistas Ativos</h6>
                <h3>{{ $summary['active_bolsistas'] }}</h3>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm p-3 border-left-warning">
                <h6>Projetos Ativos</h6>
                <h3>{{ $summary['active_projects'] }}</h3>
            </div>
        </div>
    </div>

    {{-- TOTAIS AGRUPADOS --}}
    <div class="mt-4">

        <h4 class="mt-4">Totais por Unidade</h4>
        <ul>
            @foreach($totals['byUnit'] as $unit => $total)
                <li>{{ $unit }}:
                    <strong>R$ {{ number_format($total,2,',','.') }}</strong>
                </li>
            @endforeach
        </ul>

        <h4 class="mt-4">Totais por Projeto</h4>
        <ul>
            @foreach($totals['byProject'] as $proj => $total)
                <li>{{ $proj }}:
                    <strong>R$ {{ number_format($total,2,',','.') }}</strong>
                </li>
            @endforeach
        </ul>

        <h4 class="mt-4">Totais por Mês</h4>
        <ul>
            @foreach($totals['byMonth'] as $month => $total)
                <li>Mês {{ $month }}:
                    <strong>R$ {{ number_format($total,2,',','.') }}</strong>
                </li>
            @endforeach
        </ul>

        <h4 class="mt-4">Totais por Status</h4>
        <ul>
            @foreach($totals['byStatus'] as $status => $total)
                <li>{{ ucfirst($status) }}:
                    <strong>R$ {{ number_format($total,2,',','.') }}</strong>
                </li>
            @endforeach
        </ul>
    </div>

</div>
@endsection
