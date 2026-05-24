@extends('layouts.app')

@section('title', 'Gestão de Bolsistas')

@section('content')
<div class="container-fluid">
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-2 mb-3">
        <h1 class="mb-0 text-black">Gerenciamento de Bolsistas</h1>

        <a href="{{ route('admin.scholarship_holders.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-2"></i> Criar Novo Bolsista
        </a>
    </div>

    <div class="card mb-3 shadow-sm text-body">
        <div class="card-body">
            <form method="GET" id="holder-filters-form" class="row g-2 align-items-end">
                <div class="col-lg-4 col-md-6">
                    <label for="holder-filter-name" class="form-label fw-semibold">Nome</label>
                    <input
                        type="search"
                        id="holder-filter-name"
                        name="filter_name"
                        value="{{ request('filter_name') }}"
                        class="form-control"
                        placeholder="Digite nome, CPF ou e-mail"
                        list="holder-name-suggestions"
                        autocomplete="off">
                    <datalist id="holder-name-suggestions"></datalist>
                </div>

                <div class="col-lg-3 col-md-6">
                    <label for="holder-filter-unit" class="form-label fw-semibold">Unidade</label>
                    <select name="filter_unit" id="holder-filter-unit" class="form-select">
                        <option value="">Todas as unidades</option>
                        @foreach($units as $id => $name)
                            <option value="{{ $id }}" @selected((string) request('filter_unit') === (string) $id)>
                                {{ $name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-lg-3 col-md-6">
                    <label for="holder-filter-position" class="form-label fw-semibold">Cargo</label>
                    <select name="filter_position" id="holder-filter-position" class="form-select">
                        <option value="">Todos os cargos</option>
                        @foreach($positions as $id => $name)
                            <option value="{{ $id }}" @selected((string) request('filter_position') === (string) $id)>
                                {{ $name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-lg-2 col-md-6">
                    <label for="holder-page-length" class="form-label fw-semibold">Itens por página</label>
                    <select name="page_length" id="holder-page-length" class="form-select">
                        @foreach([10, 25, 50, 100] as $pageSize)
                            <option value="{{ $pageSize }}" @selected((int) request('page_length', 25) === $pageSize)>
                                {{ $pageSize }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-12 d-flex flex-wrap gap-2 mt-2">
                    <button type="submit" class="btn btn-primary">Filtrar</button>
                    <a href="{{ route('admin.scholarship_holders.index') }}" class="btn btn-outline-secondary">Limpar</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
                {!! $dataTable->table(['class' => 'table table-hover align-middle table-striped'], true) !!}
            </div>
    </div>
</div>

@push('scripts')
    {!! $dataTable->scripts() !!}

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('holder-filters-form');
            const pageLength = document.getElementById('holder-page-length');
            const nameInput = document.getElementById('holder-filter-name');
            const suggestionList = document.getElementById('holder-name-suggestions');
            let debounceTimer;

            pageLength.addEventListener('change', function () {
                form.submit();
            });

            nameInput.addEventListener('input', function () {
                clearTimeout(debounceTimer);

                const query = this.value.trim();

                if (query.length < 2) {
                    suggestionList.innerHTML = '';
                    return;
                }

                debounceTimer = setTimeout(function () {
                    const url = new URL(@json(route('admin.scholarship-holders.search')), window.location.origin);
                    url.searchParams.set('q', query);
                    url.searchParams.set('include_inactive', '1');

                    fetch(url.toString(), {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                        },
                    })
                        .then(response => response.ok ? response.json() : { results: [] })
                        .then(data => {
                            suggestionList.innerHTML = '';

                            (data.results || []).forEach(item => {
                                const option = document.createElement('option');
                                option.value = item.text;
                                suggestionList.appendChild(option);
                            });
                        })
                        .catch(() => {
                            suggestionList.innerHTML = '';
                        });
                }, 250);
            });
        });
    </script>
@endpush
@endsection
