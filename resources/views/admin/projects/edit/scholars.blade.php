@extends('layouts.app')

@section('title', 'Bolsistas do Projeto')

@section('content')
<div class="container-fluid">
    <h4 class="mb-4 fw-bold">Vincular Bolsistas ao Projeto</h4>

    <div class="row">
        <div class="col-md-3">
            @include('admin.projects.edit._nav', ['active' => 'scholars'])
        </div>

        <div class="col-md-9">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if ($positions->isEmpty())
                <div class="alert alert-warning">
                    Cadastre primeiro os cargos deste projeto para liberar a vinculacao de bolsistas.
                    <a href="{{ route('admin.projects.edit.positions', $project) }}" class="alert-link">Abrir etapa de cargos</a>
                </div>
            @endif

            <form method="POST" action="{{ route('admin.projects.edit.scholars.update', $project) }}">
                @csrf
                @method('PUT')

                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="mb-3">Adicionar Bolsista</h5>

                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="holder-select" class="form-label">Bolsista</label>
                                <select id="holder-select" class="form-select" {{ $positions->isEmpty() ? 'disabled' : '' }}></select>
                                <small class="text-muted">Digite pelo menos 2 caracteres para buscar.</small>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">Cargo</label>
                                <select id="position_id" class="form-select" {{ $positions->isEmpty() ? 'disabled' : '' }}>
                                    @foreach ($positions as $p)
                                        <option value="{{ $p->id }}">{{ $p->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-2">
                                <label class="form-label">Carga Horaria Semanal</label>
                                <input type="number" id="weekly_workload" class="form-control" {{ $positions->isEmpty() ? 'disabled' : '' }}>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">Inicio</label>
                                <input type="date" id="start_date" class="form-control" {{ $positions->isEmpty() ? 'disabled' : '' }}>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">Fim</label>
                                <input type="date" id="end_date" class="form-control" {{ $positions->isEmpty() ? 'disabled' : '' }}>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">Edital/Portaria</label>
                                <input type="text" id="edital_portaria" class="form-control" {{ $positions->isEmpty() ? 'disabled' : '' }}>
                            </div>

                            <div class="col-md-2 d-flex align-items-end">
                                <button type="button" id="addScholar" class="btn btn-success w-100" {{ $positions->isEmpty() ? 'disabled' : '' }}>
                                    Adicionar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <h5 class="mb-3">Bolsistas vinculados</h5>

                        <table class="table table-striped" id="scholarTable">
                            <thead>
                                <tr>
                                    <th>Bolsista</th>
                                    <th>Cargo</th>
                                    <th>Carga Horaria Semanal</th>
                                    <th>Edital/Portaria</th>
                                    <th>Inicio</th>
                                    <th>Fim</th>
                                    <th>Status</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($project->scholarshipHolders as $i => $holder)
                                    <tr>
                                        <td>
                                            {{ $holder->user->name }}
                                            <input type="hidden" name="scholarships[{{ $i }}][scholarship_holder_id]" value="{{ $holder->id }}">
                                        </td>
                                        <td>
                                            <select name="scholarships[{{ $i }}][position_id]" class="form-select">
                                                @foreach ($positions as $p)
                                                    <option value="{{ $p->id }}" @selected($holder->pivot->position_id == $p->id)>
                                                        {{ $p->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <input type="number" name="scholarships[{{ $i }}][weekly_workload]" value="{{ $holder->pivot->weekly_workload }}" class="form-control">
                                        </td>
                                        <td>
                                            <input type="text" name="scholarships[{{ $i }}][edital_portaria]" value="{{ $holder->pivot->edital_portaria }}" class="form-control">
                                        </td>
                                        <td>
                                            <input type="date" name="scholarships[{{ $i }}][start_date]" value="{{ $holder->pivot->start_date }}" class="form-control">
                                        </td>
                                        <td>
                                            <input type="date" name="scholarships[{{ $i }}][end_date]" value="{{ $holder->pivot->end_date }}" class="form-control">
                                        </td>
                                        <td>
                                            <select name="scholarships[{{ $i }}][status]" class="form-select">
                                                <option value="active" @selected($holder->pivot->status == 'active')>Ativo</option>
                                                <option value="inactive" @selected($holder->pivot->status == 'inactive')>Inativo</option>
                                            </select>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-danger remove-row">Remover</button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="mt-4 text-end">
                    <button class="btn btn-primary">Salvar alteracoes</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const addButton = document.getElementById('addScholar');

    if (!addButton || addButton.disabled) {
        return;
    }

    let index = {{ count($project->scholarshipHolders) }};

    const holderSelect = new TomSelect('#holder-select', {
        valueField: 'id',
        labelField: 'text',
        searchField: 'text',
        placeholder: 'Digite nome, CPF ou e-mail...',
        maxOptions: 20,
        loadThrottle: 300,
        load(query, callback) {
            if (query.length < 2) {
                callback();
                return;
            }

            fetch(`{{ route('admin.scholarship-holders.search') }}?q=${encodeURIComponent(query)}`)
                .then(response => response.json())
                .then(json => callback(json.results))
                .catch(() => callback());
        },
        render: {
            option(item, escape) {
                return `<div>${escape(item.text)}</div>`;
            }
        }
    });

    addButton.addEventListener('click', () => {
        const holderId = holderSelect.getValue();

        if (!holderId) {
            alert('Selecione um bolsista');
            return;
        }

        const holder = holderSelect.options[holderId];

        const exists = [...document.querySelectorAll('input[name*="[scholarship_holder_id]"]')]
            .some(input => input.value == holderId);

        if (exists) {
            alert('Este bolsista ja foi adicionado.');
            return;
        }

        const row = `
            <tr>
                <td>
                    ${holder.text}
                    <input type="hidden" name="scholarships[${index}][scholarship_holder_id]" value="${holderId}">
                </td>
                <td>
                    <input type="hidden" name="scholarships[${index}][position_id]" value="${document.getElementById('position_id').value}">
                    ${document.querySelector('#position_id option:checked').text}
                </td>
                <td>
                    <input type="number" name="scholarships[${index}][weekly_workload]" value="${document.getElementById('weekly_workload').value}" class="form-control">
                </td>
                <td>
                    <input type="text" name="scholarships[${index}][edital_portaria]" value="${document.getElementById('edital_portaria').value}" class="form-control">
                </td>
                <td>
                    <input type="date" name="scholarships[${index}][start_date]" value="${document.getElementById('start_date').value}" class="form-control">
                </td>
                <td>
                    <input type="date" name="scholarships[${index}][end_date]" value="${document.getElementById('end_date').value}" class="form-control">
                </td>
                <td>
                    <select name="scholarships[${index}][status]" class="form-select">
                        <option value="active">Ativo</option>
                        <option value="inactive">Inativo</option>
                    </select>
                </td>
                <td>
                    <button type="button" class="btn btn-danger btn-sm remove-row">Remover</button>
                </td>
            </tr>
        `;

        document.querySelector('#scholarTable tbody').insertAdjacentHTML('beforeend', row);
        holderSelect.clear();
        index++;
    });

    document.addEventListener('click', function (e) {
        if (e.target.classList.contains('remove-row')) {
            e.target.closest('tr').remove();
        }
    });
});
</script>
@endpush
