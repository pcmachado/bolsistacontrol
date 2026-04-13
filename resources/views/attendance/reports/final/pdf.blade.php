<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<style>
    body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
    h3, h4 { text-align: center; margin: 4px 0; }
    table { width: 100%; border-collapse: collapse; margin-top: 10px; }
    td { border: 1px solid #000; padding: 6px; vertical-align: top; }
    .assinaturas td { border: none; padding-top: 40px; text-align: center; }
</style>
</head>
<body>

<h3>INSTITUTO FEDERAL DE EDUCAÇÃO, CIÊNCIA E TECNOLOGIA</h3>
<h4>Relatório Final de Atividades do Bolsista</h4>

<table>
<tr>
    <td><strong>Bolsista</strong></td>
    <td>{{ $report->scholarshipHolder->user->name }}</td>
</tr>
<tr>
    <td><strong>Projeto</strong></td>
    <td>{{ $report->project->name ?? '-' }}</td>
</tr>
</table>

<h4>Atividades desenvolvidas</h4>
<p>{{ nl2br(e($report->activities)) }}</p>

<h4>Resultados alcançados</h4>
<p>{{ nl2br(e($report->results)) }}</p>

<h4>Contribuições</h4>
<p>{{ nl2br(e($report->contributions)) }}</p>

<table class="assinaturas">
<tr>
    <td>_________________________<br>Bolsista</td>
    <td>_________________________<br>Coordenação</td>
</tr>
</table>

</body>
</html>
