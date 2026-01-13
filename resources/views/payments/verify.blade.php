@extends('layouts.guest')

@section('title', 'Verificar Recibo')

@section('content')
<div class="container py-5">
    <h3>Verificação de Recibo</h3>

    <form method="POST" action="{{ route('receipt.verify') }}">
        @csrf

        <div class="mb-3">
            <label>Código de verificação</label>
            <input type="text"
                   name="receipt_hash"
                   class="form-control"
                   placeholder="Cole o código do recibo">
        </div>

        <button class="btn btn-primary">
            Verificar
        </button>
    </form>
</div>
@endsection
