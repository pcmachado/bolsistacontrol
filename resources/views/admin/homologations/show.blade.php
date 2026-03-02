<form method="POST" action="{{ route('admin.homologations.reject', $submission) }}">
@csrf

<table class="table">
    <thead>
        <tr>
            <th>
                <input type="checkbox" checked disabled>
            </th>
            <th>Data</th>
            <th>Horas</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($submission->records as $record)
            <tr>
                <td>
                    <input type="checkbox"
                           name="records[]"
                           value="{{ $record->id }}"
                           checked>
                </td>
                <td>{{ $record->date->format('d/m/Y') }}</td>
                <td>{{ $record->formattedDuration() }}</td>
                <td>{{ ucfirst($record->status) }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

<textarea name="reason"
          class="form-control mb-3"
          placeholder="Motivo da rejeição (se houver)"></textarea>

<button class="btn btn-danger">Rejeitar selecionados</button>
</form>

<form method="POST"
      action="{{ route('admin.homologations.approve', $submission) }}">
@csrf
<button class="btn btn-success mt-2">
    Aprovar mês inteiro
</button>
</form>
