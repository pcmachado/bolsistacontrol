@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="alert alert-info mb-0">
        A edicao detalhada do projeto foi movida para o fluxo em abas.
        <a href="{{ route('admin.projects.edit.general', $project) }}" class="alert-link">Abrir dados gerais</a>
    </div>
</div>
@endsection
