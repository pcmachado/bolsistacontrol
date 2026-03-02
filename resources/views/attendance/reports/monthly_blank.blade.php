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
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 14px;
        }

        th, td {
            border: 1px solid #000;
            padding: 6px;
        }

        th {
            background: #f0f0f0;
        }

        .assinaturas td {
            border: none;
            text-align: center;
            padding-top: 45px;
        }
    </style>
</head>
<body>

<h3 style="text-align:center;">RELATÓRIO MENSAL DE FREQUÊNCIA</h3>

<table>
    <tr>
        <th width="30%">Nome do Bolsista</th>
        <td></td>
    </tr>
    <tr>
        <th>Projeto</th>
        <td></td>
    </tr>
    <tr>
        <th>Mês/Ano</th>
        <td></td>
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
            <td height="22"></td>
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

</body>
</html>
