@extends('layouts.app')

@section('content')
<div class="container">

    <h3>Novo aluno</h3>

    <form method="POST" action="{{ route('admin.students.store') }}">
        @csrf

        @include('admin.students._form')

        <button class="btn btn-primary mt-3">
            Salvar
        </button>
    </form>

</div>
@endsection