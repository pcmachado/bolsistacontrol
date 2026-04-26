@extends('layouts.pdf')

@section('header')
<h3>BOLSA FORMAÇÃO – PROGRAMA MULHERES MIL</h3>
<h4>RELATÓRIO FINAL DE ATIVIDADES</h4>
@endsection

@section('content')
<table>
    <tr>
        <td width="25%"><strong>Bolsista</strong></td>
        <td>{{ $report->scholarshipHolder->user->name }}</td>
    </tr>
    <tr>
        <td><strong>Projeto</strong></td>
        <td>{{ $report->project->name ?? '—' }}</td>
    </tr>
    <tr>
        <td><strong>Edital / Portaria</strong></td>
        <td>{{ $report->project?->pivot?->edital_portaria ?? '-' }}</td>
    </tr>
    <tr>
        <td><strong>Vigência</strong></td>
        <td>
            {{ optional($report->start_date)->format('d/m/Y') }}
            até
            {{ optional($report->end_date)->format('d/m/Y') }}
        </td>
    </tr>
</table>

<h5>Atividades desenvolvidas</h5>
<p>{!! nl2br(e($report->activities)) !!}</p>

<h5>Resultados alcançados</h5>
<p>{!! nl2br(e($report->results)) !!}</p>

<h5>Contribuições</h5>
<p>{!! nl2br(e($report->contributions)) !!}</p>

<div class="assinatura">
    ___________________________________<br>
    Bolsista
</div>
@endsection