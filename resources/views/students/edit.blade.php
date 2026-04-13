@extends('layouts.app')

@section('content')
<div class="container">

    <h3>Editar aluno</h3>

    <form method="POST" action="{{ route('students.update', $student) }}">
        @csrf
        @method('PUT')

        @include('students._form')

        <button class="btn btn-primary mt-3">
            Atualizar
        </button>
    </form>

</div>
@endsection