@props(['href', 'label', 'icon', 'active' => false])

@php
    // Detect if current route matches (for initial page load)
    $isActive = $active || request()->url() === $href || request()->routeIs(str_replace(url('/'), '', $href) . '*');
@endphp

<a href="{{ $href }}"
   class="ajax-link flex items-center px-4 py-2 text-sm rounded-lg transition-colors duration-200
          {{ $isActive
              ? 'bg-indigo-100 text-indigo-700 dark:bg-indigo-800 dark:text-white font-semibold'
              : 'text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700' }}">
    <i class="{{ $icon }} mr-3"></i>
    {{ $label }}
</a>
