@props([
    'route' => null,
    'icon' => '',
    'title' => '',
    'activeRoutes' => []
])

@php
    $isActive = false;

    if ($route && request()->routeIs($route)) {
        $isActive = true;
    }

    if (is_string($activeRoutes)) {
        $activeRoutes = array_filter(array_map('trim', explode(',', $activeRoutes)));
    }

    if (is_iterable($activeRoutes)) {
        foreach ($activeRoutes as $pattern) {
            if (request()->routeIs($pattern)) {
                $isActive = true;
                break;
            }
        }
    }

    $classes = 'nav-link d-flex align-items-center rounded px-3 py-2';

    $classes .= $isActive
        ? ' active'
        : ' link-body-emphasis';
@endphp

<a href="{{ $route ? route($route) : '#' }}"
   class="{{ $classes }}"
   data-title="{{ $title }}">

    <i class="{{ $icon }} sidebar-icon me-2"></i>

    <span class="sidebar-item-text text-truncate">
        {{ $title }}
    </span>
</a>