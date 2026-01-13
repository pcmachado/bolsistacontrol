@extends('layouts.app')

@section('title', 'Pagamento em Lote')

@section('content')
<div class="container">

    <h1 class="mb-4">Pagamento em Lote</h1>

    <form method="POST" action="{{ route('admin.payments.batch.preview') }}">
        @csrf

        <div class="row g-3">

            <div class="col-md-4">
                <label class="form-label">Unidade</label>
                <select name="unit_id" class="form-select" required>
                    @foreach($units as $unit)
                        <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-4">
                <label class="form-label">Mês</label>
                <input type="number" name="month" class="form-control"
                       min="1" max="12" value="{{ now()->month }}" required>
            </div>

            <div class="col-md-4">
                <label class="form-label">Ano</label>
                <input type="number" name="year" class="form-control"
                       value="{{ now()->year }}" required>
            </div>

        </div>

        <div class="mt-4 text-end">
            <button class="btn btn-primary">
                Gerar prévia →
            </button>
        </div>

    </form>

</div>
@endsection
