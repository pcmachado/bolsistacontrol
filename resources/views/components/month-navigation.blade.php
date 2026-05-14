@props([
    'route',
    'month',
    'params' => [],
    'minMonth' => '2020-01',
    'maxMonth' => null,
    'titleClass' => 'h4 mb-0 text-black',
    'buttonClass' => 'btn btn-outline-secondary',
])

@php
    $current = \Carbon\Carbon::createFromFormat('Y-m', $month)->startOfMonth();
    $min = \Carbon\Carbon::createFromFormat('Y-m', $minMonth)->startOfMonth();
    $max = \Carbon\Carbon::createFromFormat('Y-m', $maxMonth ?? now()->format('Y-m'))->startOfMonth();

    $prevMonth = $current->copy()->subMonth();
    $nextMonth = $current->copy()->addMonth();

    $prev = $prevMonth->format('Y-m');
    $next = $nextMonth->format('Y-m');

    $disablePrev = $prevMonth->lt($min);
    $disableNext = $nextMonth->gt($max);

    $baseParams = array_filter($params, fn ($value) => ! is_null($value) && $value !== '');
    $prevParams = array_merge($baseParams, ['month' => $prev]);
    $nextParams = array_merge($baseParams, ['month' => $next]);
@endphp

<div {{ $attributes->merge(['class' => 'd-flex justify-content-between align-items-center mb-3']) }}>
    <a href="{{ $disablePrev ? '#' : route($route, $prevParams) }}"
       class="{{ $buttonClass }} {{ $disablePrev ? 'disabled' : '' }}"
       @if($disablePrev) aria-disabled="true" tabindex="-1" @endif>
        &larr;
    </a>

    <h4 class="{{ $titleClass }}">
        {{ $current->translatedFormat('F/Y') }}
    </h4>

    <a href="{{ $disableNext ? '#' : route($route, $nextParams) }}"
       class="{{ $buttonClass }} {{ $disableNext ? 'disabled' : '' }}"
       @if($disableNext) aria-disabled="true" tabindex="-1" @endif>
        &rarr;
    </a>
</div>
