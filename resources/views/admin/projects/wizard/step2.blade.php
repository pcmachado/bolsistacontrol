@extends('layouts.project-wizard')

@section('title', 'Cargos do Projeto')

@section('wizard-content')

<h4 class="mb-4 fw-bold">Definir Cargos</h4>

<form method="POST"
      action="{{ route('admin.projects.store.step2', $project) }}">
    @csrf

    <p class="text-muted mb-3">
        Selecione os cargos que farão parte do projeto e defina o valor da hora.
    </p>

    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th style="width: 50px;"></th>
                    <th>Cargo</th>
                    <th style="width: 200px;">Valor Hora (R$)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($positions as $position)
                    @php
                        $pivot = $project->positions->firstWhere('id', $position->id)?->pivot;
                    @endphp
                    <tr>
                        <td>
                            <input type="checkbox"
                                   class="form-check-input position-check"
                                   data-id="{{ $position->id }}"
                                   {{ $pivot ? 'checked' : '' }}>
                        </td>

                        <td>{{ $position->name }}</td>

                        <td>
                            <input type="number"
                                   step="0.01"
                                   min="0"
                                   name="positions[{{ $position->id }}][hourly_rate]"
                                   class="form-control hourly-input"
                                   value="{{ $pivot->hourly_rate ?? '' }}"
                                   {{ $pivot ? '' : 'disabled' }}>
                            <input type="hidden"
                                   name="positions[{{ $position->id }}][id]"
                                   value="{{ $position->id }}">
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Ações --}}
    <div class="d-flex justify-content-between mt-4">
        <a href="{{ route('admin.projects.create.step1') }}"
           class="btn btn-outline-secondary">
            ← Voltar
        </a>

        <button type="submit" class="btn btn-primary">
            Próximo →
        </button>
    </div>

</form>

@endsection

@push('scripts')
<script>
document.querySelectorAll('.position-check').forEach(cb => {
    cb.addEventListener('change', function () {
        const row = this.closest('tr');
        const input = row.querySelector('.hourly-input');

        input.disabled = !this.checked;

        if (!this.checked) {
            input.value = '';
        }
    });
});
</script>
@endpush
