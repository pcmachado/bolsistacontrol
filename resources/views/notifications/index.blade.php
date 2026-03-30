@extends('layouts.app')

@section('content')
<div class="container">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>
            Notificações
            @if($unreadCount ?? 0)
                <span class="badge bg-danger">{{ $unreadCount }}</span>
            @endif
        </h3>

        @if(auth()->user()->unreadNotifications()->exists())
            <form action="{{ route('notifications.markAll') }}" method="POST">
                @csrf
                <button class="btn btn-sm btn-primary">
                    ✔ Marcar todas como lidas
                </button>
            </form>
        @endif
    </div>

    <div class="list-group">

        @forelse($notifications as $n)

            @php
                $level = $n->data['level'] ?? 'info';

                $color = match($level) {
                    'warning' => 'list-group-item-warning',
                    'danger'  => 'list-group-item-danger',
                    'success' => 'list-group-item-success',
                    default   => ''
                };

                $icon = match($level) {
                    'warning' => 'bi-exclamation-triangle text-warning',
                    'danger'  => 'bi-x-circle text-danger',
                    'success' => 'bi-check-circle text-success',
                    default   => 'bi-info-circle text-primary'
                };
            @endphp

            <a href="{{ route('notifications.read', $n->id) }}"
               class="list-group-item list-group-item-action {{ is_null($n->read_at) ? 'fw-bold border-start border-4 border-primary' : '' }} {{ $color }}">

                <div class="d-flex w-100 justify-content-between">
                    <div>
                        <i class="bi {{ $icon }} me-2"></i>
                        <strong>{{ $n->data['title'] ?? 'Notificação' }}</strong>
                    </div>

                    <small>{{ $n->created_at->diffForHumans() }}</small>
                </div>

                <p class="mb-1 mt-1">
                    {{ $n->data['message'] ?? '' }}
                </p>

                @if(!empty($n->data['url']))
                    <small class="text-primary">
                        🔗 Clique para abrir
                    </small>
                @endif

            </a>

        @empty
            <div class="alert alert-light">
                Nenhuma notificação encontrada.
            </div>
        @endforelse

    </div>

    <div class="mt-3">
        {{ $notifications->links() }}
    </div>

</div>
@endsection