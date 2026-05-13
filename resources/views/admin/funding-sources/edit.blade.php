@extends('layouts.app')

@section('title', 'Editar Forma de Fomento')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-0">Editar Forma de Fomento</h1>
        <a href="{{ route('admin.funding-sources.index') }}" class="btn btn-outline-secondary">Voltar</a>
    </div>

    @include('admin.funding-sources.partials.form', [
        'action' => route('admin.funding-sources.update', $fundingSource),
        'method' => 'PUT',
        'fundingSource' => $fundingSource,
    ])
</div>
@endsection
