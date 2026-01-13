@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Criar Permissão</h1>

    <form action="{{ route('admin.permissions.store') }}" method="POST">
        @include('admin.permissions._form')
    </form>
</div>
@endsection
