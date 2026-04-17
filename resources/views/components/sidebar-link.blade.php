@props(['active', 'icon', 'badge' => null])

@php
$classes = ($active ?? false)
            ? 'relative sidebar-link-active flex items-center rounded-lg text-sm transition-all duration-200 group'
            : 'relative flex items-center rounded-lg text-sm text-gray-500 hover:bg-gray-100 hover:text-gray-900 transition-all duration-200 group';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }} 
   :class="sidebarOpen ? 'px-4 py-3 gap-3' : 'px-0 py-3 justify-center mb-1'"
   @click="if(window.innerWidth < 1024) sidebarOpen = false">
    <div class="w-6 h-6 flex items-center justify-center shrink-0">
        <i class="{{ $icon }} {{ $active ? 'text-amber-600' : 'text-gray-400 group-hover:text-gray-600' }} text-[1.1rem] transition-colors"></i>
    </div>
    <span class="flex-1 whitespace-nowrap overflow-hidden text-ellipsis" x-show="sidebarOpen" style="display: none;">
        {{ $slot }}
    </span>
    @if($badge && $badge > 0)
        <!-- Full badge for opened sidebar -->
        <span class="px-2 py-0.5 rounded-full bg-red-100 text-red-600 text-[10px] font-bold shrink-0" x-show="sidebarOpen" style="display: none;">
            {{ $badge }}
        </span>
        <!-- Red dot for closed sidebar -->
        <span class="absolute top-1.5 right-1.5 w-2 h-2 rounded-full bg-red-500 ring-2 ring-white" x-show="!sidebarOpen" style="display: none;"></span>
    @endif
</a>
