@php
    $groupLabels = [
        'feature' => 'Novas funcionalidades',
        'fix' => 'Correções',
        'refactor' => 'Melhorias internas',
        'maintenance' => 'Manutenção',
        'other' => 'Outras alterações',
    ];

    $groupStyles = [
        'feature' => 'success',
        'fix' => 'warning',
        'refactor' => 'primary',
        'maintenance' => 'secondary',
        'other' => 'dark',
    ];

    $groupedChanges = collect($changes)->groupBy('type');
@endphp

@if($groupedChanges->isEmpty())
    <p class="mb-0 text-muted">
        Nenhuma alteração elegível foi encontrada no histórico recente do Git para compor esta versão.
    </p>
@else
    <div class="release-notes-auto">
        <p class="text-muted small mb-3">
            Notas geradas automaticamente a partir do histórico do Git.
        </p>

        @foreach($groupedChanges as $type => $items)
            <div class="mb-3">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <span class="badge text-bg-{{ $groupStyles[$type] ?? 'secondary' }}">
                        {{ $groupLabels[$type] ?? 'Alterações' }}
                    </span>
                    <span class="text-muted small">{{ $items->count() }} item(ns)</span>
                </div>

                <ul class="mb-0 ps-3">
                    @foreach($items as $item)
                        <li>{{ $item['message'] }}</li>
                    @endforeach
                </ul>
            </div>
        @endforeach
    </div>
@endif
