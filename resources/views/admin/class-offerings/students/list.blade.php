@extends('layouts.app')

@section('title', 'Alunos da turma')

@section('content')
<div class="container-fluid">

    <div class="card mb-3"><div class="card-body">
        <form method="POST" action="{{ route('admin.class.students.add', $class) }}" class="row g-2">
            @csrf
            <div class="col-md-9">
                <label class="form-label">Adicionar aluno da unidade</label>
                <select name="student_id" class="form-select" required>
                    <option value="">Selecione...</option>
                    @foreach(($unitStudents ?? collect()) as $student)
                        <option value="{{ $student->id }}">{{ $student->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <button class="btn btn-primary w-100">Adicionar à turma</button>
            </div>
        </form>
    </div></div>

    <div class="d-flex justify-content-between mb-3">
        <h3>Alunos - {{ $class->name }}</h3>

        <a href="{{ route('admin.class.students.index', $class) }}"
           class="btn btn-success">
            🧾 Lançamento
        </a>
    </div>

    {!! $dataTable->table() !!}

</div>
@endsection

@push('scripts')
{!! $dataTable->scripts() !!}
@endpush