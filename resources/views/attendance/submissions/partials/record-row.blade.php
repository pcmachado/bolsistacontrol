<tr>
    <td>
        <input type="checkbox"
               name="records[]"
               value="{{ $record->id }}"
               checked>
    </td>

    <td>{{ $record->date->format('d/m/Y') }}</td>

    <td>{{ $record->hours }} h</td>

    <td class="text-muted small">
        {{ $record->description ?? '-' }}
    </td>

    <td>
        <span class="badge bg-secondary">
            {{ ucfirst($record->status) }}
        </span>
    </td>
</tr>
