@extends('layouts.app')

@section('content')

<h4>Gestão de Bolsistas do Curso</h4>

<form method="POST" action="{{ route('admin.courses.holders.store', $course) }}">
    @csrf

    <div class="row">

        <div class="col-md-6">
            <select name="scholarship_holder_id" class="form-control">
                @foreach($available as $holder)
                    <option value="{{ $holder->id }}">
                        {{ $holder->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-4">
            <select name="role" class="form-control">
                <option value="orientador">Orientador</option>
                <option value="supervisor">Supervisor</option>
            </select>
        </div>

        <div class="col-md-2">
            <button class="btn btn-success w-100">Adicionar</button>
        </div>

    </div>
</form>

<hr>

<table class="table table-bordered mt-3">
    <thead>
        <tr>
            <th>Nome</th>
            <th>Papel</th>
            <th>Ações</th>
        </tr>
    </thead>
    <tbody>

        @foreach($holders as $holder)
            <tr>

                <td>{{ $holder->name }}</td>

                <td>
                    <form method="POST"
                          action="{{ route('admin.courses.holders.update', [$course, $holder->id]) }}">
                        @csrf
                        @method('PUT')

                        <select name="role" onchange="this.form.submit()">
                            <option value="orientador"
                                {{ $holder->pivot->role === 'orientador' ? 'selected' : '' }}>
                                Orientador
                            </option>

                            <option value="supervisor"
                                {{ $holder->pivot->role === 'supervisor' ? 'selected' : '' }}>
                                Supervisor
                            </option>
                        </select>
                    </form>
                </td>

                <td>
                    <form method="POST"
                          action="{{ route('admin.courses.holders.destroy', [$course, $holder->id]) }}">
                        @csrf
                        @method('DELETE')

                        <button class="btn btn-danger btn-sm">
                            Remover
                        </button>
                    </form>
                </td>

            </tr>
        @endforeach

    </tbody>
</table>

@endsection