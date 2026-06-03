@props(['active' => false])

@php
    $classes = $active
        ? 'flex items-center gap-3 rounded-lg bg-indigo-50 px-3 py-2 text-sm font-medium text-indigo-700'
        : 'flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium text-gray-600 hover:bg-gray-50 hover:text-gray-900';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    @if (isset($icon))
        <span class="shrink-0 text-gray-400 [[aria-current=page]_&]:text-indigo-600">{{ $icon }}</span>
    @endif
    <span>{{ $slot }}</span>
</a>
