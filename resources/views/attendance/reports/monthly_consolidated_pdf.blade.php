@extends('layouts.pdf')

@section('title', 'Relatório Consolidado Mensal')

@section('header-extra')
<div style="margin-top: 5px;">
    <h3>RELATÓRIO CONSOLIDADO MENSAL</h3>
    <h5>{{ $holder->user->name ?? '-' }} - {{ $period->format('m/Y') }}</h5>
</div>
@endsection

@section('content')
<table class="no-break">
    <thead>
        <tr>
            <th>Projeto</th>
            <th>Horas previstas</th>
            <th>Horas registradas</th>
            <th>Diferença</th>
        </tr>
    </thead>
    <tbody>
        @forelse($rows as $row)
            <tr>
                <td>{{ $row['project_name'] }}</td>
                <td>{{ number_format($row['expected_hours'], 1, ',', '.') }}h</td>
                <td>{{ number_format($row['registered_hours'], 1, ',', '.') }}h</td>
                <td>{{ number_format($row['difference_hours'], 1, ',', '.') }}h</td>
            </tr>
        @empty
            <tr><td colspan="4">Sem projetos vinculados.</td></tr>
        @endforelse
    </tbody>
    <tfoot>
        <tr>
            <td><strong>Total geral</strong></td>
            <td><strong>{{ number_format($totals['expected_hours'], 1, ',', '.') }}h</strong></td>
            <td><strong>{{ number_format($totals['registered_hours'], 1, ',', '.') }}h</strong></td>
            <td><strong>{{ number_format($totals['difference_hours'], 1, ',', '.') }}h</strong></td>
        </tr>
    </tfoot>
</table>
@endsection
