@extends('layouts.pdf')

@section('title', 'Relatório Consolidado')

@section('header-extra')

<div style="margin-top:5px;">
    <h3>RELATÓRIO CONSOLIDADO DE PAGAMENTOS</h3>
</div>

@endsection

@section('content')

@foreach($grouped as $group)

    <h4>{{ $group['unit'] }}</h4>

    <table>
        <thead>
            <tr>
                <th>Bolsista</th>
                <th>Projeto</th>
                <th>Período</th>
                <th>Status</th>
                <th>Valor</th>
            </tr>
        </thead>
        <tbody>

            @foreach($group['payments'] as $p)
            <tr>
                <td>{{ $p['holder'] }}</td>
                <td>{{ $p['project'] }}</td>
                <td>{{ $p['period'] }}</td>
                <td>{{ $p['status'] }}</td>
                <td>R$ {{ number_format($p['amount'], 2, ',', '.') }}</td>
            </tr>
            @endforeach

            <tr>
                <td colspan="4"><strong>Total</strong></td>
                <td><strong>R$ {{ number_format($group['total'], 2, ',', '.') }}</strong></td>
            </tr>

        </tbody>
    </table>

@endforeach

<h5>Total geral: R$ {{ number_format($totalGeral, 2, ',', '.') }}</h5>

@endsection