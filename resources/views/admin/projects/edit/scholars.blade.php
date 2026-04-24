@extends('layouts.app')

@section('title', 'Bolsistas do Projeto')

@section('content')
<h4 class="mb-4 fw-bold">Vincular Bolsistas ao Projeto</h4>

@if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form method="POST"
      action="{{ route('admin.projects.edit.scholars.update', $project) }}">
    @csrf
    @method('PUT')

    {{-- ========================
        ADICIONAR NOVO
    ======================== --}}
    <div class="card mb-4">
        <div class="card-body">

            <h5 class="mb-3">Adicionar Bolsista</h5>

            <div class="row g-3">

                <div class="col-md-4">
                    <label>Bolsista</label>
                    <select id="holder-select" class="form-control"></select>
                </div>

                <div class="col-md-3">
                    <label>Cargo</label>
                    <select id="position_id" class="form-select">
                        @foreach ($positions as $p)
                            <option value="{{ $p->id }}">{{ $p->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <label>Carga</label>
                    <input type="number" id="weekly_workload" class="form-control">
                </div>

                <div class="col-md-3">
                    <label>Início</label>
                    <input type="date" id="start_date" class="form-control">
                </div>

                <div class="col-md-3">
                    <label>Fim</label>
                    <input type="date" id="end_date" class="form-control">
                </div>

                <div class="col-md-3">
                    <label>Edital</label>
                    <input type="text" id="edital_portaria" class="form-control">
                </div>

                <div class="col-md-2 d-flex align-items-end">
                    <button type="button" id="addScholar" class="btn btn-success w-100">
                        ➕ Adicionar
                    </button>
                </div>

            </div>
        </div>
    </div>

    {{-- ========================
        LISTA
    ======================== --}}
    <div class="card">
        <div class="card-body">

            <h5 class="mb-3">Bolsistas vinculados</h5>

            <table class="table table-striped" id="scholarTable">
                <thead>
                    <tr>
                        <th>Bolsista</th>
                        <th>Cargo</th>
                        <th>Carga Horária Semanal</th>
                        <th>Edital/Portaria</th>
                        <th>Início</th>
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
                                <input type="hidden"
                                       name="scholarships[{{ $i }}][scholarship_holder_id]"
                                       value="{{ $holder->id }}">
                            </td>

                            <td>
                                <select name="scholarships[{{ $i }}][position_id]" class="form-select">
                                    @foreach ($positions as $p)
                                        <option value="{{ $p->id }}"
                                            @selected($holder->pivot->position_id == $p->id)>
                                            {{ $p->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </td>

                            <td>
                                <input type="number"
                                       name="scholarships[{{ $i }}][weekly_workload]"
                                       value="{{ $holder->pivot->weekly_workload }}"
                                       class="form-control">
                            </td>

                            <td>
                                <input type="text"
                                       name="scholarships[{{ $i }}][edital_portaria]"
                                       value="{{ $holder->pivot->edital_portaria }}"
                                       class="form-control">
                            </td>

                            <td>
                                <input type="date"
                                       name="scholarships[{{ $i }}][start_date]"
                                       value="{{ $holder->pivot->start_date }}"
                                       class="form-control">
                            </td>

                            <td>
                                <input type="date"
                                       name="scholarships[{{ $i }}][end_date]"
                                       value="{{ $holder->pivot->end_date }}"
                                       class="form-control">
                            </td>

                            <td>
                                <select name="scholarships[{{ $i }}][status]" class="form-select">
                                    <option value="active" @selected($holder->pivot->status == 'active')>Ativo</option>
                                    <option value="inactive" @selected($holder->pivot->status == 'inactive')>Inativo</option>
                                </select>
                            </td>

                            <td>
                                <button type="button" class="btn btn-sm btn-danger remove-row">
                                    🗑
                                </button>
                            </td>

                        </tr>
                    @endforeach

                </tbody>
            </table>

        </div>
    </div>

    <div class="mt-4 text-end">
        <button class="btn btn-primary">
            💾 Salvar alterações
        </button>
    </div>

</form>
@endsection

@push('scripts')
<script>
let index = {{ count($project->scholarshipHolders) }};

$('#addScholar').click(function () {

    let holder = $('#holder-select').select2('data')[0];

    if (!holder) return alert('Selecione um bolsista');

    let row = `
        <tr>
            <td>
                ${holder.text}
                <input type="hidden" name="scholarships[${index}][scholarship_holder_id]" value="${holder.id}">
            </td>

            <td>
                <input type="hidden" name="scholarships[${index}][position_id]" value="${$('#position_id').val()}">
                ${$('#position_id option:selected').text()}
            </td>

            <td>
                <input type="number" name="scholarships[${index}][weekly_workload]" value="${$('#weekly_workload').val()}" class="form-control">
            </td>

            <td>
                <input type="date" name="scholarships[${index}][start_date]" value="${$('#start_date').val()}" class="form-control">
            </td>

            <td>
                <input type="date" name="scholarships[${index}][end_date]" value="${$('#end_date').val()}" class="form-control">
            </td>

            <td>
                <select name="scholarships[${index}][status]" class="form-select">
                    <option value="active">Ativo</option>
                    <option value="inactive">Inativo</option>
                </select>
            </td>

            <td>
                <button type="button" class="btn btn-danger btn-sm remove-row">🗑</button>
            </td>
        </tr>
    `;

    $('#scholarTable tbody').append(row);
    index++;
});

$(document).on('click', '.remove-row', function () {
    $(this).closest('tr').remove();
});
</script>
@endpush