@props(['route', 'icon', 'label', 'count' => 0])

@php $active = request()->routeIs($route); @endphp

<a href="{{ route($route) }}"
   class="nav-link {{ $active ? 'active' : '' }}">
    <i data-feather="{{ $icon }}" class="w-4 h-4 shrink-0"></i>
    <span>{{ $label }}</span>
    @if($count > 0)
        <span class="ml-auto bg-red-500 text-white text-xs font-bold rounded-full min-w-[18px] h-[18px] flex items-center justify-center px-1">
            {{ $count > 99 ? '99+' : $count }}
        </span>
    @endif
</a>
