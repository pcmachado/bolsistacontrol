@extends('layouts.app')

@section('title', 'Enviar Pagamento')

@section('content')
<div class="container">

    <h3 class="mb-4">
        <i class="bi bi-cash-coin me-2"></i>
        Enviar Pagamento
    </h3>

    @if ($errors->any())
        <div class="alert alert-danger">
            {{ $errors->first() }}
        </div>
    @endif

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">

            <form method="POST" action="{{ route('admin.payments.store') }}">
                @csrf

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Bolsista</label>
                        <select name="scholarship_holder_id" class="form-select" required>
                            <option value="">Selecione</option>
                            @foreach($scholarshipHolders as $h)
                                <option value="{{ $h->id }}">
                                    {{ $h->name }} — {{ $h->user->email }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Mês</label>
                        <select name="month" class="form-select" required>
                            @for($m=1; $m<=12; $m++)
                                <option value="{{ $m }}">{{ str_pad($m,2,'0',STR_PAD_LEFT) }}</option>
                            @endfor
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Ano</label>
                        <input type="number" name="year" class="form-control"
                               value="{{ now()->year }}" required>
                    </div>
                </div>

                <div class="d-flex justify-content-end">
                    <button class="btn btn-primary">
                        <i class="bi bi-send"></i> Enviar para Pagamento
                    </button>
                </div>

            </form>

        </div>
    </div>

</div>
@endsection
