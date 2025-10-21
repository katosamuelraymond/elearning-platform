@props(['label', 'icon'])

<div x-data="{ open: false }" class="space-y-1">
    <button @click="open = !open" class="flex items-center justify-between w-full px-4 py-3 rounded-lg text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-200">
        <span class="flex items-center">
            <i class="{{ $icon }} mr-3 text-gray-400"></i>
            {{ $label }}
        </span>
        <i :class="open ? 'fas fa-chevron-down' : 'fas fa-chevron-right'"></i>
    </button>

    <div x-show="open" class="ml-6 mt-1 space-y-1" x-cloak>
        {{ $slot }}
    </div>
</div>
