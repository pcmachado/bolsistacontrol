<i class="fas fa-php    "></i><table class="table table-striped align-middle mb-0">
    <thead>
        <tr>
            <th>Disciplina</th>
            <th>Professor</th>
            <th>Carga Horária</th>
            <th>Horário</th>
            <th>Sala</th>
            <th class="text-end">Ações</th>
        </tr>
    </thead>

    <tbody>
        @foreach($offering->disciplines as $disc)
            @php $pivot = $disc->pivot; @endphp

            <tr>
                <td>{{ $disc->name }}</td>

                {{-- Professor --}}
                <td>
                    <form method="POST"
                          action="{{ route('admin.class-offerings.disciplines.update', $pivot->id) }}">
                        @csrf
                        @method('PUT')

                        <select name="teacher_id" class="form-select" onchange="this.form.submit()">
                            <option value="">Nenhum</option>
                            @foreach($teachers as $t)
                                <option value="{{ $t->id }}"
                                    {{ $pivot->teacher_id == $t->id ? 'selected' : '' }}>
                                    {{ $t->name }}
                                </option>
                            @endforeach
                        </select>
                    </form>
                </td>

                {{-- Carga horária --}}
                <td>
                    <form method="POST"
                          action="{{ route('admin.class-offerings.disciplines.update', $pivot->id) }}">
                        @csrf
                        @method('PUT')

                        <input type="number" name="workload" class="form-control"
                               value="{{ $pivot->workload }}" min="1" onchange="this.form.submit()">
                    </form>
                </td>

                {{-- Horário --}}
                <td>
                    <form method="POST"
                          action="{{ route('admin.class-offerings.disciplines.update', $pivot->id) }}">
                        @csrf
                        @method('PUT')

                        <input type="text" name="schedule" class="form-control"
                               value="{{ $pivot->schedule }}" onchange="this.form.submit()">
                    </form>
                </td>

                {{-- Sala --}}
                <td>
                    <form method="POST"
                          action="{{ route('admin.class-offerings.disciplines.update', $pivot->id) }}">
                        @csrf
                        @method('PUT')

                        <input type="text" name="room" class="form-control"
                               value="{{ $pivot->room }}" onchange="this.form.submit()">
                    </form>
                </td>

                {{-- Ações --}}
                <td class="text-end">
                    <form action="{{ route('admin.class-offerings.disciplines.destroy', $pivot->id) }}"
                          method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')

                        <button class="btn btn-danger btn-sm"
                                onclick="return confirm('Remover esta disciplina?')">
                            <i class="bi bi-trash"></i>
                        </button>
                    </form>
                </td>

            </tr>
        @endforeach
    </tbody>
</table>
