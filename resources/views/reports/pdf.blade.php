<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>{{ $titulo }}</title>
    <style>
        body { font-family: sans-serif; }
        h1, h2 { text-align: center; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h1>{{ $titulo }}</h1>
    <h2>Período: {{ $periodo }}</h2>
    <table>
        <thead>
            <tr>
                <th>Nome do Bolsista</th>
                <th>Total de Horas Trabalhadas</th>
            </tr>
        </thead>
        <tbody>
            @foreach($resumo as $item)
            <tr>
                <td>{{ $item['bolsista'] }}</td>
                <td>{{ $item['total_horas'] }}h</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>