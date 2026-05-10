@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-dark">Criar Nova Função</h1>
        <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Voltar
        </a>
    </div>

    @if (count($errors) > 0)
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong><i class="bi bi-exclamation-circle"></i> Erro ao criar função!</strong>
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.roles.store') }}">
        @csrf
        
        <div class="card shadow-lg mb-4">
            <div class="card-header bg-light">
                <h5 class="mb-0">Informações Básicas</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label for="name" class="form-label"><strong>Nome da Função</strong> <span class="text-danger">*</span></label>
                    <input type="text" 
                           name="name" 
                           placeholder="Ex: gerente_projeto, analista_financeiro" 
                           class="form-control @error('name') is-invalid @enderror"
                           value="{{ old('name') }}"
                           required>
                    @error('name')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                    <small class="form-text text-muted">Use snake_case para nomear a função (ex: coordenador_geral)</small>
                </div>

                <div class="mb-3">
                    <label for="level" class="form-label"><strong>Nível Hierárquico</strong> <span class="text-danger">*</span></label>
                    <input type="number"
                           name="level"
                           min="0"
                           max="100"
                           class="form-control @error('level') is-invalid @enderror"
                           value="{{ old('level', 10) }}"
                           required>
                    @error('level')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                    <small class="form-text text-muted">Quanto maior o número, maior o poder hierárquico desta função.</small>
                </div>
            </div>
        </div>

        <div class="card shadow-lg">
            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Atribuir Permissões (Opcional)</h5>
                <div class="btn-group btn-group-sm" role="group">
                    <button type="button" class="btn btn-outline-primary" id="selectAll">
                        <i class="bi bi-check-all"></i> Marcar Tudo
                    </button>
                    <button type="button" class="btn btn-outline-secondary" id="deselectAll">
                        <i class="bi bi-x-lg"></i> Desmarcar Tudo
                    </button>
                </div>
            </div>
            <div class="card-body">
                <p class="text-muted small mb-3">
                    <i class="bi bi-info-circle"></i> Você pode deixar em branco e adicionar permissões depois editando a função.
                </p>

                <div class="accordion" id="permissionsAccordion">
                    @forelse ($permissionsByCategory as $category => $permissions)
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="heading-{{ Str::slug($category) }}">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#collapse-{{ Str::slug($category) }}" aria-expanded="false"
                                        aria-controls="collapse-{{ Str::slug($category) }}">
                                    <strong>{{ $category }}</strong>
                                    <span class="badge bg-secondary ms-2">{{ $permissions->count() }}</span>
                                </button>
                            </h2>
                            <div id="collapse-{{ Str::slug($category) }}" class="accordion-collapse collapse"
                                 aria-labelledby="heading-{{ Str::slug($category) }}" data-bs-parent="#permissionsAccordion">
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
                                                           {{ in_array($permission['id'], old('permissions', [])) ? 'checked' : '' }}>
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
                    @empty
                        <div class="alert alert-info">Nenhuma permissão cadastrada no sistema.</div>
                    @endforelse
                </div>
            </div>

            <div class="card-footer bg-light d-flex justify-content-between">
                <span class="badge bg-success" id="selectedCount">0 permissões selecionadas</span>
                <div class="btn-group">
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-check-lg"></i> Criar Função
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

    function updateSelectedCount() {
        const selectedCount = document.querySelectorAll('.permission-checkbox:checked').length;
        selectedCountBadge.textContent = `${selectedCount} permissão${selectedCount !== 1 ? 'ões' : ''} selecionada${selectedCount !== 1 ? 's' : ''}`;
    }

    selectAllBtn.addEventListener('click', function() {
        checkboxes.forEach(checkbox => checkbox.checked = true);
        updateSelectedCount();
    });

    deselectAllBtn.addEventListener('click', function() {
        checkboxes.forEach(checkbox => checkbox.checked = false);
        updateSelectedCount();
    });

    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateSelectedCount);
    });

    updateSelectedCount();
});
</script>
@endpush

@endsection
@endsection