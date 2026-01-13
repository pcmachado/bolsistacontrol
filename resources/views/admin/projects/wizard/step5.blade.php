@extends('layouts.project-wizard')

@section('title', 'Fontes de Fomento')

@section('wizard-content')

<h4 class="mb-4 fw-bold">Fontes de Fomento</h4>

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
                @foreach($fundingSources as $source)
                    @php
                        $pivot = $project->fundingSources
                            ->firstWhere('id', $source->id)?->pivot;
                    @endphp
                    <tr>
                        <td>
                            <input type="checkbox"
                                   class="form-check-input funding-check"
                                   data-id="{{ $source->id }}"
                                   {{ $pivot ? 'checked' : '' }}>
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
                                   name="fundings[{{ $source->id }}][amount]"
                                   class="form-control amount-input"
                                   value="{{ $pivot->amount ?? '' }}"
                                   {{ $pivot ? '' : 'disabled' }}>

                            <input type="hidden"
                                   name="fundings[{{ $source->id }}][funding_source_id]"
                                   value="{{ $source->id }}">
                        </td>
                    </tr>
                @endforeach
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
        const container = document.getElementById('funding-' + this.dataset.index);

        container.classList.toggle('d-none', !this.checked);

        container.querySelectorAll('input, select').forEach(el => {
            el.disabled = !this.checked;
        });
    });
});
</script>
@endpush
