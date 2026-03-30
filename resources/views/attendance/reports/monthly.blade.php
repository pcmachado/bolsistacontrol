@extends('layouts.pdf')

@section('title', 'Relatório Mensal de Frequência')

@section('header-extra')

<div style="margin-top: 5px;">
    <h3>BOLSA FORMAÇÃO – PROGRAMA MULHERES MIL - IFRS {{ $submission->year }}</h3>
    <h4>REGISTRO DAS HORAS TRABALHADAS</h4>
    <h5>
        Campus {{ $submission->scholarshipHolder->unit->name ?? '—' }} –
        {{ str_pad($submission->month, 2, '0', STR_PAD_LEFT) }}/{{ $submission->year }}
    </h5>
</div>

@endsection

@section('content')

<h5>1. Dados do bolsista</h5>

<table>
<tr>
    <td width="30%"><strong>Nome</strong></td>
    <td>{{ $submission->scholarshipHolder->user->name }}</td>
</tr>
<tr>
    <td><strong>CPF</strong></td>
    <td>{{ $submission->scholarshipHolder->cpf }}</td>
</tr>
<tr>
    <td><strong>Carga horária prevista</strong></td>
    <td>
        {{ $submission->scholarshipHolder->weekly_limit_minutes
            ? ($submission->scholarshipHolder->weekly_limit_minutes / 60) * 4
            : '—'
        }} horas mensais
    </td>
</tr>
</table>

<h5>2. Quadro das horas e atividades</h5>

<table>
<thead>
<tr>
    <th width="15%">Data</th>
    <th width="15%">Horas</th>
    <th>Atividades</th>
</tr>
</thead>
<tbody>

@php $totalHoras = 0; @endphp

@forelse($records as $record)
    @php $totalHoras += $record->hours; @endphp

<tr>
    <td>{{ $record->date->format('d/m/Y') }}</td>
    <td>{{ hoursToTime($record->hours) }}</td>
    <td>{{ $record->description ?: '—' }}</td>
</tr>

@empty
<tr>
    <td colspan="3">Nenhuma atividade registrada.</td>
</tr>
@endforelse

<tr>
    <td><strong>Total</strong></td>
    <td><strong>{{ hoursToTime($totalHoras) }}</strong></td>
    <td></td>
</tr>

</tbody>
</table>

<h5>3. Assinaturas</h5>

<table class="assinaturas no-break">
<tr>
    <td>
        <div style="border-top:1px solid #000; width:80%; margin:0 auto;"></div>
        Coordenação Adjunta
    </td>
    <td>
        <div style="border-top:1px solid #000; width:80%; margin:0 auto;"></div>
        Coordenação Geral
    </td>
</tr>
</table>

@endsection