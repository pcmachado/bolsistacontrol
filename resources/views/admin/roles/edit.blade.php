@extends('layouts.app')

@section('content')
<h1>Editar Função: {{ ucfirst($role->name) }}</h1>

<form action="{{ route('admin.roles.update', $role) }}" method="POST">
    @csrf
    @method('PUT')

    <div class="mb-3">
        <label for="permissions" class="form-label">Permissões</label>
        <div class="row">
            @foreach($permissions as $permission)
                <div class="col-md-4">
                    <div class="form-check">
                        <input type="checkbox" name="permissions[]" value="{{ $permission->id }}"
                               class="form-check-input"
                               id="perm_{{ $permission->id }}"
                               {{ in_array($permission->id, $rolePermissions) ? 'checked' : '' }}>
                        <label class="form-check-label" for="perm_{{ $permission->id }}">
                            {{ ucfirst($permission->name) }}
                        </label>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <button type="submit" class="btn btn-primary">Salvar</button>
    <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary">Cancelar</a>
</form>
@endsection
