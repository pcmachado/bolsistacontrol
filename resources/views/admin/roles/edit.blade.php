@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-dark">Editar Função: <strong>{{ ucfirst(str_replace('_', ' ', $role->name)) }}</strong></h1>
            <small class="text-muted">Total de Permissões: {{ $permissionCount }} ({{ $permissionPercentage }}%)</small>
        </div>
        <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Voltar
        </a>
    </div>

    <form action="{{ route('admin.roles.update', $role) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="card shadow-lg">
            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Selecionar Permissões por Categoria</h5>
                <div class="btn-group btn-group-sm" role="group">
                    <button type="button" class="btn btn-outline-primary" id="selectAll" title="Marcar Tudo">
                        <i class="bi bi-check-all"></i> Marcar Tudo
                    </button>
                    <button type="button" class="btn btn-outline-secondary" id="deselectAll" title="Desmarcar Tudo">
                        <i class="bi bi-x-lg"></i> Desmarcar Tudo
                    </button>
                </div>
            </div>

            <div class="card-body">
                @if ($permissionsByCategory->isEmpty())
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> Nenhuma permissão disponível no sistema.
                    </div>
                @else
                    <div class="accordion" id="permissionsAccordion">
                        @foreach ($permissionsByCategory as $category => $permissions)
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button" type="button" data-bs-toggle="collapse" 
                                            data-bs-target="#collapse{{ Str::slug($category) }}" 
                                            aria-expanded="true" 
                                            aria-controls="collapse{{ Str::slug($category) }}">
                                        <i class="bi bi-folder me-2"></i>
                                        <strong>{{ $category }}</strong>
                                        <span class="badge bg-primary ms-auto">{{ $permissions->count() }}</span>
                                    </button>
                                </h2>
                                <div id="collapse{{ Str::slug($category) }}" class="accordion-collapse collapse show" 
                                     data-bs-parent="#permissionsAccordion">
                                    <div class="accordion-body">
                                        <div class="row">
                                            @foreach ($permissions as $permission)
                                                <div class="col-md-6 col-lg-4 mb-3">
                                                    <div class="form-check">
                                                        <input type="checkbox" 
                                                               name="permissions[]" 
                                                               value="{{ $permission['id'] }}"
                                                               class="form-check-input permission-checkbox"
                                                               id="perm_{{ $permission['id'] }}"
                                                               data-category="{{ Str::slug($category) }}"
                                                               {{ $permission['assigned'] ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="perm_{{ $permission['id'] }}">
                                                            <span class="fw-semibold">{{ $permission['label'] }}</span>
                                                            <small class="d-block text-muted">{{ $permission['name'] }}</small>
                                                        </label>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="card-footer bg-light d-flex justify-content-between">
                <div>
                    <span class="badge bg-success me-2" id="selectedCount">0 permissões selecionadas</span>
                </div>
                <div class="btn-group">
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-check-lg"></i> Salvar Permissões
                    </button>
                    <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary">
                        <i class="bi bi-x-lg"></i> Cancelar
                    </a>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectAllBtn = document.getElementById('selectAll');
    const deselectAllBtn = document.getElementById('deselectAll');
    const checkboxes = document.querySelectorAll('.permission-checkbox');
    const selectedCountBadge = document.getElementById('selectedCount');

    // Função para atualizar o contador
    function updateSelectedCount() {
        const selectedCount = document.querySelectorAll('.permission-checkbox:checked').length;
        selectedCountBadge.textContent = `${selectedCount} permissão${selectedCount !== 1 ? 'ões' : ''} selecionada${selectedCount !== 1 ? 's' : ''}`;
    }

    // Marcar Tudo
    selectAllBtn.addEventListener('click', function() {
        checkboxes.forEach(checkbox => {
            checkbox.checked = true;
        });
        updateSelectedCount();
    });

    // Desmarcar Tudo
    deselectAllBtn.addEventListener('click', function() {
        checkboxes.forEach(checkbox => {
            checkbox.checked = false;
        });
        updateSelectedCount();
    });

    // Atualizar contador ao mudar checkbox
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateSelectedCount);
    });

    // Atualizar contador inicial
    updateSelectedCount();
});
</script>
@endpush

<style>
.accordion-button:not(.collapsed) {
    background-color: #e7f1ff;
    color: #0d6efd;
}

.accordion-button {
    padding: 1rem 1.5rem;
    font-weight: 500;
}

.accordion-body {
    padding: 1.5rem;
}

.permission-checkbox {
    cursor: pointer;
    width: 20px;
    height: 20px;
    margin-top: 2px;
}

.form-check-label {
    cursor: pointer;
    user-select: none;
    margin-left: 0.5rem;
}

.form-check-label small {
    font-size: 0.75rem;
    margin-top: 0.25rem;
}
</style>

@endsection
