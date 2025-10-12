<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Relatório Individual - Modelo em Branco</title>
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

    <h3>BOLSA FORMAÇÃO - PROGRAMA MULHERES MIL IFRS {{ now()->year }}</h3>
    <h4>REGISTRO DAS HORAS TRABALHADAS – CAMPUS __________</h4>
    <h5>Mês: ____/____</h5>

    {{-- Dados do bolsista --}}
    <h5>1. Dados do bolsista</h5>
    <table>
        <tr>
            <td><strong>Nome</strong></td>
            <td>________________________________________</td>
        </tr>
        <tr>
            <td><strong>CPF</strong></td>
            <td>________________________________________</td>
        </tr>
        <tr>
            <td><strong>Carga horária prevista</strong></td>
            <td>________ horas mensais</td>
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
            <tr><td>Semana 1</td><td></td><td></td></tr>
            <tr><td>Semana 2</td><td></td><td></td></tr>
            <tr><td>Semana 3</td><td></td><td></td></tr>
            <tr><td>Semana 4</td><td></td><td></td></tr>
            <tr><td><strong>Total Horas</strong></td><td></td><td></td></tr>
        </tbody>
    </table>

    {{-- Assinaturas --}}
    <h5>3. Assinaturas</h5>
    <table class="assinaturas">
        <tr>
            <td>_________________________________<br>Bolsista / Coordenação Adjunta</td>
            <td>_________________________________<br>Coordenação Geral</td>
        </tr>
    </table>

    {{-- Rodapé --}}
    <div class="rodape">
        Emitido em {{ now()->format('d/m/Y H:i') }}
    </div>

</body>
</html>
