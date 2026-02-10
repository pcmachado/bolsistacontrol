<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Relatório Mensal de Frequência</title>

    <style>
        @page {
            size: A4 portrait;
            margin: 2cm;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #000;
        }

        /* =========================
           CABEÇALHO
        ========================= */
        .header {
            text-align: center;
            margin-bottom: 15px;
        }

        .logos {
            width: 100%;
            margin-bottom: 10px;
        }

        .logos td {
            border: none;
            text-align: center;
            vertical-align: middle;
        }

        .logos img {
            max-height: 70px;
        }

        h3, h4, h5 {
            margin: 4px 0;
        }

        /* =========================
           TABELAS
        ========================= */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        th, td {
            border: 1px solid #000;
            padding: 6px;
            vertical-align: top;
        }

        th {
            background-color: #f0f0f0;
        }

        /* =========================
           ASSINATURAS
        ========================= */
        .assinaturas {
            margin-top: 40px;
        }

        .assinaturas td {
            border: none;
            text-align: center;
            padding-top: 40px;
        }

        /* =========================
           RODAPÉ
        ========================= */
        .rodape {
            font-size: 10px;
            text-align: right;
            margin-top: 20px;
        }

        /* Botão não aparece no PDF */
        .no-print {
            margin-bottom: 15px;
        }
    </style>
</head>
<body>

{{-- BOTÃO PDF (fora do PDF gerado) --}}
<div class="no-print">
    <a href="{{ request()->fullUrlWithQuery(['pdf' => 1]) }}"
       class="btn btn-danger"
       target="_blank">
        Baixar PDF
    </a>
</div>

{{-- =========================
     CABEÇALHO INSTITUCIONAL
========================= --}}
<div class="header">

    <table class="logos">
        <tr>
            <td width="50%">
                <img src="{{ public_path('images/ifrs.png') }}" alt="IFRS">
            </td>
            <td width="50%">
                <img src="{{ public_path('images/proex.png') }}" alt="PROEX">
            </td>
        </tr>
        <tr>
            <td colspan="2" style="padding-top:10px;">
                <img src="{{ public_path('images/mulheresmil.jpg') }}" alt="Mulheres Mil">
            </td>
        </tr>
    </table>

    <h3>INSTITUTO FEDERAL DE EDUCAÇÃO, CIÊNCIA E TECNOLOGIA</h3>
    <h4>PROEX – Pró-Reitoria de Extensão</h4>
    <h4>Programa Mulheres Mil – Educação, Cidadania e Desenvolvimento Sustentável</h4>

    <br>

    <h3>BOLSA FORMAÇÃO – PROGRAMA MULHERES MIL IFRS {{ $submission->year }}</h3>
    <h4>REGISTRO DAS HORAS TRABALHADAS</h4>
    <h5>
        Campus {{ $submission->scholarshipHolder->unit->name ?? '—' }} –
        {{ str_pad($submission->month, 2, '0', STR_PAD_LEFT) }}/{{ $submission->year }}
    </h5>

</div>

{{-- =========================
     DADOS DO BOLSISTA
========================= --}}
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

{{-- =========================
     QUADRO DE HORAS
========================= --}}
<h5>2. Quadro das horas e atividades</h5>

<table>
    <thead>
        <tr>
            <th width="15%">Data</th>
            <th width="15%">Horas</th>
            <th>Atividades desenvolvidas</th>
        </tr>
    </thead>
    <tbody>
        @php($totalHoras = 0)

        @foreach($submission->records as $record)
            @php($totalHoras += $record->hours)
            <tr>
                <td>{{ $record->date->format('d/m/Y') }}</td>
                <td>{{ number_format($record->hours, 2) }}</td>
                <td>{{ $record->description ?: '—' }}</td>
            </tr>
        @endforeach

        <tr>
            <td><strong>Total</strong></td>
            <td><strong>{{ number_format($totalHoras, 2) }}</strong></td>
            <td></td>
        </tr>
    </tbody>
</table>

{{-- =========================
     ASSINATURAS
========================= --}}
<h5>3. Assinaturas</h5>

<table class="assinaturas">
    <tr>
        <td>
            _________________________________ <br>
            Coordenação Adjunta
        </td>
        <td>
            _________________________________ <br>
            Coordenação Geral
        </td>
    </tr>
</table>

{{-- =========================
     RODAPÉ
========================= --}}
<div class="rodape">
    Emitido em {{ now()->format('d/m/Y H:i') }}
</div>

</body>
</html>
