@extends('layouts.app')

@section('title', 'Alunos da turma')

@section('content')
<div class="container-fluid">

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