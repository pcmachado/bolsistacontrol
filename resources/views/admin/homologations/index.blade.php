<h3>Homologações Pendentes</h3>

<table class="table table-hover">
    <thead>
        <tr>
            <th>Bolsista</th>
            <th>Unidade</th>
            <th>Mês</th>
            <th>Horas</th>
            <th>Ações</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($submissions as $submission)
            <tr>
                <td>{{ $submission->scholarshipHolder->user->name }}</td>
                <td>{{ $submission->scholarshipHolder->unit->name }}</td>
                <td>{{ $submission->month }}/{{ $submission->year }}</td>
                <td>{{ $submission->total_hours }}</td>
                <td>
                    <a href="{{ route('admin.homologations.show', $submission) }}"
                       class="btn btn-sm btn-primary">
                        Analisar
                    </a>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
