@extends('layouts.app')
@section('content')
<div class="bg-white p-8 rounded-lg shadow-xl w-full max-w-4xl mx-auto">
    <h1 class="text-3xl font-bold mb-6 text-center text-gray-800">Minhas Notificações</h1>
    @if ($notificacoes->isEmpty())
        <p class="text-center text-gray-500">Não há novas notificações.</p>
    @else
        <div class="space-y-4">
            @foreach ($notificacoes as $notificacao)
                <div class="bg-gray-50 p-4 rounded-md shadow flex justify-between items-center">
                    <div>
                        <p class="text-gray-800">{{ $notificacao->mensagem }}</p>
                        <p class="text-xs text-gray-500 mt-1">
                            Recebido em: {{ $notificacao->created_at->format('d/m/Y H:i') }}
                            @if(Auth::user()->role === 'coordenador')
                                - Bolsista: {{ $notificacao->bolsista->nome }}
                            @endif
                        </p>
                    </div>
                    <form action="{{ route('notificacoes.marcarLida', $notificacao) }}" method="POST">
                        @csrf
                        <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-md hover:bg-green-700">
                            Marcar como Lida
                        </button>
                    </form>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection