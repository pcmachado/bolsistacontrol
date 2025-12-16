<h2>Relatório de Aulas – Turma {{ $offering->name }}</h2>

<p><strong>Total de Horas:</strong> {{ number_format($totalHours, 2) }}</p>

<h3>Horas por Disciplina</h3>
<table width="100%" border="1" cellspacing="0" cellpadding="5">
    <tr>
        <th>Disciplina</th>
        <th>Aulas</th>
        <th>Horas</th>
    </tr>
    @foreach($hoursByDiscipline as $row)
        <tr>
            <td>{{ $row['discipline'] }}</td>
            <td>{{ $row['count'] }}</td>
            <td>{{ number_format($row['hours'], 2) }}</td>
        </tr>
    @endforeach
</table>

<h3>Horas por Professor</h3>
<table width="100%" border="1" cellspacing="0" cellpadding="5">
    <tr>
        <th>Professor</th>
        <th>Aulas</th>
        <th>Horas</th>
    </tr>
    @foreach($hoursByTeacher as $row)
        <tr>
            <td>{{ $row['teacher'] }}</td>
            <td>{{ $row['count'] }}</td>
            <td>{{ number_format($row['hours'], 2) }}</td>
        </tr>
    @endforeach
</table>

<h3>Aulas Registradas</h3>
<table width="100%" border="1" cellspacing="0" cellpadding="5">
    <tr>
        <th>Data</th>
        <th>Disciplina</th>
        <th>Professor</th>
        <th>Início</th>
        <th>Fim</th>
        <th>Horas</th>
    </tr>
    @foreach($sessions as $s)
        <tr>
            <td>{{ $s->date->format('d/m/Y') }}</td>
            <td>{{ $s->discipline->name }}</td>
            <td>{{ $s->teacher->name }}</td>
            <td>{{ $s->start_time }}</td>
            <td>{{ $s->end_time }}</td>
            <td>{{ $s->duration_hours }}</td>
        </tr>
    @endforeach
</table>
