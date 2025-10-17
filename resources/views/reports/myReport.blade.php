<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Relatório Individual - {{ $holder->user->name }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        h3, h4, h5 { margin: 5px 0; text-align: center; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        th, td { border: 1px solid #000; padding: 6px; text-align: left; }
        th { background: #f0f0f0; }
        .assinaturas { margin-top: 40px; width: 100%; }
        .assinaturas td { border: none; text-align: center; padding-top: 40px; }
        .rodape { font-size: 10px; text-align: right; margin-top: 20px; }
    </style>
</head>
<body>

    {{-- Cabeçalho institucional --}}
    <h3>INSTITUTO FEDERAL DE EDUCAÇÃO, CIÊNCIA E TECNOLOGIA</h3>
    <h4>PROEX – Pró-Reitoria de Extensão</h4>
    <h4>Programa Mulheres Mil – Educação, Cidadania e Desenvolvimento Sustentável</h4>

    <h3>BOLSA FORMAÇÃO - PROGRAMA MULHERES MIL IFRS {{ $year }}</h3>
    <h4>REGISTRO DAS HORAS TRABALHADAS – CAMPUS {{ $holder->unit->name ?? '---' }}</h4>
    <h5>Mês: {{ str_pad($month, 2, '0', STR_PAD_LEFT) }}/{{ $year }}</h5>

    {{-- Dados do bolsista --}}
    <h5>1. Dados do bolsista</h5>
    <table>
        <tr>
            <td><strong>Nome</strong></td>
            <td>{{ $holder->user->name }}</td>
        </tr>
        <tr>
            <td><strong>CPF</strong></td>
            <td>{{ $holder->cpf }}</td>
        </tr>
        <tr>
            <td><strong>Carga horária prevista</strong></td>
            <td>{{ $holder->weekly_hour_limit / 60 * 4 ?? '-' }} horas mensais</td>
        </tr>
    </table>

    {{-- Quadro das horas --}}
    <h5>2. Quadro das horas e atividades</h5>
    <table>
        <thead>
            <tr>
                <th>Período (semana)</th>
                <th>Horas cumpridas</th>
                <th>Atividades desenvolvidas</th>
            </tr>
        </thead>
        <tbody>
            @foreach($semanas as $semana)
            <tr>
                <td>Semana {{ $semana['semana'] }}</td>
                <td>{{ $semana['horas'] }}</td>
                <td>{{ $semana['atividades'] }}</td>
            </tr>
            @endforeach
            <tr>
                <td><strong>Total Horas</strong></td>
                <td><strong>{{ $totalHoras }}</strong></td>
                <td></td>
            </tr>
        </tbody>
    </table>

    {{-- Assinaturas --}}
    <h5>3. Assinaturas</h5>
    <table class="assinaturas">
        <tr>
            <td>_________________________________<br>Coordenação Adjunta</td>
            <td>_________________________________<br>Coordenação Geral</td>
        </tr>
    </table>

    {{-- Rodapé --}}
    <div class="rodape">
        Emitido por {{ $holder->user->name }} em {{ now()->format('d/m/Y H:i') }}
    </div>

</body>
</html>
