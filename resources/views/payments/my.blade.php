@extends('layouts.app')

@section('title', 'Meus Pagamentos')

@section('content')
<div class="container-fluid">

    <h3 class="mb-4">Meus Pagamentos</h3>

    {!! $dataTable->table() !!}

</div>
@endsection

@push('scripts')
    {!! $dataTable->scripts() !!}
@endpush
