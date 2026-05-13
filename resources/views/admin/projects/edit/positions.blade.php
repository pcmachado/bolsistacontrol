@extends('layouts.app')

@section('title', 'Editar Cargos do Projeto')

@section('content')
<div class="container-fluid">
    <h4 class="mb-4 fw-bold">
        Editar Cargos - {{ $project->name }}
    </h4>

    <div class="row">
        <div class="col-md-3">
            @include('admin.projects.edit._nav', ['active' => 'positions'])
        </div>

        <div class="col-md-9">
            <div class="card rounded-0 shadow-sm">
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.projects.edit.positions.update', $project) }}">
                        @csrf
                        @method('PUT')

                        <p class="text-muted mb-3">
                            Selecione os cargos disponiveis neste projeto e informe o valor da hora.
                        </p>

                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 50px;"></th>
                                        <th>Cargo</th>
                                        <th style="width: 220px;">Valor Hora (R$)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($allPositions as $position)
                                        @php
                                            $pivot = $project->positions->firstWhere('id', $position->id)?->pivot;
                                        @endphp
                                        <tr>
                                            <td>
                                                <input type="checkbox"
                                                       class="form-check-input position-check"
                                                       {{ $pivot ? 'checked' : '' }}>
                                            </td>
                                            <td>{{ $position->name }}</td>
                                            <td>
                                                <input type="number"
                                                       step="0.01"
                                                       min="0"
                                                       name="positions[{{ $position->id }}][hourly_rate]"
                                                       class="form-control hourly-input"
                                                       value="{{ old("positions.{$position->id}.hourly_rate", $pivot->hourly_rate ?? '') }}"
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

                        <div class="d-flex justify-content-end mt-4">
                            <button type="submit" class="btn btn-primary">
                                Salvar cargos
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.querySelectorAll('.position-check').forEach(cb => {
    cb.addEventListener('change', function () {
        const input = this.closest('tr').querySelector('.hourly-input');

        input.disabled = !this.checked;

        if (!this.checked) {
            input.value = '';
        }
    });
});
</script>
@endpush
