@extends('layouts.pdf')

@section('title', 'Relatório Mensal de Frequência')

@section('header-extra')
<div style="margin-top: 5px;">
    <h3>BOLSA FORMACAO - PROGRAMA MULHERES MIL - IFRS {{ $submission->year }}</h3>
    <h4>REGISTRO DAS HORAS TRABALHADAS</h4>
    <h5>
        Campus {{ $submission->scholarshipHolder->unit->name ?? '-' }} -
        {{ str_pad($submission->month, 2, '0', STR_PAD_LEFT) }}/{{ $submission->year }}
    </h5>
</div>
@endsection

@section('content')
<h5>1. Dados do bolsista</h5>

<table>
    <tr>
        <th width="30%">Nome do Bolsista</th>
        <td>{{ $submission->scholarshipHolder->user->name }}</td>
    </tr>
    <tr>
        <th>Projeto</th>
        <td>{{ $submission->project?->name ?? '-' }}</td>
    </tr>
    <tr>
        <th>Mês/Ano</th>
        <td>{{ str_pad($submission->month, 2, '0', STR_PAD_LEFT) }}/{{ $submission->year }}</td>
    </tr>
</table>

<table>
    <thead>
        <tr>
            <th width="15%">Data</th>
            <th width="15%">Horas</th>
            <th>Atividades Desenvolvidas</th>
        </tr>
    </thead>
    <tbody>
        @for($i = 0; $i < 20; $i++)
            <tr>
                <td height="18"></td>
                <td></td>
                <td></td>
            </tr>
        @endfor
    </tbody>
</table>

<table>
    <tr>
        <th width="30%">Total de Horas</th>
        <td></td>
    </tr>
</table>

<table class="assinaturas">
    <tr>
        <td>
            _________________________________<br>
            Bolsista
        </td>
        <td>
            _________________________________<br>
            Coordenação
        </td>
    </tr>
</table>
@endsection
