@extends('layouts.app')

@section('title', 'Frequências')

@section('content')
<div class="container-fluid">
    <h1 class="mb-3">Registros de Frequência</h1>

    {{-- Filtros --}}
    <form method="GET" class="row g-2 mb-3">
        <div class="col-md-3">
            <input type="month"
                   name="month"
                   value="{{ request('month') }}"
                   class="form-control">
        </div>

        <div class="col-md-3">
            <select name="status" class="form-select">
                <option value="">Todos</option>
                <option value="draft" @selected(request('status') === 'draft')>
                    Rascunhos
                </option>
                <option value="submitted" @selected(request('status') === 'submitted')>
                    Enviados
                </option>
            </select>
        </div>

        <div class="col-md-2">
            <button class="btn btn-primary">Filtrar</button>
        </div>
    </form>

    {{-- DataTable --}}
    {!! $dataTable->table() !!}
</div>
@endsection
