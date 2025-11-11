<nav class="fixed top-0 w-full z-50 bg-white border-b border-gray-200 shadow-sm transition-colors duration-300 dark:bg-gray-800 dark:border-gray-700">
    <div class="px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
            <!-- Left Section: Logo & Sidebar Toggle -->
            <div class="flex items-center space-x-4">
                <!-- Sidebar Toggle Button - Show when logged in -->
                @auth
                <button id="sidebar-toggle" class="p-2 rounded-lg text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
                @endauth

                <!-- Logo -->
                <a href="{{ auth()->check()
            ? (
                auth()->user()->isAdmin() ? route('admin.dashboard')
                : (auth()->user()->isTeacher() ? route('teacher.dashboard')
                : route('student.dashboard'))
              )
            : url('/') }}"
                   class="flex items-center space-x-2">
                    <div class="w-8 h-8 bg-gradient-to-r from-blue-600 to-purple-600 rounded-lg flex items-center justify-center">
                        <i class="fas fa-graduation-cap text-white text-sm"></i>
                    </div>
                    <span class="text-xl font-bold text-gray-900 dark:text-white">
        Lincoln<span class="text-blue-600 dark:text-blue-400">Learn</span>
    </span>
                </a>

            </div>



            <!-- Right Section: User Menu & Theme Toggle -->
            <div class="flex items-center space-x-3">
                <!-- Global Theme Toggle -->
                <button id="theme-toggle" type="button"
                        class="p-2 text-gray-500 hover:bg-gray-100 rounded-lg transition-colors duration-200 dark:text-gray-400 dark:hover:bg-gray-700"
                        title="Toggle dark/light mode">
                    <svg id="theme-toggle-dark-icon" class="w-5 h-5 hidden" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path>
                    </svg>
                    <svg id="theme-toggle-light-icon" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z"
                              fill-rule="evenodd" clip-rule="evenodd"></path>
                    </svg>
                </button>
                    <a href="{{ route('home') }}" class="inline-flex items-center justify-center rounded-full p-2 text-gray-700 hover:text-white bg-blue-300 hover:bg-blue-500 transition-colors duration-200" title="Home">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M3 9.75L12 3l9 6.75V21a1.5 1.5 0 01-1.5 1.5H4.5A1.5 1.5 0 013 21V9.75zM12 5.25L5.25 10.5v10.5h13.5V10.5L12 5.25z"/>
                        </svg>
                    </a>


                @auth
                <!-- User Profile Dropdown - Only show when logged in -->
               <div class="relative" x-data="{ open: false }">
    <button @click="open = !open"
            class="flex items-center space-x-3 p-2 rounded-lg hover:bg-gray-100 transition-colors duration-200 dark:hover:bg-gray-700">
        <div class="w-8 h-8 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center">
            <span class="text-white text-sm font-semibold">
                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
            </span>
        </div>
        <div class="hidden md:block text-left">
            <div class="text-sm font-medium text-gray-900 dark:text-white">
                {{ auth()->user()->name }}
            </div>
            <div class="text-xs text-gray-500 dark:text-gray-400 capitalize">
                {{ auth()->user()->primaryRole }}
            </div>
        </div>
        <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
        </svg>
    </button>

    <!-- Dropdown Menu -->
    <div x-show="open" @click.away="open = false"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="transform opacity-0 scale-95"
         x-transition:enter-end="transform opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-75"
         x-transition:leave-start="transform opacity-100 scale-100"
         x-transition:leave-end="transform opacity-0 scale-95"
         class="absolute right-0 mt-2 w-56 bg-white rounded-lg shadow-lg border border-gray-200 py-1 z-50 dark:bg-gray-800 dark:border-gray-700">
        <div class="px-4 py-2 border-b border-gray-100 dark:border-gray-600">
            <div class="text-sm font-medium text-gray-900 dark:text-white">
                {{ auth()->user()->name }}
            </div>
            <div class="text-xs text-gray-500 dark:text-gray-400">
                {{ auth()->user()->email }}
            </div>
        </div>

        <!-- Dynamic Links for All Roles -->
        <a href="{{ route(auth()->user()->routeRole . '.profile') }}"
           class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors duration-200 dark:text-gray-200 dark:hover:bg-gray-700">
            <i class="fas fa-user-circle mr-3 text-gray-400"></i>
            My Profile
        </a>

        <a href="{{ route(auth()->user()->routeRole . '.settings') }}"
           class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors duration-200 dark:text-gray-200 dark:hover:bg-gray-700">
            <i class="fas fa-cog mr-3 text-gray-400"></i>
            Settings
        </a>

        <div class="border-t border-gray-100 dark:border-gray-600 my-1"></div>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                    class="flex items-center w-full px-4 py-2 text-sm text-red-600 hover:bg-gray-100 transition-colors duration-200 dark:text-red-400 dark:hover:bg-gray-700">
                <i class="fas fa-sign-out-alt mr-3"></i>
                Sign Out
            </button>
        </form>
    </div>
</div>

                @else
                <!-- Guest Auth Buttons - Only show when NOT logged in -->
                <div class="hidden md:flex items-center space-x-2">
                    <a href="{{ route('login') }}"
                       class="px-4 py-2 text-sm font-medium text-white bg-gradient-to-r from-blue-600 to-purple-600 rounded-lg hover:from-blue-700 hover:to-purple-700 transition-all duration-200 shadow-sm">
                        Sign In
                    </a>
                </div>
                @endauth

                <!-- Mobile Menu Button -->

            </div>
        </div>
    </div>



</nav>


<script>
    // Global Theme Toggle Functionality for CDN
    function initializeTheme() {
        const themeToggleBtn = document.getElementById('theme-toggle');
        const themeToggleDarkIcon = document.getElementById('theme-toggle-dark-icon');
        const themeToggleLightIcon = document.getElementById('theme-toggle-light-icon');

        // Check for saved theme preference or system preference
        const currentTheme = localStorage.getItem('theme') || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');

        // Apply the current theme
        if (currentTheme === 'dark') {
            document.documentElement.classList.add('dark');
            themeToggleDarkIcon.classList.add('hidden');
            themeToggleLightIcon.classList.remove('hidden');
        } else {
            document.documentElement.classList.remove('dark');
            themeToggleDarkIcon.classList.remove('hidden');
            themeToggleLightIcon.classList.add('hidden');
        }

        // Theme toggle event
        themeToggleBtn.addEventListener('click', function() {
            // Toggle theme
            if (document.documentElement.classList.contains('dark')) {
                document.documentElement.classList.remove('dark');
                localStorage.setItem('theme', 'light');
                themeToggleDarkIcon.classList.remove('hidden');
                themeToggleLightIcon.classList.add('hidden');
            } else {
                document.documentElement.classList.add('dark');
                localStorage.setItem('theme', 'dark');
                themeToggleDarkIcon.classList.add('hidden');
                themeToggleLightIcon.classList.remove('hidden');
            }
        });
    }

    // Initialize sidebar state
    function initializeSidebar() {
        const sidebar = document.getElementById('sidebar-container');
        if (sidebar && localStorage.getItem('sidebar-collapsed') === 'true') {
            sidebar.classList.add('hidden');
        }
    }

    // Close mobile menu when clicking outside
    document.addEventListener('click', function(event) {
        const mobileMenu = document.getElementById('mobile-menu');
        const mobileButton = document.getElementById('mobile-menu-button');

        if (mobileMenu && mobileButton && !mobileMenu.contains(event.target) && !mobileButton.contains(event.target)) {
            mobileMenu.classList.add('hidden');
        }
    });

    // Initialize everything when DOM is loaded
    document.addEventListener('DOMContentLoaded', function() {
        initializeTheme();
        initializeSidebar();

        // Mobile menu toggle
        const mobileMenuButton = document.getElementById('mobile-menu-button');
        const mobileMenu = document.getElementById('mobile-menu');

        if (mobileMenuButton && mobileMenu) {
            mobileMenuButton.addEventListener('click', function() {
                mobileMenu.classList.toggle('hidden');
            });
        }
    });
</script>
