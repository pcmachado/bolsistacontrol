@extends('layouts.app')

@section('title', 'Editar Fontes de Fomento')

@section('content')
<div class="container-fluid">
    <h4 class="mb-4 fw-bold">
        Editar Fontes de Fomento - {{ $project->name }}
    </h4>

    <div class="row">
        <div class="col-md-3">
            @include('admin.projects.edit._nav', ['active' => 'funding'])
        </div>

                            <td>
                                <select name="fundings[{{ $loop->index }}][status]"
                                        class="form-select form-select-sm"
                                        {{ $pivot ? '' : 'disabled' }}>
                                    <option value="active"
                                        {{ ($pivot && $pivot->status === 'active') ? 'selected' : '' }}>
                                        Ativo
                                    </option>
                                    <option value="finished"
                                        {{ ($pivot && $pivot->status === 'finished') ? 'selected' : '' }}>
                                        Finalizado
                                    </option>
                                </select>
                            </td>
        <div class="col-md-9">
            <form method="POST" action="{{ route('admin.projects.edit.funding.update', $project) }}">
                @csrf

                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th style="width:50px;"></th>
                                <th>Fonte</th>
                                <th style="width:180px;">Valor</th>
                                <th style="width:140px;">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($fundingSources as $source)
                                @php
                                    $pivot = $project->fundingSources->firstWhere('id', $source->id)?->pivot;
                                @endphp
                                <tr>
                                    <td>
                                        <input type="checkbox" class="form-check-input funding-check" {{ $pivot ? 'checked' : '' }}>
                                    </td>
                                    <td><strong>{{ $source->name }}</strong></td>
                                    <td>
                                        <input type="number" step="0.01" name="fundings[{{ $loop->index }}][allocated_amount]" class="form-control form-control-sm" value="{{ $pivot->allocated_amount ?? '' }}" {{ $pivot ? '' : 'disabled' }}>
                                    </td>
                                    <td>
                                        <select name="fundings[{{ $loop->index }}][status]" class="form-select form-select-sm" {{ $pivot ? '' : 'disabled' }}>
                                            <option value="active" {{ ($pivot && $pivot->status === 'active') ? 'selected' : '' }}>Ativo</option>
                                            <option value="inactive" {{ ($pivot && $pivot->status === 'inactive') ? 'selected' : '' }}>Inativo</option>
                                        </select>
                                    </td>
                                    <input type="hidden" name="fundings[{{ $loop->index }}][funding_source_id]" value="{{ $source->id }}">
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-end mt-4">
                    <button type="submit" class="btn btn-primary">
                        Salvar alteracoes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.querySelectorAll('.funding-check').forEach(cb => {
    cb.addEventListener('change', function () {
        const row = this.closest('tr');

        row.querySelectorAll('input, select').forEach(el => {
            if (el.type !== 'hidden') {
                el.disabled = !this.checked;
            }
        });
    });
});
</script>
@endpush
