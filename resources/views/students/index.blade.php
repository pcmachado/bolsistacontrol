@extends('layouts.app')

@section('title', 'Alunos')

@section('content')
<div class="container-fluid">

    <div class="d-flex justify-content-between mb-3">
        <h3>Alunos</h3>
        <a href="{{ route('students.create') }}" class="btn btn-primary">
            ➕ Novo aluno
        </a>
    </div>

    {{-- FILTRO --}}
    <form method="GET" class="row g-2 mb-3">
        <div class="col-md-4">
            <select name="class_offering_id" class="form-select">
                <option value="">Todas as turmas</option>
                @foreach($classes as $c)
                    <option value="{{ $c->id }}" @selected($classId == $c->id)>
                        {{ $c->name ?? 'Turma '.$c->id }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-2">
            <button class="btn btn-primary">Filtrar</button>
        </div>
    </form>

    {{-- DATATABLE --}}
    {!! $dataTable->table() !!}

</div>
@endsection

@push('scripts')
    {!! $dataTable->scripts() !!}
@endpush