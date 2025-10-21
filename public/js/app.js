document.addEventListener('DOMContentLoaded', function () {

    async function loadPage(url, pushState = true) {
        try {
            document.body.classList.add('loading');

            const response = await fetch(url, {
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'text/html' }
            });

            if (!response.ok) throw new Error('Network response not ok');

            const html = await response.text();


            const mainContent = document.getElementById('main-content');
            mainContent.innerHTML = html;

            // Update active sidebar link
            updateActiveSidebarLink(url);

            // Push browser history
            if (pushState) history.pushState(null, '', url);

            // Reinitialize collapsibles
            initializeCollapsibles();

            // Scroll to top
            window.scrollTo(0, 0);

            console.log('✅ AJAX content loaded from', url);

        } catch (err) {
            console.error('AJAX navigation error:', err);
            window.location.href = url; // fallback
        } finally {
            document.body.classList.remove('loading');
        }
    }

    document.addEventListener('click', function (e) {
        const link = e.target.closest('a.ajax-link');
        if (!link) return;
        const href = link.getAttribute('href');
        if (!href || href === '#') return;
        e.preventDefault();
        loadPage(href);
    });

    window.addEventListener('popstate', function () {
        loadPage(window.location.href, false);
    });

    function initializeCollapsibles() {
        const collapsibles = document.querySelectorAll('[data-collapsible]');
        const savedState = JSON.parse(localStorage.getItem('sidebar-collapsible')) || {};

        collapsibles.forEach(collapsible => {
            const key = collapsible.dataset.collapsible;
            const content = collapsible.querySelector('[data-collapsible-content]');
            const button = collapsible.querySelector('[data-collapsible-button]');
            if (!content || !button) return;

            if (savedState[key] === true) content.classList.remove('hidden');
            else content.classList.add('hidden');

            button.replaceWith(button.cloneNode(true));
            const newButton = collapsible.querySelector('[data-collapsible-button]');
            newButton.addEventListener('click', () => {
                const isOpen = !content.classList.contains('hidden');
                content.classList.toggle('hidden');
                savedState[key] = !isOpen;
                localStorage.setItem('sidebar-collapsible', JSON.stringify(savedState));
            });
        });
    }

    function updateActiveSidebarLink(url) {
        const normalizedUrl = url.replace(/\/+$/, '');
        document.querySelectorAll('#sidebar-container a.ajax-link').forEach(link => {
            const linkUrl = link.href.replace(/\/+$/, '');
            if (linkUrl === normalizedUrl) {
                link.classList.add(
                    'bg-indigo-100','text-indigo-700',
                    'dark:bg-indigo-800','dark:text-white',
                    'font-semibold'
                );
            } else {
                link.classList.remove(
                    'bg-indigo-100','text-indigo-700',
                    'dark:bg-indigo-800','dark:text-white',
                    'font-semibold'
                );
            }
        });
    }

    // Initial setup
    initializeCollapsibles();
    updateActiveSidebarLink(window.location.href);

});
