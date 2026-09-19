@props(['route', 'icon', 'label'])

@php $active = request()->routeIs($route); @endphp

<a href="{{ route($route) }}"
   class="nav-link {{ $active ? 'active' : '' }}">
    <i data-feather="{{ $icon }}" class="w-4 h-4 shrink-0"></i>
    <span>{{ $label }}</span>
</a>
