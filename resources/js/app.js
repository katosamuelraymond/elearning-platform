import './bootstrap';


import Alpine from 'alpinejs';
import focus from '@alpinejs/focus';

Alpine.plugin(focus);
// Alpine.start();


window.Alpine = Alpine;


window.loadContent = async function(url, title = null) {
    try {

        const mainContent = document.getElementById('main-content');
        if (mainContent) {
            mainContent.innerHTML = `
                <div class="flex justify-center items-center h-64">
                    <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600"></div>
                </div>
            `;
        }

        const response = await fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'text/html, application/xhtml+xml'
            }
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const html = await response.text();

        if (mainContent) {
            mainContent.innerHTML = html;


            if (title) {
                window.history.pushState({}, title, url);
                document.title = `${title} - Lincoln eLearning`;
            }

            initializeComponents();
        }
    } catch (error) {
        console.error('Error loading content:', error);
        const mainContent = document.getElementById('main-content');
        if (mainContent) {
            mainContent.innerHTML = `
                <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800">Error loading content</h3>
                            <div class="mt-2 text-sm text-red-700">
                                <p>Please try again or refresh the page.</p>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }
    }
};


window.initializeComponents = function() {

    Alpine.start();
};


window.addEventListener('popstate', function(event) {
    loadContent(window.location.pathname + window.location.search);
});

document.addEventListener('DOMContentLoaded', function() {
    initializeComponents();
});
