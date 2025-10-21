@php
    $user = auth()->user();
@endphp
<aside id="sidebar-container"
       class="w-64 bg-gradient-to-b from-gray-50 to-white dark:from-gray-800 dark:to-gray-900 border-r border-gray-200 dark:border-gray-700
              transition-all duration-300 lg:block
              h-[calc(100vh-4rem)] overflow-y-auto sticky top-16 scrollbar-hide
              shadow-lg dark:shadow-xl">
    <div class="p-6">
        <!-- Header -->
        <div class="mb-8">
            <h2 class="text-xl font-bold text-gray-800 dark:text-white mb-2 pb-3 border-b-2 border-indigo-500 dark:border-indigo-400">
                Navigation
            </h2>
            <p class="text-sm text-gray-500 dark:text-gray-400">
                Welcome back, {{ $user->name }}
            </p>
        </div>

        <nav class="space-y-1">
            @if($user)
                @switch($user->primaryRole)
                    {{-- ================= STUDENT ================= --}}
                    @case('student')
                        <x-partials.sidebar-link
                            href="{{ route('student.dashboard') }}"
                            label="Dashboard"
                            icon="fas fa-gauge-high text-blue-500 dark:text-blue-400"
                            class="ajax-link"
                        />

                        <x-partials.sidebar-collapsible label="Subjects" icon="fas fa-book-open text-emerald-500 dark:text-emerald-400">
                            <x-partials.sidebar-link
                                href="{{ route('student.subjects.index') }}"
                                label="All Subjects"
                                icon="fas fa-bookmark text-emerald-400 dark:text-emerald-300 text-xs"
                                class="ajax-link"
                            />
                        </x-partials.sidebar-collapsible>

                        {{-- Combined Assessment Section --}}
                        <x-partials.sidebar-collapsible label="Assessment" icon="fas fa-clipboard-check text-purple-500 dark:text-purple-400">
                            {{-- Assignments Section --}}
                            <div class="ml-2 mb-2">
                                <div class="flex items-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1">
                                    <i class="fas fa-file-pen text-orange-400 dark:text-orange-300 mr-2 text-xs"></i>
                                    Assignments
                                </div>
                                <x-partials.sidebar-link
                                    href="{{ route('student.assignments.index') }}"
                                    label="My Assignments"
                                    icon="fas fa-list-check text-orange-400 dark:text-orange-300 text-xs"
                                    class="ajax-link ml-4"
                                />
                            </div>

                            {{-- Exams Section --}}
                            <div class="ml-2 mb-2">
                                <div class="flex items-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1">
                                    <i class="fas fa-file-signature text-red-400 dark:text-red-300 mr-2 text-xs"></i>
                                    Exams
                                </div>
                                <x-partials.sidebar-link
                                    href="{{ route('student.exams.index') }}"
                                    label="My Exams"
                                    icon="fas fa-file-lines text-red-400 dark:text-red-300 text-xs"
                                    class="ajax-link ml-4"
                                />
                            </div>

                            {{-- Quizzes Section --}}
                            <div class="ml-2">
                                <div class="flex items-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1">
                                    <i class="fas fa-puzzle-piece text-indigo-400 dark:text-indigo-300 mr-2 text-xs"></i>
                                    Quizzes
                                </div>
                                <x-partials.sidebar-link
                                    href="{{ route('student.quizzes.index') }}"
                                    label="My Quizzes"
                                    icon="fas fa-clock text-indigo-400 dark:text-indigo-300 text-xs"
                                    class="ajax-link ml-4"
                                />
                            </div>
                        </x-partials.sidebar-collapsible>

                        <x-partials.sidebar-link
                            href="{{ route('student.grades.index') }}"
                            label="Grades & Analytics"
                            icon="fas fa-chart-line text-green-500 dark:text-green-400"
                            class="ajax-link"
                        />

                        <x-partials.sidebar-link
                            href="{{ route('student.resources.index') }}"
                            label="Resources"
                            icon="fas fa-folder-open text-amber-500 dark:text-amber-400"
                            class="ajax-link"
                        />
                        @break

                    {{-- ================= TEACHER ================= --}}
                    @case('teacher')
                        <x-partials.sidebar-link
                            href="{{ route('teacher.dashboard') }}"
                            label="Dashboard"
                            icon="fas fa-gauge-high text-blue-500 dark:text-blue-400"
                            class="ajax-link"
                        />

                        <x-partials.sidebar-collapsible label="Subjects" icon="fas fa-book-open-reader text-emerald-500 dark:text-emerald-400">
                            <x-partials.sidebar-link
                                href="{{ route('teacher.subjects.index') }}"
                                label="My Subjects"
                                icon="fas fa-bookmark text-emerald-400 dark:text-emerald-300 text-xs"
                                class="ajax-link"
                            />
                        </x-partials.sidebar-collapsible>

                        {{-- Combined Assessment Section --}}
                        <x-partials.sidebar-collapsible label="Assessment" icon="fas fa-clipboard-check text-purple-500 dark:text-purple-400">
                            {{-- Assignments Section --}}
                            <div class="ml-2 mb-3">
                                <div class="flex items-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-2">
                                    <i class="fas fa-file-pen text-orange-400 dark:text-orange-300 mr-2 text-xs"></i>
                                    Assignments
                                </div>
                                <x-partials.sidebar-link
                                    href="{{ route('teacher.assignments.index') }}"
                                    label="All Assignments"
                                    icon="fas fa-list-check text-orange-400 dark:text-orange-300 text-xs"
                                    class="ajax-link ml-4"
                                />
                                <x-partials.sidebar-link
                                    href="{{ route('teacher.assignments.create') }}"
                                    label="Create Assignment"
                                    icon="fas fa-plus-circle text-orange-300 dark:text-orange-200 text-xs"
                                    class="ajax-link ml-4"
                                />
                            </div>

                            {{-- Exams Section --}}
                            <div class="ml-2 mb-3">
                                <div class="flex items-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-2">
                                    <i class="fas fa-file-signature text-red-400 dark:text-red-300 mr-2 text-xs"></i>
                                    Exams
                                </div>
                                <x-partials.sidebar-link
                                    href="{{ route('teacher.exams.index') }}"
                                    label="All Exams"
                                    icon="fas fa-file-lines text-red-400 dark:text-red-300 text-xs"
                                    class="ajax-link ml-4"
                                />
                                <x-partials.sidebar-link
                                    href="{{ route('teacher.exams.create') }}"
                                    label="Create Exam"
                                    icon="fas fa-plus-circle text-red-300 dark:text-red-200 text-xs"
                                    class="ajax-link ml-4"
                                />
                            </div>

                            {{-- Quizzes Section --}}
                            <div class="ml-2">
                                <div class="flex items-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-2">
                                    <i class="fas fa-puzzle-piece text-indigo-400 dark:text-indigo-300 mr-2 text-xs"></i>
                                    Quizzes
                                </div>
                                <x-partials.sidebar-link
                                    href="{{ route('teacher.quizzes.index') }}"
                                    label="All Quizzes"
                                    icon="fas fa-clock text-indigo-400 dark:text-indigo-300 text-xs"
                                    class="ajax-link ml-4"
                                />
                                <x-partials.sidebar-link
                                    href="{{ route('teacher.quizzes.create') }}"
                                    label="Create Quiz"
                                    icon="fas fa-plus-circle text-indigo-300 dark:text-indigo-200 text-xs"
                                    class="ajax-link ml-4"
                                />
                            </div>
                        </x-partials.sidebar-collapsible>

                        <x-partials.sidebar-link
                            href="{{ route('teacher.grades.index') }}"
                            label="Grades & Analytics"
                            icon="fas fa-chart-simple text-green-500 dark:text-green-400"
                            class="ajax-link"
                        />

                        <x-partials.sidebar-link
                            href="{{ route('teacher.resources.index') }}"
                            label="Resources"
                            icon="fas fa-folder-tree text-amber-500 dark:text-amber-400"
                            class="ajax-link"
                        />
                        @break

                    {{-- ================= ADMIN ================= --}}
                    @case('admin')
                        <x-partials.sidebar-link
                            href="{{ route('admin.dashboard') }}"
                            label="Dashboard"
                            icon="fas fa-gauge-high text-blue-500 dark:text-blue-400"
                            class="ajax-link"
                        />

                        <x-partials.sidebar-collapsible label="Subjects" icon="fas fa-book-open text-emerald-500 dark:text-emerald-400">
                            <x-partials.sidebar-link
                                href="{{ route('admin.subjects.index') }}"
                                label="All Subjects"
                                icon="fas fa-bookmark text-emerald-400 dark:text-emerald-300 text-xs"
                                class="ajax-link"
                            />
                            <x-partials.sidebar-link
                                href="{{ route('admin.subjects.create') }}"
                                label="Add New"
                                icon="fas fa-plus-circle text-emerald-300 dark:text-emerald-200 text-xs"
                                class="ajax-link"
                            />
                        </x-partials.sidebar-collapsible>

                        {{-- Combined Assessment Section --}}
                        <x-partials.sidebar-collapsible label="Assessment" icon="fas fa-clipboard-check text-purple-500 dark:text-purple-400">
                            {{-- Assignments Section --}}
                            <div class="ml-2 mb-3">
                                <div class="flex items-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-2">
                                    <i class="fas fa-file-pen text-orange-400 dark:text-orange-300 mr-2 text-xs"></i>
                                    Assignments
                                </div>
                                <x-partials.sidebar-link
                                    href="{{ route('admin.assignments.index') }}"
                                    label="All Assignments"
                                    icon="fas fa-list-check text-orange-400 dark:text-orange-300 text-xs"
                                    class="ajax-link ml-4"
                                />
                                <x-partials.sidebar-link
                                    href="{{ route('admin.assignments.create') }}"
                                    label="Create Assignment"
                                    icon="fas fa-plus-circle text-orange-300 dark:text-orange-200 text-xs"
                                    class="ajax-link ml-4"
                                />
                            </div>

                            {{-- Exams Section --}}
                            <div class="ml-2 mb-3">
                                <div class="flex items-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-2">
                                    <i class="fas fa-file-signature text-red-400 dark:text-red-300 mr-2 text-xs"></i>
                                    Exams
                                </div>
                                <x-partials.sidebar-link
                                    href="{{ route('admin.exams.index') }}"
                                    label="All Exams"
                                    icon="fas fa-file-lines text-red-400 dark:text-red-300 text-xs"
                                    class="ajax-link ml-4"
                                />
                                <x-partials.sidebar-link
                                    href="{{ route('admin.exams.create') }}"
                                    label="Create Exam"
                                    icon="fas fa-plus-circle text-red-300 dark:text-red-200 text-xs"
                                    class="ajax-link ml-4"
                                />
                            </div>

                            {{-- Quizzes Section --}}
                            <div class="ml-2">
                                <div class="flex items-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-2">
                                    <i class="fas fa-puzzle-piece text-indigo-400 dark:text-indigo-300 mr-2 text-xs"></i>
                                    Quizzes
                                </div>
                                <x-partials.sidebar-link
                                    href="{{ route('admin.quizzes.index') }}"
                                    label="All Quizzes"
                                    icon="fas fa-clock text-indigo-400 dark:text-indigo-300 text-xs"
                                    class="ajax-link ml-4"
                                />
                                <x-partials.sidebar-link
                                    href="{{ route('admin.quizzes.create') }}"
                                    label="Create Quiz"
                                    icon="fas fa-plus-circle text-indigo-300 dark:text-indigo-200 text-xs"
                                    class="ajax-link ml-4"
                                />
                            </div>
                        </x-partials.sidebar-collapsible>

                        <x-partials.sidebar-collapsible label="Grades & Analytics" icon="fas fa-chart-simple text-green-500 dark:text-green-400">
                            <x-partials.sidebar-link
                                href="{{ route('admin.grades.index') }}"
                                label="All Grades"
                                icon="fas fa-chart-column text-green-400 dark:text-green-300 text-xs"
                                class="ajax-link"
                            />
                        </x-partials.sidebar-collapsible>

                        <x-partials.sidebar-collapsible label="Resources" icon="fas fa-folder-tree text-amber-500 dark:text-amber-400">
                            <x-partials.sidebar-link
                                href="{{ route('admin.resources.index') }}"
                                label="All Resources"
                                icon="fas fa-folder-open text-amber-400 dark:text-amber-300 text-xs"
                                class="ajax-link"
                            />
                            <x-partials.sidebar-link
                                href="{{ route('admin.resources.upload') }}"
                                label="Upload Resource"
                                icon="fas fa-cloud-arrow-up text-amber-300 dark:text-amber-200 text-xs"
                                class="ajax-link"
                            />
                        </x-partials.sidebar-collapsible>

                        <x-partials.sidebar-collapsible label="User Management" icon="fas fa-users-gear text-cyan-500 dark:text-cyan-400">
                            <x-partials.sidebar-link
                                href="{{ route('admin.users.index') }}"
                                label="All Users"
                                icon="fas fa-user-group text-cyan-400 dark:text-cyan-300 text-xs"
                                class="ajax-link"
                            />
                            <x-partials.sidebar-link
                                href="{{ route('admin.users.create') }}"
                                label="Add User"
                                icon="fas fa-user-plus text-cyan-300 dark:text-cyan-200 text-xs"
                                class="ajax-link"
                            />
                        </x-partials.sidebar-collapsible>

                        @break

                    @default
                        {{-- Default links if role unknown --}}
                @endswitch
            @endif

            {{-- Common links --}}
            <div class="pt-4 mt-4 border-t border-gray-200 dark:border-gray-700">
                <x-partials.sidebar-link
                    href="{{ route($user->routeRole . '.profile') }}"
                    label="Profile"
                    icon="fas fa-user-circle text-violet-500 dark:text-violet-400"
                    class="ajax-link"
                />
                <x-partials.sidebar-link
                    href="{{ route($user->routeRole . '.settings') }}"
                    label="Settings"
                    icon="fas fa-sliders text-gray-500 dark:text-gray-400"
                    class="ajax-link"
                />
            </div>
        </nav>

        {{-- Quick Stats --}}
        <div class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-600">
            <h3 class="text-sm font-semibold text-gray-500 dark:text-gray-400 mb-4 uppercase tracking-wide">Quick Stats</h3>
            <div class="space-y-3">
                <div class="flex justify-between items-center text-sm p-2 rounded-lg bg-blue-50 dark:bg-blue-900/20">
                    <span class="text-gray-600 dark:text-gray-300 flex items-center">
                        <i class="fas fa-book mr-2 text-blue-500"></i>
                        Active Courses
                    </span>
                    <span class="font-bold text-blue-600 dark:text-blue-400 bg-white dark:bg-blue-800 px-2 py-1 rounded text-xs">5</span>
                </div>
                <div class="flex justify-between items-center text-sm p-2 rounded-lg bg-orange-50 dark:bg-orange-900/20">
                    <span class="text-gray-600 dark:text-gray-300 flex items-center">
                        <i class="fas fa-clipboard-check mr-2 text-orange-500"></i>
                        Pending Assessments
                    </span>
                    <span class="font-bold text-orange-600 dark:text-orange-400 bg-white dark:bg-orange-800 px-2 py-1 rounded text-xs">3</span>
                </div>
                <div class="flex justify-between items-center text-sm p-2 rounded-lg bg-green-50 dark:bg-green-900/20">
                    <span class="text-gray-600 dark:text-gray-300 flex items-center">
                        <i class="fas fa-chart-line mr-2 text-green-500"></i>
                        Avg. Grade
                    </span>
                    <span class="font-bold text-green-600 dark:text-green-400 bg-white dark:bg-green-800 px-2 py-1 rounded text-xs">87%</span>
                </div>
            </div>
        </div>

         <div class="mt-8 pt-6 border-t border-gray-100 dark:border-gray-700">
            <div class="text-center">
                <div class="w-8 h-8 bg-gradient-to-r from-indigo-500 to-purple-600 rounded-lg mx-auto mb-2 flex items-center justify-center shadow-md">
                    <i class="fas fa-graduation-cap text-white text-sm"></i>
                </div>
                <p class="text-xs text-gray-500 dark:text-gray-400">EduPlatform v2.0</p>
                <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">© 2024 All rights reserved</p>
            </div>
        </div>
    </div>
</aside>
