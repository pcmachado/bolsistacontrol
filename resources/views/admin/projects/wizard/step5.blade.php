@extends('layouts.app')

    <style>
        .nav-pills .nav-link.completed {
            background-color: #198754; /* verde */
            color: #fff;
        }
    </style>

@section('content')
    <div class="container">
        <h3>Passo 5: Fontes de Fomento</h3>
        @include('admin.projects.partials._steps', ['step' => 5, 'project' => $project ?? null])
        @include('admin.projects.partials._progress', ['progress' => 80, 'label' => 'Passo 5 de 6'])

        <form method="POST" action="{{ route('admin.projects.store.step5', $project) }}">
            @csrf
            <div class="mb-3">
                <label for="fundings" class="form-label">Fontes de Fomento</label>
                <div class="input-group">
                    <select name="fundings[0][funding_source_id]" class="form-select">
                        @foreach($fundingSources as $source)
                            <option value="{{ $source->id }}">{{ $source->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="input-group mt-2" id="fundings-wrapper">
                    <span class="input-group-text">R$</span>
                    <input type="number" name="fundings[0][amount]" class="form-control" placeholder="Valor">
                </div>
                <div class="mt-3 d-flex justify-content-between">
                    <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#addFundingModal">
                        + Novo
                    </button>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Finalizar</button>
        </form>
    </div>

    {{-- Modal para adicionar nova fonte de fomento --}}
    <x-modal-add-generic 
        id="addFundingModal"
        title="Adicionar Fonte de Fomento"
        :route="route('admin.funding_sources.store')"
        :fields="[
            ['name' => 'name', 'label' => 'Nome da Fonte', 'type' => 'text', 'required' => true],
            ['name' => 'description', 'label' => 'Descrição', 'type' => 'textarea']
        ]"
    />
@endsection

@push('scripts')
<script>
    document.getElementById('addFundingRow').addEventListener('click', function() {
        const wrapper = document.getElementById('fundings-wrapper');
        const index = wrapper.querySelectorAll('.funding-row').length;
        const row = document.createElement('div');
        row.classList.add('row','mb-2','funding-row');
        row.innerHTML = `
            <div class="col-md-6">
                <select name="fundings[${index}][funding_source_id]" class="form-select">
                    @foreach($fundingSources as $source)
                        <option value="{{ $source->id }}">{{ $source->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <input type="number" name="fundings[${index}][amount]" class="form-control" placeholder="Valor">
            </div>
        `;
        wrapper.appendChild(row);
    });
</script>
@endpush