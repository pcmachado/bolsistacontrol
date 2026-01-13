@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Editar Permissão</h1>

    <form action="{{ route('admin.permissions.update', $permission) }}" method="POST">
        @method('PUT')
        @include('admin.permissions._form')
    </form>
</div>
@endsection
