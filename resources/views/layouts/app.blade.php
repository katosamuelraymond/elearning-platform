<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Lincoln eLearning')</title>
    <link rel="stylesheet" href="/css/styles.css">
    <script src="{{ asset('js/app.js') }}"></script>

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
        }
    </script>

    <style>
        .scrollbar-hide::-webkit-scrollbar {
            display: none;
        }
        .scrollbar-hide {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        /* Fix for horizontal overflow */
        html, body {
            max-width: 100%;
            overflow-x: hidden;
        }

        /* Ensure all containers respect viewport boundaries */
        .min-h-screen {
            max-width: 100vw;
        }
    </style>

    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="//unpkg.com/alpinejs" defer></script>
</head>
<body class="h-full bg-gray-50 dark:bg-gray-900 transition-colors duration-300 max-w-full overflow-x-hidden">

<div id="navbar-container" class="w-full">
    @includeWhen($showNavbar ?? true, 'components.partials.navbar')
</div>

<div class="flex min-h-screen pt-16 w-full max-w-full">

    @if($showSidebar ?? false)
        <div id="sidebar-container" class="transition-all duration-300 w-auto">
            @include('components.partials.sidebar')
        </div>
    @endif

    <!-- Main content area with proper width constraints -->
    <div class="flex-1 flex flex-col min-w-0 w-full"> <!-- Added min-w-0 to prevent flex item overflow -->
        <main id="main-content" class="flex-1 transition-all duration-300 bg-white dark:bg-gray-800 w-full max-w-full overflow-x-hidden">
            <div class="w-full max-w-full">
                @yield('content')
            </div>
        </main>

        @if($showFooter ?? true)
            <div id="footer-container" class="w-full max-w-full">
                @include('components.partials.footer')
            </div>
        @endif
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const sidebarToggle = document.getElementById('sidebar-toggle');
        const sidebarContainer = document.getElementById('sidebar-container');

        if (sidebarToggle && sidebarContainer) {
            const sidebarCollapsed = localStorage.getItem('sidebar-collapsed');
            if (sidebarCollapsed === 'true') {
                sidebarContainer.classList.add('hidden');
            } else {
                sidebarContainer.classList.remove('hidden');
            }

            sidebarToggle.addEventListener('click', function() {
                sidebarContainer.classList.toggle('hidden');
                localStorage.setItem('sidebar-collapsed', sidebarContainer.classList.contains('hidden'));
            });
        }
    });
</script>

<script src="{{ asset('js/app.js') }}" defer></script>
</body>
</html>
