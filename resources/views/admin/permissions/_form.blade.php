@csrf

<div class="mb-3">
    <label for="name" class="form-label">Nome da Permissão</label>
    <input type="text" name="name" id="name" class="form-control"
           value="{{ old('name', $permission->name ?? '') }}" required>
</div>

<div class="d-flex justify-content-between">
    <a href="{{ route('admin.permissions.index') }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Voltar
    </a>
    <button type="submit" class="btn btn-primary">
        <i class="bi bi-save"></i> Salvar
    </button>
</div>
