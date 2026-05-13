@extends('layouts.app')

@section('title', 'Cadastrar Forma de Fomento')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-0">Cadastrar Forma de Fomento</h1>
        <a href="{{ route('admin.funding-sources.index') }}" class="btn btn-outline-secondary">Voltar</a>
    </div>

    @include('admin.funding-sources.partials.form', [
        'action' => route('admin.funding-sources.store'),
        'method' => 'POST',
        'fundingSource' => new \App\Models\FundingSource(['type' => 'external', 'active' => true]),
    ])
</div>
@endsection
