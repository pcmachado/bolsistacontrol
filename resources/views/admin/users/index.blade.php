@extends('layouts.app')

@section('title', 'Gestão de Usuários')

@section('content')
<div class="container-fluid">
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-2 mb-3">
        <h1 class="mb-0 text-black">Gerenciamento de Usuários</h1>

        <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-2"></i> Criar Novo Usuário
        </a>
    </div>

    <div class="card mb-3 shadow-sm text-body">
        <div class="card-body">
            <form method="GET" id="user-filters-form" class="row g-2 align-items-end">
                <div class="col-lg-4 col-md-6">
                    <label for="user-filter-name" class="form-label fw-semibold">Nome</label>
                    <input
                        type="search"
                        id="user-filter-name"
                        name="filter_name"
                        value="{{ request('filter_name') }}"
                        class="form-control"
                        placeholder="Digite nome ou e-mail"
                        list="user-name-suggestions"
                        autocomplete="off">
                    <datalist id="user-name-suggestions"></datalist>
                </div>

                <div class="col-lg-3 col-md-6">
                    <label for="user-filter-unit" class="form-label fw-semibold">Unidade</label>
                    <select name="filter_unit" id="user-filter-unit" class="form-select">
                        <option value="">Todas as unidades</option>
                        @foreach($units as $id => $name)
                            <option value="{{ $id }}" @selected((string) request('filter_unit') === (string) $id)>
                                {{ $name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-lg-3 col-md-6">
                    <label for="user-filter-role" class="form-label fw-semibold">Cargo / Papel</label>
                    <select name="filter_role" id="user-filter-role" class="form-select">
                        <option value="">Todos os papéis</option>
                        @foreach($roles as $value => $label)
                            <option value="{{ $value }}" @selected((string) request('filter_role') === (string) $value)>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-lg-2 col-md-6">
                    <label for="user-page-length" class="form-label fw-semibold">Itens por página</label>
                    <select name="page_length" id="user-page-length" class="form-select">
                        @foreach([10, 25, 50, 100] as $pageSize)
                            <option value="{{ $pageSize }}" @selected((int) request('page_length', 25) === $pageSize)>
                                {{ $pageSize }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-12 d-flex flex-wrap gap-2 mt-2">
                    <button type="submit" class="btn btn-primary">Filtrar</button>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">Limpar</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm rounded-4 border-0 overflow-hidden">
        <div class="table-responsive">
                {!! $dataTable->table(['class' => 'table table-hover align-middle table-striped w-100 mb-0'], true) !!}
            </div>
    </div>
</div>

@push('scripts')
    {!! $dataTable->scripts() !!}

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('user-filters-form');
            const pageLength = document.getElementById('user-page-length');
            const nameInput = document.getElementById('user-filter-name');
            const suggestionList = document.getElementById('user-name-suggestions');
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
                    const url = new URL(@json(url('/api/users/search')), window.location.origin);
                    url.searchParams.set('q', query);

                    fetch(url.toString(), {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                        },
                    })
                        .then(response => response.ok ? response.json() : [])
                        .then(data => {
                            suggestionList.innerHTML = '';

                            (data || []).forEach(item => {
                                const option = document.createElement('option');
                                option.value = item.name;
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
