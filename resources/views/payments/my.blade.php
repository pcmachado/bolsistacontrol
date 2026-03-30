@extends('layouts.app')

@section('title', 'Meus Pagamentos')

@section('content')
<div class="container-fluid">

    <form method="GET">

        <input type="month" name="month" class="form-control">

        <input type="number" name="year" placeholder="Ano" class="form-control">

        <button class="btn btn-primary">Filtrar</button>

    </form>

    <h3 class="mb-4">Meus Pagamentos</h3>

    <a href="{{ route('payments.my.report', ['pdf' => 1]) }}" target="_blank" class="btn btn-danger"> 📄 Gerar PDF </a>

    {!! $dataTable->table() !!}

</div>
@endsection

@push('scripts')
    {!! $dataTable->scripts() !!}
@endpush
