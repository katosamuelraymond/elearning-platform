@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation">
        <ul class="flex justify-center items-center space-x-2">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <li aria-disabled="true">
                    <span class="px-3 py-2 text-gray-400 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md cursor-not-allowed">
                        &laquo;
                    </span>
                </li>
            @else
                <li>
                    <a href="{{ $paginator->previousPageUrl() }}"
                       class="ajax-link px-3 py-2 text-blue-600 dark:text-blue-400 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors duration-200">
                        &laquo;
                    </a>
                </li>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <li aria-disabled="true">
                        <span class="px-3 py-2 text-gray-400 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md">
                            {{ $element }}
                        </span>
                    </li>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li aria-current="page">
                                <span class="px-3 py-2 text-white bg-blue-600 border border-blue-600 rounded-md">
                                    {{ $page }}
                                </span>
                            </li>
                        @else
                            <li>
                                <a href="{{ $url }}"
                                   class="ajax-link px-3 py-2 text-blue-600 dark:text-blue-400 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors duration-200">
                                    {{ $page }}
                                </a>
                            </li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <li>
                    <a href="{{ $paginator->nextPageUrl() }}"
                       class="ajax-link px-3 py-2 text-blue-600 dark:text-blue-400 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors duration-200">
                        &raquo;
                    </a>
                </li>
            @else
                <li aria-disabled="true">
                    <span class="px-3 py-2 text-gray-400 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md cursor-not-allowed">
                        &raquo;
                    </span>
                </li>
            @endif
        </ul>
    </nav>
@endif
