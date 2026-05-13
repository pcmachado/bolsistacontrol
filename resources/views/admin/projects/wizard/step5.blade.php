@extends('layouts.project-wizard')

@section('title', 'Fontes de Fomento')

@section('wizard-content')

<h4 class="mb-4 fw-bold">Fontes de Fomento</h4>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="card border-primary mb-4">
    <div class="card-header bg-primary-subtle fw-semibold">
        Cadastrar nova forma de fomento
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.funding-sources.store') }}" class="row g-3 align-items-end">
            @csrf
            <input type="hidden" name="redirect_to" value="{{ url()->current() }}">

            <div class="col-md-4">
                <label class="form-label">Nome</label>
                <input type="text" name="name" class="form-control" placeholder="Ex: Edital PROEX" required>
            </div>

            <div class="col-md-2">
                <label class="form-label">Tipo</label>
                <select name="type" class="form-select" required>
                    <option value="external">Externa</option>
                    <option value="internal">Interna</option>
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label">Valor Total</label>
                <input type="number" step="0.01" min="0" name="total_amount" class="form-control" placeholder="Ex: 10000,00">
            </div>

            <div class="col-md-3">
                <button type="submit" class="btn btn-outline-primary w-100">
                    + Cadastrar e continuar
                </button>
            </div>

            <div class="col-12">
                <label class="form-label">Descrição</label>
                <input type="text" name="description" class="form-control" placeholder="Descrição opcional da forma de fomento">
            </div>
        </form>
    </div>
</div>

<form method="POST"
      action="{{ route('admin.projects.store.step5', $project) }}">
    @csrf

    <p class="text-muted mb-3">
        Selecione as fontes de fomento do projeto e informe os valores.
    </p>

    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th style="width:50px;"></th>
                    <th>Fonte de Fomento</th>
                    <th style="width:220px;">Valor (R$)</th>
                </tr>
            </thead>
            <tbody>
                @forelse($fundingSources as $source)
                    @php
                        $pivot = $project->fundingSources
                            ->firstWhere('id', $source->id)?->pivot;
                    @endphp
                    <tr>
                        <td>
                            <input type="checkbox"
                                   name="fundings[{{ $source->id }}][selected]"
                                   value="1"
                                   class="form-check-input funding-check"
                                   data-id="{{ $source->id }}"
                                   {{ old("fundings.{$source->id}.selected", $pivot ? '1' : null) ? 'checked' : '' }}>
                        </td>

                        <td>
                            <strong>{{ $source->name }}</strong><br>
                            @if(!empty($source->description))
                                <small class="text-muted">{{ $source->description }}</small>
                            @endif
                        </td>

                        <td>
                            <input type="number"
                                   step="0.01"
                                   min="0"
                                   name="fundings[{{ $source->id }}][allocated_amount]"
                                   class="form-control amount-input"
                                   value="{{ old("fundings.{$source->id}.allocated_amount", $pivot->allocated_amount ?? '') }}"
                                   {{ old("fundings.{$source->id}.selected", $pivot ? '1' : null) ? '' : 'disabled' }}>

                            <input type="hidden"
                                   name="fundings[{{ $source->id }}][funding_source_id]"
                                   value="{{ $source->id }}">
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="text-center text-muted py-4">
                            Nenhuma forma de fomento cadastrada. Cadastre uma acima para continuar.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Ações --}}
    <div class="d-flex justify-content-between mt-4">
        <a href="{{ route('admin.projects.create.step4', $project) }}"
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
document.querySelectorAll('.funding-check').forEach(cb => {
    cb.addEventListener('change', function () {
        const row = this.closest('tr');

        row.querySelectorAll('.amount-input').forEach(input => {
            input.disabled = !this.checked;
        });
    });
});
</script>
@endpush
