@extends('layouts.app')

@section('title', 'Submissões de Frequência')

@section('content')
<div class="container-fluid">

    {{-- Cabeçalho --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Submissões Mensais de Frequência</h1>

        @role('bolsista')
            <form method="POST" action="{{ route('my-attendance.submissions.store') }}">
                @csrf
                <input type="hidden" name="month" value="{{ now()->format('Y-m') }}">
                <button class="btn btn-primary">
                    <i class="bi bi-send"></i>
                    Enviar mês atual
                </button>
            </form>
        @endrole
    </div>

    {{-- Cards de resumo --}}
    <div class="row mb-4">
        @include('attendance.submissions.cards.submitted')
        @include('attendance.submissions.cards.approved')
        @include('attendance.submissions.cards.rejected')
        @include('attendance.submissions.cards.late')
    </div>


    {{-- Filtros --}}
    @include('attendance.submissions.partials.filters')

    {{-- Tabela --}}
    <div class="card shadow-sm">
        <div class="card-body">
            {!! $dataTable->table() !!}
        </div>
    </div>

</div>
@endsection

@push('scripts')
    {!! $dataTable->scripts() !!}
@endpush
