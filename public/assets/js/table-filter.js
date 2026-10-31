/* Filter native Adminer table names without interpreting them as HTML. */
(function () {
    'use strict';

    document.addEventListener('DOMContentLoaded', function () {
        const field = document.getElementById('filter-field');
        const list = document.getElementById('tables');
        if (!field || !list) return;

        const database = new URLSearchParams(location.search).get('db') || '';
        const entries = Array.from(list.children).map(row => {
            const label = row.querySelector('a:not(.select), span');
            return label ? { row, label, text: label.textContent.trim() } : null;
        }).filter(Boolean);
        let previousValue;
        let timeout;

        function read(key) {
            try { return localStorage.getItem(key); } catch (_) { return null; }
        }
        function persist(key, value) {
            try { localStorage.setItem(key, value); } catch (_) { /* Filtering works without storage. */ }
        }
        function filter() {
            const value = field.value.toLowerCase();
            if (value === previousValue) return;
            previousValue = value;
            persist('adminer_tables_filter', value);
            const pattern = value ? new RegExp(value.replace(/[.*+?^${}()|[\]\\]/g, '\\$&'), 'gi') : null;

            entries.forEach(({ row, label, text }) => {
                const matches = !value || text.toLowerCase().includes(value);
                row.classList.toggle('hidden', !matches);
                label.textContent = text;
                if (!pattern || !matches) return;

                const highlighted = document.createDocumentFragment();
                let offset = 0;
                for (const match of text.matchAll(pattern)) {
                    highlighted.append(text.slice(offset, match.index));
                    const strong = document.createElement('strong');
                    const italic = document.createElement('i');
                    italic.textContent = match[0];
                    strong.appendChild(italic);
                    highlighted.appendChild(strong);
                    offset = match.index + match[0].length;
                }
                highlighted.append(text.slice(offset));
                label.replaceChildren(highlighted);
            });
        }

        if (read('adminer_tables_filter_db') === database) {
            field.value = read('adminer_tables_filter') || '';
        }
        persist('adminer_tables_filter_db', database);
        filter();
        field.addEventListener('input', function () {
            clearTimeout(timeout);
            timeout = setTimeout(filter, 200);
        });
    });
})();
