@props(['active', 'icon', 'badge' => null])

@php
$classes = ($active ?? false)
            ? 'sidebar-link-active flex items-center gap-3 px-4 py-3 rounded-lg text-sm transition-all duration-200 group'
            : 'flex items-center gap-3 px-4 py-3 rounded-lg text-sm text-gray-500 hover:bg-gray-100 hover:text-gray-900 transition-all duration-200 group';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    <div class="w-5 h-5 flex items-center justify-center">
        <i class="{{ $icon }} {{ $active ? 'text-amber-600' : 'text-gray-400 group-hover:text-gray-600' }} transition-colors"></i>
    </div>
    <span class="flex-1 whitespace-nowrap overflow-hidden" x-show="sidebarOpen">
        {{ $slot }}
    </span>
    @if($badge && $badge > 0)
        <span class="px-2 py-0.5 rounded-full bg-red-100 text-red-600 text-[10px] font-bold" x-show="sidebarOpen">
            {{ $badge }}
        </span>
    @endif
</a>
