@extends('layouts.guest')

@section('title', 'Bem-vindo')

@section('content')
    <div class="text-center">
        <h1 class="mb-4">🎓 ProBolsas</h1>
        <p class="lead mb-4">
            Bem-vindo ao sistema de gestão acadêmica.<br>
            Aqui coordenadores e bolsistas acompanham unidades, projetos e frequências de forma simples e segura.
        </p>

        <a href="{{ route('login') }}" class="btn btn-primary btn-lg">
            Entrar no Sistema
        </a>
        <hr class="my-4">
        <a href="{{ route('payments.verify.form') }}" class="btn btn-secondary">
            Verificar Recibo de Pagamento
        </a>
    </div>
@endsection
