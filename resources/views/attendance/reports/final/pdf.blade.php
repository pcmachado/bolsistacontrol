@extends('layouts.pdf')

@section('header-extra')
<h3>BOLSA FORMACAO - PROGRAMA MULHERES MIL</h3>
<h4>RELATORIO FINAL DE ATIVIDADES</h4>
@endsection

@section('content')
@if(!empty($reportLayout['body_html']))
    <div class="mb-3">
        {!! $reportLayout['body_html'] !!}
    </div>
@endif

<table>
    <tr>
        <td width="25%"><strong>Bolsista</strong></td>
        <td>{{ $report->scholarshipHolder->user->name }}</td>
    </tr>
    <tr>
        <td><strong>Projeto</strong></td>
        <td>{{ $report->project->name ?? '-' }}</td>
    </tr>
    <tr>
        <td><strong>Edital / Portaria</strong></td>
        <td>{{ $report->project?->pivot?->edital_portaria ?? '-' }}</td>
    </tr>
    <tr>
        <td><strong>Vigencia</strong></td>
        <td>
            {{ optional($report->start_date)->format('d/m/Y') }}
            ate
            {{ optional($report->end_date)->format('d/m/Y') }}
        </td>
    </tr>
</table>

<h5>Atividades desenvolvidas</h5>
<p>{!! nl2br(e($report->activities)) !!}</p>

<h5>Resultados alcancados</h5>
<p>{!! nl2br(e($report->results)) !!}</p>

<h5>Contribuicoes</h5>
<p>{!! nl2br(e($report->contributions)) !!}</p>

<div class="assinatura">
    ___________________________________<br>
    Bolsista
</div>
@endsection
