@props([
    'route' => null,
    'icon'  => null,
    'title',
    'activeRoutes' => []
])

@php
    // Detecta rota ativa
    $isActive = false;

    // Se a rota principal combinou
    if ($route && request()->routeIs($route)) {
        $isActive = true;
    }

    // Normaliza activeRoutes para array seguro
    if (is_string($activeRoutes)) {
        $activeRoutes = array_filter(array_map('trim', explode(',', $activeRoutes)));
    }

    // Itera somente se for array/iterável
    if (is_iterable($activeRoutes)) {
        foreach ($activeRoutes as $pattern) {
            if ($pattern && request()->routeIs($pattern)) {
                $isActive = true;
                break;
            }
        }
    }

    // Classes
    $linkClasses = 'sidebar-link d-flex align-items-center px-3 py-2';
    $linkClasses .= $isActive ? ' active' : '';
@endphp

<a 
    href="{{ $route ? route($route) : '#' }}"
    class="{{ $linkClasses }}"
>
    {{-- Ícone sempre visível --}}
    @if($icon)
        <i class="{{ $icon }} sidebar-icon me-3"></i>
    @endif

    {{-- Texto (oculto na sidebar colapsada via CSS) --}}
    <span class="sidebar-text">{{ $title }}</span>
</a>
