@extends('layouts.app')

@section('content')
<div class="container">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>Notificações</h3>

        @if(auth()->user()->unreadNotifications()->exists())
            <form action="{{ route('notifications.markAll') }}" method="POST">
                @csrf
                <button class="btn btn-sm btn-primary">Marcar todas como lidas</button>
            </form>
        @endif
    </div>

    @php
        $color = match($n->data['level'] ?? 'info') {
            'info' => '',
            'warning' => 'list-group-item-warning',
            'danger' => 'list-group-item-danger',
            default => ''
        };
    @endphp

    <div class="list-group">
        @forelse($notifications as $n)
            <a href="{{ route('notifications.read', $n->id) }}"
               class="list-group-item list-group-item-action {{ is_null($n->read_at) ? 'fw-bold' : '' }} {{ $color }}">
                <div class="d-flex w-100 justify-content-between">
                    <h6 class="mb-1">{{ $n->data['title'] ?? 'Notificação' }}</h6>
                    <small>{{ $n->created_at->diffForHumans() }}</small>
                </div>
                @if(!empty($n->data['url']))
                    <small class="text-primary">Clique para abrir</small>
                @endif
                <p class="mb-1">{{ $n->data['message'] ?? '' }}</p>
            </a>
        @empty
            <p class="text-muted">Nenhuma notificação encontrada.</p>
        @endforelse
    </div>

    <div class="mt-3">
        {{ $notifications->links() }}
    </div>
</div>
@endsection
