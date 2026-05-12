@extends('layouts.app')

@section('title', 'Criar Novo Usuário')

@section('content')
<div class="container-fluid py-4">
    <!-- Cabeçalho -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-dark">Criar Novo Usuário</h1>
        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary shadow-sm">
            <i class="bi bi-arrow-left me-1"></i> Voltar
        </a>
    </div>

    <!-- Alertas de Erro -->
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
            <strong><i class="bi bi-exclamation-triangle-fill me-2"></i> Erro!</strong> Há problemas com os dados inseridos.
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Formulário -->
    <div class="card shadow-sm border-0">
        <div class="card-body p-4">
            <form method="POST" action="{{ route('admin.users.store') }}">
                @csrf

                <div class="row g-4">
                    <!-- Nome -->
                    <div class="col-md-6">
                        <label for="name" class="form-label fw-bold">Nome Completo <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" placeholder="Ex: João da Silva" class="form-control @error('name') is-invalid @enderror" required>
                    </div>

                    <!-- E-mail -->
                    <div class="col-md-6">
                        <label for="email" class="form-label fw-bold">E-mail <span class="text-danger">*</span></label>
                        <input type="email" name="email" id="email" value="{{ old('email') }}" placeholder="Ex: joao@exemplo.com" class="form-control @error('email') is-invalid @enderror" required>
                    </div>

                    <!-- Senha -->
                    <div class="col-md-6">
                        <label for="password" class="form-label fw-bold">Senha <span class="text-danger">*</span></label>
                        <input type="password" name="password" id="password" placeholder="Digite uma senha forte" class="form-control @error('password') is-invalid @enderror" required>
                        <div class="form-text text-muted small">
                            <i class="bi bi-info-circle"></i> Se marcar "Notificar usuário" abaixo, esta senha será substituída por uma temporária.
                        </div>
                    </div>

                    <!-- Função (Role) -->
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Função (Role) <span class="text-danger">*</span></label>
                        <x-role-selector
                            :selectedRoles="old('role') ? [old('role')] : []"
                            :user="auth()->user()"
                            :multiple="false"
                            :required="true"
                            name="role"
                        />
                    </div>

                    <!-- Instituição -->
                    <div class="col-md-6">
                        <label for="institution_id" class="form-label fw-bold">Instituição</label>
                        <select name="institution_id" id="institution_id" class="form-select @error('institution_id') is-invalid @enderror">
                            <option value="">Selecione...</option>
                            @foreach($institutions as $id => $name)
                                <option value="{{ $id }}" {{ (isset($selectedInstitution) && $selectedInstitution == $id) || old('institution_id') == $id ? 'selected' : '' }}>
                                    {{ $name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Unidade (CORRIGIDO PARA ARRAY DO PLUCK) -->
                    <div class="col-md-6">
                        <label for="unit_id" class="form-label fw-bold">Unidade</label>
                        <select name="unit_id" id="unit_id" class="form-select @error('unit_id') is-invalid @enderror">
                            <option value="">Selecione uma unidade (opcional)</option>
                            @foreach ($units as $id => $name)
                                <option value="{{ $id }}" {{ old('unit_id') == $id ? 'selected' : '' }}>
                                    {{ $name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Notificar Usuário (Checkbox) -->
                    <div class="col-12 mt-4">
                        <div class="form-check form-switch p-3 bg-light rounded border">
                            <input type="checkbox" name="notify_user" value="1" class="form-check-input ms-0 me-3" id="notify_user" style="width: 2.5em; height: 1.25em; cursor: pointer;">
                            <label class="form-check-label fw-bold" style="cursor: pointer;" for="notify_user">
                                Notificar usuário por e-mail
                                <span class="d-block text-muted fw-normal small mt-1">
                                    <i class="bi bi-envelope"></i> Se marcado, uma senha temporária será gerada e enviada por e-mail para o usuário.
                                </span>
                            </label>
                        </div>
                    </div>

                    <!-- Botões de Ação -->
                    <div class="col-12 text-end mt-4">
                        <hr class="mb-4">
                        <a href="{{ route('admin.users.index') }}" class="btn btn-light border shadow-sm me-2">Cancelar</a>
                        <button type="submit" class="btn btn-primary shadow-sm">
                            <i class="bi bi-save me-1"></i> Criar Usuário
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('institution_id').addEventListener('change', function () {
    fetch('/api/institutions/' + this.value + '/units')
        .then(res => res.json())
        .then(data => {
            let unitSelect = document.getElementById('unit_id');
            unitSelect.innerHTML = '<option value="">Selecione uma unidade (opcional)...</option>';

            data.forEach(unit => {
                unitSelect.innerHTML += `<option value="${unit.id}">${unit.name}</option>`;
            });

            if (data.length === 1) {
                unitSelect.value = data[0].id;
            }
        });
});
</script>
@endpush
