@extends('layouts.pdf')

@section('content')

<style>
    body {
        font-family: DejaVu Sans, sans-serif;
        font-size: 12px;
    }

    h4 {
        margin: 6px 0 4px 0;
        font-size: 12px;
    }

    .info-table td {
        padding: 4px;
        font-size: 11px;
    }

    .lined-box {
        border: 1px solid #000;
        min-height: 75px;
        padding: 5px;
        margin-bottom: 8px;
    }

    .line {
        border-bottom: 1px solid #999;
        height: 14px;
        margin-bottom: 3px;
    }

    .assinaturas td {
        border: none;
        text-align: center;
        padding-top: 40px;
        font-size: 12px;
    }

    .obs {
        margin-top: 10px;
        font-size: 10px;
        text-align: center;
    }
</style>

<table class="info-table no-break">
    <tr>
        <td width="50%">
            <strong>Bolsista:</strong>
            {{ auth()->user()->name }}
        </td>
        <td width="50%">
            <strong>Projeto:</strong>
            {{ $project->name ?? '-' }}
        </td>
    </tr>
    <tr>
        <td>
            <strong>Edital / Portaria:</strong>
            {{ $pivot->edital_portaria ?? '-' }}
        </td>
        <td>
            <strong>Vigência:</strong>
            {{ \Carbon\Carbon::parse($pivot->start_date)->format('d/m/Y') }}
            até ___/___/____
        </td>
    </tr>
</table>

<h4>1. Atividades desenvolvidas</h4>
<div class="lined-box">
    @for($i = 0; $i < 5; $i++)
        <div class="line"></div>
    @endfor
</div>

<h4>2. Resultados alcançados</h4>
<div class="lined-box">
    @for($i = 0; $i < 5; $i++)
        <div class="line"></div>
    @endfor
</div>

<h4>3. Contribuições</h4>
<div class="lined-box">
    @for($i = 0; $i < 5; $i++)
        <div class="line"></div>
    @endfor
</div>

<table class="assinaturas no-break">
    <tr>
        <td width="60%">
            ___________________________<br>
            Bolsista
        </td>
        <td width="60%">
            ___________________________<br>
            Coordenação
        </td>
    </tr>
</table>

<p class="obs">
    A entrega deste relatório deverá ocorrer no máximo 30 dias antes do último pagamento da bolsa.
</p>

@endsection
