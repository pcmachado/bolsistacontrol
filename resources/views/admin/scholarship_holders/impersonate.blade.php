@extends('layouts.app')

@section('title', 'Bolsistas')

@section('content')
<div class="container">

    <h3 class="mb-4">👥 Bolsistas</h3>

    {{-- FILTROS --}}
    <form method="GET" class="row g-2 mb-4">

        <div class="col-md-3">
            <select name="project_id" class="form-control">
                <option value="">Projeto</option>
                @foreach($projects as $id => $name)
                    <option value="{{ $id }}" @selected(request('project_id') == $id)>
                        {{ $name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-2">
            <select name="unit_id" class="form-control">
                <option value="">Unidade</option>
                @foreach($units as $id => $name)
                    <option value="{{ $id }}" @selected(request('unit_id') == $id)>
                        {{ $name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-3">
            <select name="position_id" class="form-control">
                <option value="">Cargo</option>
                @foreach($positions as $id => $name)
                    <option value="{{ $id }}" @selected(request('position_id') == $id)>
                        {{ $name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-2">
            <select name="status" class="form-control">
                <option value="">Status</option>
                <option value="active">Ativo</option>
                <option value="inactive">Inativo</option>
            </select>
        </div>

        <div class="col-md-2">
            <button class="btn btn-primary w-100">Filtrar</button>
        </div>

    </form>

    {{-- TABELA --}}
    <div class="card">
        <div class="card-body">

            <table class="table table-sm align-middle">
                <thead>
                    <tr>
                        <th>Bolsista</th>
                        <th>Projeto</th>
                        <th>Unidade</th>
                        <th>Cargo</th>
                        <th>Status</th>
                        <th width="180">Ações</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($holders as $holder)
                        <tr>
                            <td>{{ $holder->user->name }}</td>
                            <td>{{ $holder->projects->pluck('name')->join(', ') }}</td>
                            <td>{{ $holder->unit?->name }}</td>
                            <td>
                                @foreach($holder->projects as $project)
                                    {{ optional($project->positions
                                        ->firstWhere('id', $project->pivot->position_id)
                                    )->name }}
                                @endforeach
                            </td>
                            <td>
                                <span class="badge bg-{{ $holder->status == 'active' ? 'success' : 'secondary' }}">
                                    {{ $holder->status }}
                                </span>
                            </td>
                            <td class="d-flex gap-1">

                                <a href="{{ route('admin.impersonate.holders.show', $holder) }}"
                                   class="btn btn-sm btn-outline-primary">
                                    👁
                                </a>

                                <a href="{{ route('admin.impersonate.holders.edit', $holder) }}"
                                   class="btn btn-sm btn-outline-warning">
                                    ✏️
                                </a>

                                {{-- 🔥 IMPERSONATE --}}
                                <form method="POST"
                                      action="{{ route('admin.impersonate', $holder->user) }}">
                                    @csrf
                                    <button class="btn btn-sm btn-outline-dark">
                                        🔐
                                    </button>
                                </form>

                            </td>
                        </tr>
                    @endforeach
                </tbody>

            </table>

            {{ $holders->withQueryString()->links() }}

        </div>
    </div>

</div>
@endsection
