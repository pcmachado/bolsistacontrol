@extends('layouts.app')

@section('title', 'Bolsistas do Projeto')

@section('content')
<h4 class="mb-4 fw-bold">Vincular Bolsistas</h4>

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

    <p class="text-muted mb-3">
        Selecione os bolsistas, defina o cargo, a carga horária semanal e o status.
    </p>

    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th style="width:50px;"></th>
                    <th>Bolsista</th>
                    <th style="width:220px;">Cargo</th>
                    <th style="width:160px;">Carga Horária</th>
                    <th style="width:120px;">Status</th>
                </tr>
            </thead>
            <tbody>

                @foreach ($scholarshipHolders as $i => $holder)
                    @php
                        $pivot = $project->scholarshipHolders
                            ->firstWhere('id', $holder->id)?->pivot;
                    @endphp

                    <tr>
                        {{-- Checkbox --}}
                        <td>
                            <input type="checkbox"
                                   class="form-check-input holder-check"
                                   data-index="{{ $i }}"
                                   {{ $pivot ? 'checked' : '' }}>
                        </td>

                        {{-- Bolsista --}}
                        <td>
                            <strong>{{ $holder->user->name }}</strong><br>
                            <small class="text-muted">{{ $holder->registration }}</small>
                        </td>

                        {{-- Campos controlados --}}
                        <td colspan="3">
                            <div id="holder-{{ $i }}"
                                 class="row g-2 align-items-center {{ $pivot ? '' : 'd-none' }}">

                                {{-- ID do bolsista --}}
                                <div id="holder-{{ $i }}" class="holder-fields d-none">
                                    <input type="hidden"
                                        name="scholarships[{{ $i }}][scholarship_holder_id]"
                                        value="{{ $holder->id }}">
                                    ...
                                </div>

                                {{-- Cargo --}}
                                <div class="col-md-4">
                                    <select name="scholarships[{{ $i }}][position_id]"
                                            class="form-select form-select-sm"
                                            {{ $pivot ? '' : 'disabled' }}>
                                        <option value="">Selecione o cargo</option>
                                        @foreach ($positions as $position)
                                            <option value="{{ $position->id }}"
                                                {{ ($pivot && $pivot->position_id == $position->id) ? 'selected' : '' }}>
                                                {{ $position->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- Carga horária --}}
                                <div class="col-md-4">
                                    <input type="number"
                                           min="1"
                                           max="40"
                                           name="scholarships[{{ $i }}][weekly_workload]"
                                           class="form-control form-control-sm"
                                           placeholder="Horas/semana"
                                           value="{{ $pivot->weekly_workload ?? '' }}"
                                           {{ $pivot ? '' : 'disabled' }}>
                                </div>

                                {{-- Status --}}
                                <div class="col-md-4">
                                    <select name="scholarships[{{ $i }}][status]"
                                            class="form-select form-select-sm"
                                            {{ $pivot ? '' : 'disabled' }}>
                                        <option value="active"
                                            {{ ($pivot && $pivot->status === 'active') ? 'selected' : '' }}>
                                            Ativo
                                        </option>
                                        <option value="inactive"
                                            {{ ($pivot && $pivot->status === 'inactive') ? 'selected' : '' }}>
                                            Inativo
                                        </option>
                                    </select>
                                </div>

                            </div>
                        </td>
                    </tr>
                @endforeach

            </tbody>
        </table>
    </div>

    {{-- Ações --}}
    <div class="d-flex justify-content-end mt-4">
        <button type="submit" class="btn btn-primary">
            Salvar alterações
        </button>
    </div>

</form>

@endsection

@push('scripts')
<script>
document.querySelectorAll('.holder-check').forEach(cb => {
    cb.addEventListener('change', function () {
        const container = document.getElementById('holder-' + this.dataset.index);
        container.classList.toggle('d-none', !this.checked);

        container.querySelectorAll('input, select').forEach(el => {
            el.disabled = !this.checked;
        });
    });
});
</script>
@endpush
