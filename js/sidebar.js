/* Collapsible Adminer shell. Existing links, forms and table filters stay intact. */
(function () {
    'use strict';
    const root = document.documentElement;
    const media = window.matchMedia('(max-width: 800px)');
    const params = new URLSearchParams(location.search);
    let workspaceOpen = ['sql', 'import', 'dump', 'create'].some(key => params.has(key));
    root.dataset.page = params.has('select') ? 'select' : 'other';
    root.dataset.workspaceTools = workspaceOpen ? 'open' : 'closed';
    root.classList.add('shell-ready', 'shell-pending');
    let expanded = true;
    try { expanded = localStorage.getItem('adminer_sidebar') !== 'collapsed'; } catch (_) {}
    root.dataset.sidebar = media.matches || !expanded ? 'collapsed' : 'expanded';

    document.addEventListener('DOMContentLoaded', function () {
        const menu = document.getElementById('menu');
        const content = document.getElementById('content');
        const foot = document.getElementById('foot');
        if (!menu || !content || !foot) {
            root.classList.remove('shell-ready', 'shell-pending');
            return;
        }
        let mobileOpen = false;
        let workspaceTools;
        const tables = document.getElementById('tables');
        const scrollKey = 'adminer_table_scroll:' + [params.get('server'), params.get('username'), params.get('db')].join('|');
        let tableScroll = 0;
        try { tableScroll = Math.max(0, Number(sessionStorage.getItem(scrollKey)) || 0); } catch (_) {}
        const rememberTableScroll = () => {
            if (!tables?.getClientRects().length) return;
            tableScroll = tables.scrollTop;
            try { sessionStorage.setItem(scrollKey, String(tableScroll)); } catch (_) {}
        };
        tables?.addEventListener('scroll', rememberTableScroll, { passive: true });
        window.addEventListener('pagehide', rememberTableScroll);
        const isMac = /Mac|iPhone|iPad/.test(navigator.platform);
        const shortcut = isMac ? '⌘B' : 'Ctrl+B';
        const icon = open => '<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="4" width="18" height="16" rx="3"/><path d="M9 4v16"/><path d="' + (open ? 'M16 9l-3 3 3 3' : 'M13 9l3 3-3 3') + '"/></svg>';
        const makeButton = (className, label, markup) => {
            const button = document.createElement('button');
            button.type = 'button';
            button.className = className;
            button.setAttribute('aria-label', label);
            button.title = label;
            button.innerHTML = markup;
            return button;
        };
        const toolbar = document.createElement('div');
        toolbar.className = 'shell-toolbar';
        const toggle = makeButton('shell-toggle', 'Collapse sidebar', icon(true));
        toggle.setAttribute('aria-controls', 'menu');
        toolbar.appendChild(toggle);
        const breadcrumb = document.getElementById('breadcrumb');
        if (breadcrumb) toolbar.appendChild(breadcrumb);
        else {
            const title = document.createElement('span');
            title.className = 'shell-location';
            title.textContent = 'Database workspace';
            toolbar.appendChild(title);
        }
        content.prepend(toolbar);

        const brand = document.createElement('div');
        brand.className = 'shell-brand';
        const heading = menu.querySelector('h1');
        if (heading) brand.appendChild(heading);
        const close = makeButton('shell-toggle shell-close', 'Collapse sidebar', icon(true));
        close.setAttribute('aria-controls', 'menu');
        brand.appendChild(close);
        menu.prepend(brand);
        menu.setAttribute('aria-label', 'Database navigation');

        const backdrop = document.createElement('button');
        backdrop.type = 'button';
        backdrop.className = 'shell-backdrop';
        backdrop.setAttribute('aria-label', 'Close sidebar');
        backdrop.tabIndex = -1;
        document.body.appendChild(backdrop);

        function render() {
            const open = media.matches ? mobileOpen : expanded;
            root.dataset.sidebar = open ? 'expanded' : 'collapsed';
            root.dataset.sidebarOverlay = media.matches && mobileOpen ? 'open' : 'closed';
            if (workspaceTools) workspaceTools.open = !open || workspaceOpen;
            if (open && tables) tables.scrollTop = tableScroll;
            for (const button of [toggle, close]) {
                const label = open ? 'Collapse sidebar' : 'Expand sidebar';
                button.setAttribute('aria-expanded', String(open));
                button.setAttribute('aria-label', label);
                button.setAttribute('aria-keyshortcuts', isMac ? 'Meta+b' : 'Control+b');
                button.title = label + ' (' + shortcut + ')';
                button.innerHTML = icon(open);
            }
            const overlayOpen = media.matches && mobileOpen;
            backdrop.hidden = !overlayOpen;
            content.inert = overlayOpen;
            menu.setAttribute('role', overlayOpen ? 'dialog' : 'navigation');
            if (overlayOpen) menu.setAttribute('aria-modal', 'true');
            else menu.removeAttribute('aria-modal');
            if (media.matches) foot.classList.toggle('foot', !mobileOpen);
        }
        function setExpanded(value, restoreFocus = false) {
            if (media.matches) mobileOpen = value;
            else {
                expanded = value;
                try { localStorage.setItem('adminer_sidebar', value ? 'expanded' : 'collapsed'); } catch (_) {}
            }
            render();
            if (restoreFocus) (media.matches && mobileOpen ? close : toggle).focus();
        }
        function toggleSidebar(event) {
            event.stopPropagation();
            setExpanded(!(media.matches ? mobileOpen : expanded), true);
        }
        toggle.addEventListener('click', toggleSidebar);
        close.addEventListener('click', toggleSidebar);
        backdrop.addEventListener('click', () => setExpanded(false, true));
        // Capture before Adminer's body shortcuts; leave editing and IME input alone.
        document.addEventListener('keydown', event => {
            if (event.repeat || event.isComposing || event.altKey || event.shiftKey ||
                event.key.toLowerCase() !== 'b' ||
                !(isMac ? event.metaKey && !event.ctrlKey : event.ctrlKey && !event.metaKey)) return;
            if (event.composedPath().some(node => node instanceof Element &&
                (node.matches('input, textarea, select, [role="textbox"], [role="combobox"], [role="listbox"], [role="option"]') || node.isContentEditable))) return;
            event.preventDefault();
            toggleSidebar(event);
        }, true);

        const databaseButton = makeButton('shell-rail-action shell-database-action', 'Choose database', '<span aria-hidden="true"></span>');
        const tablesButton = makeButton('shell-rail-action shell-tables-action', 'Find a table', '<span aria-hidden="true"></span>');
        const dbs = document.getElementById('dbs');
        const navigation = menu.querySelector('p.links');
        if (dbs || navigation) {
            workspaceTools = document.createElement('details');
            workspaceTools.className = 'workspace-tools';
            const summary = document.createElement('summary');
            const databaseLabel = document.createElement('span');
            databaseLabel.textContent = params.get('db') || 'Database tools';
            summary.appendChild(databaseLabel);
            summary.setAttribute('aria-label', 'Database tools' + (params.get('db') ? ': ' + params.get('db') : ''));
            summary.title = 'Choose database, SQL, import, export, and create table';
            (dbs || navigation).before(workspaceTools);
            workspaceTools.appendChild(summary);
            if (dbs) workspaceTools.appendChild(dbs);
            if (navigation) workspaceTools.appendChild(navigation);
            workspaceTools.addEventListener('toggle', () => {
                if (root.dataset.sidebar === 'expanded') {
                    workspaceOpen = workspaceTools.open;
                    root.dataset.workspaceTools = workspaceOpen ? 'open' : 'closed';
                }
            });
            workspaceTools.before(databaseButton);
        }
        if (!dbs) databaseButton.remove();
        const filter = document.getElementById('filter-field');
        if (filter) {
            filter.placeholder = 'Find a table…';
            filter.setAttribute('aria-label', 'Find a table');
            const filterRow = filter.closest('p');
            filterRow?.classList.add('shell-table-filter');
            filterRow?.before(tablesButton);
        }
        databaseButton.addEventListener('click', event => {
            event.stopPropagation();
            workspaceOpen = true;
            root.dataset.workspaceTools = 'open';
            setExpanded(true);
            requestAnimationFrame(() => dbs?.querySelector('[role="combobox"], select')?.focus());
        });
        tablesButton.addEventListener('click', event => {
            event.stopPropagation();
            setExpanded(true);
            requestAnimationFrame(() => filter?.focus());
        });

        menu.querySelectorAll('p.links a').forEach(link => {
            const label = link.textContent.trim();
            link.setAttribute('aria-label', label);
            if (link.classList.contains('active')) link.setAttribute('aria-current', 'page');
            link.title = label;
            const text = document.createElement('span');
            text.className = 'shell-nav-label';
            text.append(...link.childNodes);
            link.appendChild(text);
        });
        menu.querySelectorAll('#tables li').forEach(row => {
            const name = row.querySelector('a.structure');
            const browse = row.querySelector('a.select');
            if (name) {
                name.title = name.textContent.trim();
                if (name.classList.contains('active')) name.setAttribute('aria-current', 'page');
            }
            if (browse) {
                const label = 'Browse ' + (name?.textContent.trim() || 'table');
                browse.setAttribute('aria-label', label);
                if (browse.classList.contains('active')) browse.setAttribute('aria-current', 'page');
                browse.title = label;
            }
        });
        const settings = menu.querySelector('.appearance-settings');
        const languageForm = document.getElementById('lang')?.closest('form');
        if (settings && languageForm && !languageForm.contains(settings)) settings.appendChild(languageForm);
        const summary = settings?.querySelector('summary');
        if (summary) {
            summary.title = 'Appearance';
            summary.addEventListener('click', event => {
                if (root.dataset.sidebar === 'collapsed') {
                    event.preventDefault();
                    setExpanded(true);
                    settings.open = true;
                    summary.focus();
                }
            });
        }
        const logout = menu.querySelector('.logout input[type="submit"]');
        if (logout) { logout.title = logout.value || 'Logout'; logout.setAttribute('aria-label', logout.value || 'Logout'); }

        // Adminer closes #foot on outside clicks. Keep that behavior for the mobile overlay.
        new MutationObserver(() => {
            if (media.matches && mobileOpen && foot.classList.contains('foot')) {
                mobileOpen = false;
                render();
            }
        }).observe(foot, { attributes: true, attributeFilter: ['class'] });
        menu.addEventListener('keydown', event => {
            if (!media.matches || !mobileOpen || event.defaultPrevented) return;
            if (document.querySelector('[role="combobox"][aria-expanded="true"]')) return;
            if (event.key === 'Escape') {
                event.stopPropagation();
                setExpanded(false, true);
            } else if (event.key === 'Tab') {
                const stops = Array.from(menu.querySelectorAll('a[href], button, input, select, textarea, summary, [tabindex]'))
                    .filter(element => element.tabIndex >= 0 && !element.disabled && element.getClientRects().length && !element.closest('[inert]'));
                const first = stops[0];
                const last = stops[stops.length - 1];
                if (event.shiftKey && document.activeElement === first) { event.preventDefault(); last?.focus(); }
                else if (!event.shiftKey && document.activeElement === last) { event.preventDefault(); first?.focus(); }
            }
        });
        media.addEventListener('change', () => { mobileOpen = false; render(); });
        if (root.dataset.page === 'select') {
            const queryForm = content.querySelector(':scope > form#form');
            if (queryForm) {
                const tools = document.createElement('div');
                tools.className = 'query-toolbar';
                queryForm.before(tools);
                const disclose = (node, label, className) => {
                    const details = document.createElement('details');
                    details.className = className;
                    const summary = document.createElement('summary');
                    summary.textContent = label;
                    details.append(summary, node);
                    tools.appendChild(details);
                    return details;
                };
                const filters = [...params.keys()].filter(key => /^where\[\d+\]\[col\]$/.test(key) && params.get(key)).length;
                const queryDetails = disclose(queryForm, 'Query options' + (filters ? ' · ' + filters + (filters === 1 ? ' filter' : ' filters') : ''), 'query-controls');
                queryForm.addEventListener('invalid', () => { queryDetails.open = true; }, true);
                content.addEventListener('click', event => {
                    const link = event.target.closest('a[href^="#fieldset-"]');
                    if (link) queryDetails.open = true;
                }, true);
                if (location.hash.startsWith('#fieldset-') || queryForm.querySelector('.error')) queryDetails.open = true;
            }
        }
        render();
        root.classList.remove('shell-pending');
        requestAnimationFrame(() => {
            if (tables && root.dataset.sidebar === 'expanded') tables.scrollTop = tableScroll;
            requestAnimationFrame(() => root.classList.add('shell-animate'));
        });
    });
})();
