$(document).ready(function() {
    const params = new URLSearchParams(window.location.search);
    document.body.classList.toggle('import', params.has('import'));
    document.body.classList.toggle('select-data-page', params.has('select'));

    const databases = document.querySelector('#dbs select');
    if (databases) {
        Array.from(databases.options).forEach(function(option) {
            if (option.value === '' && !option.textContent.trim()) option.textContent = 'Choose database';
        });
    }

    // Overview, row-editor, and export tables need the same containment as results.
    document.querySelectorAll('#content > form > table').forEach(function(table) {
        const scroll = document.createElement('div');
        scroll.className = 'scrollable';
        table.before(scroll);
        scroll.appendChild(table);
    });

    // Make wide-table containers keyboard-scrollable without changing table semantics.
    document.querySelectorAll('#content .scrollable').forEach(function(scroll) {
        scroll.tabIndex = 0;
        scroll.setAttribute('role', 'region');
        scroll.setAttribute('aria-label', 'Database table');
    });

    // Horizontal overflow creates a sticky containing block even without vertical
    // scrolling. Track its viewport offset so the real header pins to the page.
    const headedTables = Array.from(document.querySelectorAll('#content .scrollable'))
        .map(scroll => ({ scroll, head: scroll.querySelector(':scope > table > thead') }))
        .filter(item => item.head);
    headedTables.forEach(({ scroll }) => scroll.classList.add('viewport-table'));
    let frame = 0;
    const updateHeaders = () => {
        frame = 0;
        headedTables.forEach(({ scroll, head }) => {
            const offset = Math.max(0, -scroll.getBoundingClientRect().top);
            head.style.top = offset + 'px';
            head.classList.toggle('header-pinned', offset > 0);
        });
    };
    const scheduleHeaders = () => {
        if (!frame) frame = requestAnimationFrame(updateHeaders);
    };
    window.addEventListener('scroll', scheduleHeaders, { passive: true });
    window.addEventListener('resize', scheduleHeaders);
    const resize = new ResizeObserver(scheduleHeaders);
    const content = document.getElementById('content');
    if (content) resize.observe(content);
    headedTables.forEach(({ scroll }) => resize.observe(scroll));
    updateHeaders();
});
