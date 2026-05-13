@extends('layouts.app')

@section('title', 'Disciplinas do Curso')

@section('content')
<div class="container-fluid">
    <div class="mb-4">
        <h4 class="fw-bold mb-1">Disciplinas do Curso</h4>
        <div class="text-muted">
            Curso selecionado: <strong>{{ $course->name }}</strong>
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <p class="mb-0 text-muted">Busque disciplinas por nome ou código e adicione-as à lista abaixo.</p>
        </div>
        <a href="{{ route('admin.disciplines.create') }}" class="btn btn-sm btn-outline-primary">
            Nova Disciplina
        </a>
    </div>

    <form method="POST" action="{{ route('admin.courses.disciplines.store', $course) }}">
        @csrf

        <div class="mb-4">
            <label for="discipline-search" class="form-label fw-semibold">Buscar disciplina</label>
            <input
                id="discipline-search"
                type="text"
                class="form-control"
                placeholder="Digite nome ou código"
                autocomplete="off"
            >
        </div>

        <div class="mb-4">
            <label class="form-label fw-semibold">Disciplinas selecionadas</label>
            <div id="selected-disciplines" class="list-group"></div>
        </div>

        <div class="mb-4">
            <label class="form-label fw-semibold">Sugestões</label>
            <div id="discipline-suggestions" class="list-group"></div>
        </div>

        <div class="d-flex justify-content-end">
            <button type="submit" class="btn btn-primary">
                Salvar alterações
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
    const disciplines = @json($disciplinesJson);

    const selected = new Map(
        Object.entries(
            @json($selectedDisciplinesJson)
        )
    );

    const searchInput = document.getElementById('discipline-search');
    const suggestionsElement = document.getElementById('discipline-suggestions');
    const selectedElement = document.getElementById('selected-disciplines');

    function renderSelected() {
        selectedElement.innerHTML = '';

        if (selected.size === 0) {
            selectedElement.innerHTML = '<div class="text-muted">Nenhuma disciplina selecionada.</div>';
            return;
        }

        selected.forEach((discipline) => {
            const button = document.createElement('button');
            button.type = 'button';
            button.className = 'list-group-item list-group-item-action d-flex justify-content-between align-items-center';
            button.innerHTML = `
                <div>
                    <strong>${discipline.name}</strong>
                    <div class="small text-muted">${discipline.code || 'Sem código'}</div>
                </div>
                <span class="badge bg-${discipline.active ? 'success' : 'secondary'} rounded-pill">${discipline.active ? 'Ativa' : 'Inativa'}</span>
            `;

            const remove = document.createElement('span');
            remove.className = 'badge bg-danger ms-3 rounded-pill cursor-pointer';
            remove.textContent = 'Remover';
            remove.style.cursor = 'pointer';
            remove.addEventListener('click', () => {
                selected.delete(discipline.id);
                renderSelected();
            });

            button.appendChild(remove);
            selectedElement.appendChild(button);
        });
    }

    function renderSuggestions(query) {
        const normalized = query.trim().toLowerCase();
        const available = disciplines.filter(discipline => {
            if (selected.has(discipline.id)) {
                return false;
            }

            return discipline.name.toLowerCase().includes(normalized)
                || (discipline.code || '').toLowerCase().includes(normalized);
        });

        suggestionsElement.innerHTML = '';

        if (normalized.length === 0) {
            suggestionsElement.innerHTML = '<div class="text-muted">Digite para ver sugestões de disciplinas.</div>';
            return;
        }

        if (available.length === 0) {
            suggestionsElement.innerHTML = '<div class="text-muted">Nenhuma disciplina encontrada para essa busca.</div>';
            return;
        }

        available.slice(0, 10).forEach((discipline) => {
            const item = document.createElement('button');
            item.type = 'button';
            item.className = 'list-group-item list-group-item-action d-flex justify-content-between align-items-center';
            item.innerHTML = `
                <div>
                    <strong>${discipline.name}</strong>
                    <div class="small text-muted">${discipline.code || 'Sem código'}</div>
                </div>
                <span class="badge bg-${discipline.active ? 'success' : 'secondary'} rounded-pill">${discipline.active ? 'Ativa' : 'Inativa'}</span>
            `;
            item.addEventListener('click', () => {
                selected.set(discipline.id, discipline);
                renderSelected();
                searchInput.value = '';
                renderSuggestions('');
            });

            suggestionsElement.appendChild(item);
        });
    }

    function syncHiddenInputs() {
        document.querySelectorAll('input[name="disciplines[]"]').forEach(input => input.remove());

        selected.forEach((discipline) => {
            const hidden = document.createElement('input');
            hidden.type = 'hidden';
            hidden.name = 'disciplines[]';
            hidden.value = discipline.id;
            document.querySelector('form').appendChild(hidden);
        });
    }

    searchInput.addEventListener('input', (event) => {
        renderSuggestions(event.target.value);
    });

    document.querySelector('form').addEventListener('submit', () => {
        syncHiddenInputs();
    });

    renderSelected();
    renderSuggestions('');
</script>
@endpush
@endsection
