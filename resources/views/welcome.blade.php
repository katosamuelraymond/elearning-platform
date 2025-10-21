@extends('layouts.app')

@section('title', 'Welcome - Lincoln eLearning')

@section('content')
<div class="min-h-screen bg-white dark:bg-gray-900 transition-colors duration-300">

   <section class="relative overflow-hidden h-screen">
    <div class="carousel-container relative h-full w-full">
        <!-- Slide 1 - Left Aligned -->
        <div class="carousel-slide absolute inset-0 transition-opacity duration-1000 ease-in-out opacity-100">
            <div class="absolute inset-0 bg-gradient-to-r from-blue-900/70 to-blue-900/20 z-10"></div>
            <img src="/images/banner1.jpg"
                 alt="Lincoln High School Campus"
                 class="w-full h-full object-cover">
            <div class="absolute inset-0 z-20 flex items-center">
                <div class="container mx-auto px-6">
                    <div class="max-w-2xl bg-black/30 backdrop-blur-sm rounded-2xl p-8 md:p-12 ml-0 md:ml-8">
                        <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold text-white mb-4 leading-tight">
                            Excellence in Education
                        </h1>
                        <p class="text-xl text-blue-100 mb-8 leading-relaxed">
                            Uganda's premier A & O Level digital learning platform with UNEB-approved resources
                        </p>
                        <div class="flex flex-col sm:flex-row gap-4">
                            <a href="#" class="bg-white text-blue-700 px-8 py-4 rounded-lg font-semibold hover:bg-blue-50 transition-all duration-300 text-center shadow-lg">
                                <i class="fas fa-book-open mr-2"></i>Explore Programs
                            </a>
                            <a href="#" class="border-2 border-white text-white px-8 py-4 rounded-lg font-semibold hover:bg-white/10 transition-all duration-300 text-center">
                                <i class="fas fa-play-circle mr-2"></i>Virtual Tour
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Slide 2 - Right Aligned -->
        <div class="carousel-slide absolute inset-0 transition-opacity duration-1000 ease-in-out opacity-0">
            <div class="absolute inset-0 bg-gradient-to-l from-green-900/70 to-green-900/20 z-10"></div>
            <img src="/images/banner2.jpg"
                 alt="Students Learning"
                 class="w-full h-full object-cover">
            <div class="absolute inset-0 z-20 flex items-center">
                <div class="container mx-auto px-6">
                    <div class="max-w-2xl bg-black/30 backdrop-blur-sm rounded-2xl p-8 md:p-12 ml-auto mr-0 md:mr-8">
                        <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold text-white mb-4 leading-tight text-right">
                            Proven Academic Success
                        </h1>
                        <p class="text-xl text-green-100 mb-8 leading-relaxed text-right">
                            94% UNEB pass rate with comprehensive resources and expert instruction
                        </p>
                        <div class="flex flex-col sm:flex-row gap-4 justify-end">
                            <a href="#" class="bg-white text-green-700 px-8 py-4 rounded-lg font-semibold hover:bg-green-50 transition-all duration-300 text-center shadow-lg">
                                <i class="fas fa-chart-line mr-2"></i>View Results
                            </a>
                            <a href="#" class="border-2 border-white text-white px-8 py-4 rounded-lg font-semibold hover:bg-white/10 transition-all duration-300 text-center">
                                <i class="fas fa-users mr-2"></i>Meet Our Teachers
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Slide 3 - Bottom Left -->
        <div class="carousel-slide absolute inset-0 transition-opacity duration-1000 ease-in-out opacity-0">
            <div class="absolute inset-0 bg-gradient-to-t from-purple-900/70 to-purple-900/10 z-10"></div>
            <img src="https://images.unsplash.com/photo-1522202176988-66273c2fd55f?ixlib=rb-4.0.3&auto=format&fit=crop&w=2071&q=80"
                 alt="Modern Classroom"
                 class="w-full h-full object-cover">
            <div class="absolute inset-0 z-20 flex items-end pb-16 md:pb-24">
                <div class="container mx-auto px-6">
                    <div class="max-w-2xl bg-black/30 backdrop-blur-sm rounded-2xl p-8 md:p-12 ml-0 md:ml-8">
                        <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold text-white mb-4 leading-tight">
                            Modern Learning Environment
                        </h1>
                        <p class="text-xl text-purple-100 mb-8 leading-relaxed">
                            State-of-the-art facilities combined with innovative teaching methods
                        </p>
                        <div class="flex flex-col sm:flex-row gap-4">
                            <a href="#" class="bg-white text-purple-700 px-8 py-4 rounded-lg font-semibold hover:bg-purple-50 transition-all duration-300 text-center shadow-lg">
                                <i class="fas fa-building mr-2"></i>Our Facilities
                            </a>
                            <a href="#" class="border-2 border-white text-white px-8 py-4 rounded-lg font-semibold hover:bg-white/10 transition-all duration-300 text-center">
                                <i class="fas fa-user-graduate mr-2"></i>Admissions
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Slide 4 - Top Right -->
        <div class="carousel-slide absolute inset-0 transition-opacity duration-1000 ease-in-out opacity-0">
            <div class="absolute inset-0 bg-gradient-to-b from-orange-900/70 to-orange-900/10 z-10"></div>
            <img src="https://images.unsplash.com/photo-1541339907198-e08756dedf3f?ixlib=rb-4.0.3&auto=format&fit=crop&w=2070&q=80"
                 alt="E-learning Platform"
                 class="w-full h-full object-cover">
            <div class="absolute inset-0 z-20 flex items-start pt-16 md:pt-24">
                <div class="container mx-auto px-6">
                    <div class="max-w-2xl bg-black/30 backdrop-blur-sm rounded-2xl p-8 md:p-12 ml-auto mr-0 md:mr-8">
                        <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold text-white mb-4 leading-tight text-right">
                            Comprehensive eLearning
                        </h1>
                        <p class="text-xl text-orange-100 mb-8 leading-relaxed text-right">
                            Access UNEB past papers, video lessons, and study resources anytime, anywhere
                        </p>
                        <div class="flex flex-col sm:flex-row gap-4 justify-end">
                            <a href="#" class="bg-white text-orange-700 px-8 py-4 rounded-lg font-semibold hover:bg-orange-50 transition-all duration-300 text-center shadow-lg">
                                <i class="fas fa-laptop mr-2"></i>Access Portal
                            </a>
                            <a href="#" class="border-2 border-white text-white px-8 py-4 rounded-lg font-semibold hover:bg-white/10 transition-all duration-300 text-center">
                                <i class="fas fa-mobile-alt mr-2"></i>Mobile App
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Navigation Arrows -->
    <button class="carousel-prev absolute left-4 top-1/2 transform -translate-y-1/2 z-30 bg-white/20 hover:bg-white/30 text-white w-12 h-12 rounded-full flex items-center justify-center transition-all duration-300 backdrop-blur-sm">
        <i class="fas fa-chevron-left text-xl"></i>
    </button>
    <button class="carousel-next absolute right-4 top-1/2 transform -translate-y-1/2 z-30 bg-white/20 hover:bg-white/30 text-white w-12 h-12 rounded-full flex items-center justify-center transition-all duration-300 backdrop-blur-sm">
        <i class="fas fa-chevron-right text-xl"></i>
    </button>

    <!-- Indicators -->
    <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 z-30 flex space-x-3">
        <button class="carousel-indicator w-3 h-3 rounded-full bg-white/70 hover:bg-white transition-all duration-300 active-indicator" data-slide="0"></button>
        <button class="carousel-indicator w-3 h-3 rounded-full bg-white/50 hover:bg-white transition-all duration-300" data-slide="1"></button>
        <button class="carousel-indicator w-3 h-3 rounded-full bg-white/50 hover:bg-white transition-all duration-300" data-slide="2"></button>
        <button class="carousel-indicator w-3 h-3 rounded-full bg-white/50 hover:bg-white transition-all duration-300" data-slide="3"></button>
    </div>
</section>

    <!-- School Introduction -->
    <section class="py-16 bg-white dark:bg-gray-900">
        <div class="container mx-auto px-6">

            <div class="flex flex-col lg:flex-row items-center gap-12">
                <div class="lg:w-1/2">
                    <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-6">Welcome to Lincoln High School</h2>
                    <p class="text-gray-600 dark:text-gray-300 mb-6 leading-relaxed">
                        Established in 1995, Lincoln High School has been at the forefront of secondary education in Uganda,
                        providing exceptional academic programs for both O-Level and A-Level students. Our commitment to
                        excellence has consistently produced outstanding UNEB results year after year.
                    </p>
                    <p class="text-gray-600 dark:text-gray-300 mb-8 leading-relaxed">
                        With our innovative eLearning platform, we extend our quality education beyond the physical classroom,
                        offering comprehensive resources, expert instruction, and personalized support to help every student
                        achieve their full potential.
                    </p>

                    <div class="grid grid-cols-2 gap-6 mt-8">
                        <div class="flex items-center">
                            <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900 rounded-lg flex items-center justify-center mr-4">
                                <i class="fas fa-award text-blue-600 dark:text-blue-400"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-gray-900 dark:text-white">UNEB Excellence</h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Consistent top results</p>
                            </div>
                        </div>
                        <div class="flex items-center">
                            <div class="w-12 h-12 bg-green-100 dark:bg-green-900 rounded-lg flex items-center justify-center mr-4">
                                <i class="fas fa-users text-green-600 dark:text-green-400"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-gray-900 dark:text-white">Expert Teachers</h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Qualified & experienced</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="lg:w-1/2">
                    <div class="bg-gray-100 dark:bg-gray-800 rounded-2xl overflow-hidden">
                        <img src="/images/building.jpg" alt="Lincoln High School Campus" class="w-full h-64 object-cover">
                        <div class="p-6">
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Our Campus</h3>
                            <p class="text-gray-600 dark:text-gray-300 mb-4">
                                Located in a serene environment conducive to learning, with modern facilities including
                                science laboratories, computer labs, and a well-stocked library.
                            </p>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Announcements -->
    <div class="bg-blue-600 text-white py-3">
        <div class="container mx-auto px-6">
            <div class="flex flex-col md:flex-row items-center justify-between space-y-2 md:space-y-0">
                <div class="flex items-center space-x-2">
                    <i class="fas fa-info-circle"></i>
                    <span class="font-medium">UNEB Registration Deadline: March 30th, 2024</span>
                </div>
                <a href="#" class="underline hover:no-underline flex items-center">
                    Details <i class="fas fa-arrow-right ml-1 text-sm"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Academic Programs -->
    <section class="py-16 bg-gray-50 dark:bg-gray-800">
    <div class="container mx-auto px-6">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-4">Academic Programs</h2>
            <p class="text-gray-600 dark:text-gray-300 max-w-2xl mx-auto">
                Comprehensive UNEB-approved curriculum designed to prepare students for academic success
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 max-w-5xl mx-auto">
            <!-- O-Level Program -->
            <div class="bg-white dark:bg-gray-900 rounded-xl p-6 border border-gray-200 dark:border-gray-700 shadow-sm flex flex-col">
                <div class="flex items-center mb-6">
                    <div class="w-12 h-12 bg-blue-600 rounded-lg flex items-center justify-center">
                        <i class="fas fa-school text-white text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white">O-Level (S1-S4)</h3>
                        <p class="text-blue-600 dark:text-blue-400 text-sm">Ordinary Level Program</p>
                    </div>
                </div>

                <div class="mb-6 flex-grow">
                    <h4 class="font-semibold text-gray-900 dark:text-white mb-3">Core Subjects</h4>
                    <div class="flex flex-wrap gap-2 mb-4">
                        <span class="bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 px-3 py-1 rounded-full text-sm">English Language</span>
                        <span class="bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 px-3 py-1 rounded-full text-sm">Mathematics</span>
                        <span class="bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 px-3 py-1 rounded-full text-sm">Physics</span>
                        <span class="bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 px-3 py-1 rounded-full text-sm">Chemistry</span>
                        <span class="bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 px-3 py-1 rounded-full text-sm">Biology</span>
                        <span class="bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 px-3 py-1 rounded-full text-sm">Geography</span>
                        <span class="bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 px-3 py-1 rounded-full text-sm">History</span>
                        <span class="bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 px-3 py-1 rounded-full text-sm">CRE/IRE</span>
                    </div>

                    <h4 class="font-semibold text-gray-900 dark:text-white mb-3">Elective Subjects</h4>
                    <div class="flex flex-wrap gap-2 mb-4">
                        <span class="bg-purple-100 dark:bg-purple-900 text-purple-800 dark:text-purple-200 px-3 py-1 rounded-full text-sm">Computer Studies</span>
                        <span class="bg-purple-100 dark:bg-purple-900 text-purple-800 dark:text-purple-200 px-3 py-1 rounded-full text-sm">Agriculture</span>
                        <span class="bg-purple-100 dark:bg-purple-900 text-purple-800 dark:text-purple-200 px-3 py-1 rounded-full text-sm">Commerce</span>
                        <span class="bg-purple-100 dark:bg-purple-900 text-purple-800 dark:text-purple-200 px-3 py-1 rounded-full text-sm">Art & Design</span>
                        <span class="bg-purple-100 dark:bg-purple-900 text-purple-800 dark:text-purple-200 px-3 py-1 rounded-full text-sm">Music</span>
                        <span class="bg-purple-100 dark:bg-purple-900 text-purple-800 dark:text-purple-200 px-3 py-1 rounded-full text-sm">French</span>
                        <span class="bg-purple-100 dark:bg-purple-900 text-purple-800 dark:text-purple-200 px-3 py-1 rounded-full text-sm">Literature</span>
                        <span class="bg-purple-100 dark:bg-purple-900 text-purple-800 dark:text-purple-200 px-3 py-1 rounded-full text-sm">Entrepreneurship</span>
                    </div>

                    <h4 class="font-semibold text-gray-900 dark:text-white mb-3">Program Features</h4>
                    <div class="space-y-2">
                        <div class="flex items-center text-sm text-gray-600 dark:text-gray-300">
                            <i class="fas fa-check-circle text-green-500 mr-2"></i>
                            <span>4-year comprehensive curriculum</span>
                        </div>
                        <div class="flex items-center text-sm text-gray-600 dark:text-gray-300">
                            <i class="fas fa-check-circle text-green-500 mr-2"></i>
                            <span>Practical laboratory sessions</span>
                        </div>
                        <div class="flex items-center text-sm text-gray-600 dark:text-gray-300">
                            <i class="fas fa-check-circle text-green-500 mr-2"></i>
                            <span>Career guidance from S2</span>
                        </div>
                    </div>
                </div>


            </div>

            <!-- A-Level Program -->
            <div class="bg-white dark:bg-gray-900 rounded-xl p-6 border border-gray-200 dark:border-gray-700 shadow-sm flex flex-col">
                <div class="flex items-center mb-6">
                    <div class="w-12 h-12 bg-purple-600 rounded-lg flex items-center justify-center">
                        <i class="fas fa-university text-white text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white">A-Level (S5-S6)</h3>
                        <p class="text-purple-600 dark:text-purple-400 text-sm">Advanced Level Program</p>
                    </div>
                </div>

                <div class="mb-6 flex-grow">
                    <h4 class="font-semibold text-gray-900 dark:text-white mb-3">Science Combinations</h4>
                    <div class="flex flex-wrap gap-2 mb-4">
                        <span class="bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 px-3 py-1 rounded-full text-sm">PCM (Physics, Chemistry, Math)</span>
                        <span class="bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 px-3 py-1 rounded-full text-sm">PCB (Physics, Chemistry, Biology)</span>
                        <span class="bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 px-3 py-1 rounded-full text-sm">MEG (Math, Economics, Geography)</span>
                        <span class="bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 px-3 py-1 rounded-full text-sm">BCM (Biology, Chemistry, Math)</span>
                    </div>

                    <h4 class="font-semibold text-gray-900 dark:text-white mb-3">Arts Combinations</h4>
                    <div class="flex flex-wrap gap-2 mb-4">
                        <span class="bg-purple-100 dark:bg-purple-900 text-purple-800 dark:text-purple-200 px-3 py-1 rounded-full text-sm">HEG (History, Economics, Geography)</span>
                        <span class="bg-purple-100 dark:bg-purple-900 text-purple-800 dark:text-purple-200 px-3 py-1 rounded-full text-sm">LED (Literature, Economics, Divinity)</span>
                        <span class="bg-purple-100 dark:bg-purple-900 text-purple-800 dark:text-purple-200 px-3 py-1 rounded-full text-sm">LFA (Literature, Fine Art, Divinity)</span>
                        <span class="bg-purple-100 dark:bg-purple-900 text-purple-800 dark:text-purple-200 px-3 py-1 rounded-full text-sm">GPA (Geography, Physics, Agriculture)</span>
                    </div>


                </div>


            </div>
        </div>


    </div>
</section>

    <!-- Learning Resources -->
    <section class="py-16 bg-white dark:bg-gray-900">
    <div class="container mx-auto px-6">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-4">Learning Resources</h2>
            <p class="text-gray-600 dark:text-gray-300">Comprehensive materials to support your academic journey</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-5xl mx-auto">
            <!-- Past Papers -->
            <div class="bg-gray-50 dark:bg-gray-800 rounded-xl p-8 border border-gray-200 dark:border-gray-700 text-center hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1">
                <div class="w-20 h-20 bg-blue-600 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-file-pdf text-white text-3xl"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Past Papers</h3>
                <p class="text-gray-600 dark:text-gray-300 mb-6 leading-relaxed">
                    10+ years of UNEB examination papers with detailed marking guides and examiner reports
                </p>
                <div class="flex flex-col gap-3">
                    <span class="bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 px-4 py-2 rounded-full text-sm font-medium">
                        2013-2023 Collection
                    </span>
                    <span class="bg-blue-50 dark:bg-blue-950 text-blue-700 dark:text-blue-300 px-4 py-2 rounded-full text-sm">
                        All Subjects Available
                    </span>
                </div>
            </div>

            <!-- Video Lessons -->
            <div class="bg-gray-50 dark:bg-gray-800 rounded-xl p-8 border border-gray-200 dark:border-gray-700 text-center hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1">
                <div class="w-20 h-20 bg-purple-600 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-video text-white text-3xl"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Video Lessons</h3>
                <p class="text-gray-600 dark:text-gray-300 mb-6 leading-relaxed">
                    Expert explanations of complex topics by subject specialists with interactive demonstrations
                </p>
                <div class="flex flex-col gap-3">
                    <span class="bg-purple-100 dark:bg-purple-900 text-purple-800 dark:text-purple-200 px-4 py-2 rounded-full text-sm font-medium">
                        500+ Video Library
                    </span>
                    <span class="bg-purple-50 dark:bg-purple-950 text-purple-700 dark:text-purple-300 px-4 py-2 rounded-full text-sm">
                        Downloadable Content
                    </span>
                </div>
            </div>

            <!-- Study Notes -->
            <div class="bg-gray-50 dark:bg-gray-800 rounded-xl p-8 border border-gray-200 dark:border-gray-700 text-center hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1">
                <div class="w-20 h-20 bg-red-600 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-book text-white text-3xl"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Study Notes</h3>
                <p class="text-gray-600 dark:text-gray-300 mb-6 leading-relaxed">
                    Comprehensive topic summaries, revision guides, and quick reference materials for all subjects
                </p>
                <div class="flex flex-col gap-3">
                    <span class="bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200 px-4 py-2 rounded-full text-sm font-medium">
                        All Subjects Covered
                    </span>
                    <span class="bg-red-50 dark:bg-red-950 text-red-700 dark:text-red-300 px-4 py-2 rounded-full text-sm">
                        Updated Annually
                    </span>
                </div>
            </div>
        </div>


    </div>
</section>

    <!-- School Events -->
    <section class="py-16 bg-gray-50 dark:bg-gray-800">
    <div class="container mx-auto px-6">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-4">School Events</h2>
            <p class="text-gray-600 dark:text-gray-300">Upcoming activities and events at Lincoln High School</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 max-w-6xl mx-auto">
            <!-- Sports Day Event -->
            <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1">
                <div class="h-48 relative overflow-hidden">
                    <img src="/images/sports.jpg"
                         alt="Sports Day Event"
                         class="w-full h-full object-cover transition-transform duration-300 hover:scale-105">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent"></div>
                    <div class="absolute top-4 left-4 bg-white text-blue-700 px-3 py-2 rounded-lg font-bold shadow-lg">
                        <span class="block text-sm leading-none">MAR</span>
                        <span class="block text-xl leading-none mt-1">15</span>
                    </div>
                </div>
                <div class="p-6">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3">Annual Sports Day</h3>
                    <p class="text-gray-600 dark:text-gray-300 text-sm mb-4 leading-relaxed">
                        Inter-house competition featuring track and field events, with students competing for the championship trophy.
                    </p>
                    <div class="flex justify-between items-center pt-4 border-t border-gray-100 dark:border-gray-700">
                        <div class="flex items-center text-blue-600 dark:text-blue-400 text-sm">
                            <i class="fas fa-map-marker-alt mr-2"></i>
                            <span>Main Field</span>
                        </div>

                    </div>
                </div>
            </div>

            <!-- Career Day Event -->
            <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1">
                <div class="h-48 relative overflow-hidden">
                    <img src="/images/career.jpg"
                         alt="Career Guidance Day"
                         class="w-full h-full object-cover transition-transform duration-300 hover:scale-105">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent"></div>
                    <div class="absolute top-4 left-4 bg-white text-purple-700 px-3 py-2 rounded-lg font-bold shadow-lg">
                        <span class="block text-sm leading-none">APR</span>
                        <span class="block text-xl leading-none mt-1">05</span>
                    </div>
                </div>
                <div class="p-6">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3">Career Guidance Day</h3>
                    <p class="text-gray-600 dark:text-gray-300 text-sm mb-4 leading-relaxed">
                        University representatives and career experts provide guidance on future career paths and higher education opportunities.
                    </p>
                    <div class="flex justify-between items-center pt-4 border-t border-gray-100 dark:border-gray-700">
                        <div class="flex items-center text-purple-600 dark:text-purple-400 text-sm">
                            <i class="fas fa-map-marker-alt mr-2"></i>
                            <span>School Hall</span>
                        </div>

                    </div>
                </div>
            </div>

            <!-- Science Fair Event -->
            <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1">
                <div class="h-48 relative overflow-hidden">
                    <img src="https://images.unsplash.com/photo-1532094349884-543bc11b234d?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80"
                         alt="Science & Innovation Fair"
                         class="w-full h-full object-cover transition-transform duration-300 hover:scale-105">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent"></div>
                    <div class="absolute top-4 left-4 bg-white text-green-700 px-3 py-2 rounded-lg font-bold shadow-lg">
                        <span class="block text-sm leading-none">MAY</span>
                        <span class="block text-xl leading-none mt-1">10</span>
                    </div>
                </div>
                <div class="p-6">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3">Science & Innovation Fair</h3>
                    <p class="text-gray-600 dark:text-gray-300 text-sm mb-4 leading-relaxed">
                        Student project exhibition showcasing scientific innovations, experiments, and creative solutions to real-world problems.
                    </p>
                    <div class="flex justify-between items-center pt-4 border-t border-gray-100 dark:border-gray-700">
                        <div class="flex items-center text-green-600 dark:text-green-400 text-sm">
                            <i class="fas fa-map-marker-alt mr-2"></i>
                            <span>Science Block</span>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <!-- View All Events Button -->
        <div class="text-center mt-12">
            <button class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-lg font-semibold transition-colors flex items-center justify-center mx-auto">
                <i class="fas fa-calendar-alt mr-2"></i>
                View All Events
            </button>
        </div>
    </div>
</section>

    <!-- Final CTA -->
    <section class="py-16 bg-gradient-to-r from-blue-700 to-purple-800 text-white">
        <div class="container mx-auto px-6 text-center">
            <h2 class="text-3xl font-bold mb-4">Ready to Begin Your Academic Journey?</h2>
            <p class="max-w-2xl mx-auto mb-8 text-blue-100">
                Join Lincoln High School's digital learning platform and access comprehensive A & O Level resources designed for academic excellence.
            </p>

            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                @auth
                    <a href="{{ route('student.dashboard') }}"
                       class="bg-white text-blue-700 px-8 py-3 rounded-lg font-semibold transition-all shadow-sm hover:bg-blue-50">
                        Access Learning Portal
                    </a>
                @else
                    <a href=""
                       class="bg-white text-blue-700 px-8 py-3 rounded-lg font-semibold transition-all shadow-sm hover:bg-blue-50">
                        Student Login
                    </a>
                    <a href=""
                       class="bg-transparent border border-white text-white px-8 py-3 rounded-lg font-semibold transition-all hover:bg-white/10">
                        Contact Admissions
                    </a>
                @endauth
            </div>

            <div class="mt-8 flex justify-center space-x-6">
                <div class="flex items-center">
                    <i class="fas fa-phone-alt mr-2 text-blue-200"></i>
                    <span class="text-blue-100">+256 123 456 789</span>
                </div>
                <div class="flex items-center">
                    <i class="fas fa-envelope mr-2 text-blue-200"></i>
                    <span class="text-blue-100">info@lincolnhigh.ac.ug</span>
                </div>
                <div class="flex items-center">
                    <i class="fas fa-map-marker-alt mr-2 text-blue-200"></i>
                    <span class="text-blue-100">Kampala, Uganda</span>
                </div>
            </div>
        </div>
    </section>




<!-- Carousel JavaScript -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const slides = document.querySelectorAll('.carousel-slide');
        const indicators = document.querySelectorAll('.carousel-indicator');
        const prevButton = document.querySelector('.carousel-prev');
        const nextButton = document.querySelector('.carousel-next');

        let currentSlide = 0;
        let autoSlideInterval;

        // Function to show a specific slide
        function showSlide(index) {
            // Hide all slides
            slides.forEach(slide => {
                slide.classList.remove('opacity-100');
                slide.classList.add('opacity-0');
            });

            // Remove active class from all indicators
            indicators.forEach(indicator => {
                indicator.classList.remove('bg-white/70', 'active-indicator');
                indicator.classList.add('bg-white/50');
            });

            // Show the selected slide
            slides[index].classList.remove('opacity-0');
            slides[index].classList.add('opacity-100');

            // Update the active indicator
            indicators[index].classList.remove('bg-white/50');
            indicators[index].classList.add('bg-white/70', 'active-indicator');

            currentSlide = index;
        }

        // Function to go to next slide
        function nextSlide() {
            const nextIndex = (currentSlide + 1) % slides.length;
            showSlide(nextIndex);
        }

        // Function to go to previous slide
        function prevSlide() {
            const prevIndex = (currentSlide - 1 + slides.length) % slides.length;
            showSlide(prevIndex);
        }

        // Start auto-sliding
        function startAutoSlide() {
            autoSlideInterval = setInterval(nextSlide, 5000); // Change slide every 5 seconds
        }

        // Stop auto-sliding (when user interacts)
        function stopAutoSlide() {
            clearInterval(autoSlideInterval);
        }

        // Event listeners for navigation
        nextButton.addEventListener('click', function() {
            stopAutoSlide();
            nextSlide();
            startAutoSlide();
        });

        prevButton.addEventListener('click', function() {
            stopAutoSlide();
            prevSlide();
            startAutoSlide();
        });

        // Event listeners for indicators
        indicators.forEach((indicator, index) => {
            indicator.addEventListener('click', function() {
                stopAutoSlide();
                showSlide(index);
                startAutoSlide();
            });
        });

        // Pause auto-slide on hover
        const carouselContainer = document.querySelector('.carousel-container');
        carouselContainer.addEventListener('mouseenter', stopAutoSlide);
        carouselContainer.addEventListener('mouseleave', startAutoSlide);

        // Initialize auto-sliding
        startAutoSlide();
    });
</script>

<style>
    /* Additional custom styles for perfect balance */
    .carousel-slide {
        transition: opacity 1s ease-in-out;
    }

    .active-indicator {
        transform: scale(1.2);
    }

    /* Ensure full viewport coverage */
    .h-screen {
        height: 100vh;
    }

    /* Smooth transitions */
    .carousel-container {
        overflow: hidden;
    }
</style>
@endsection
