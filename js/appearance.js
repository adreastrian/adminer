/* Shared palette state for the early page paint, native fallback and Arc controls. */
(function () {
    'use strict';
    const catalog = window.adminerPalettes;
    if (!Array.isArray(catalog) || !catalog.length) return;
    const modes = ['light', 'dark', 'system'];
    const media = window.matchMedia('(prefers-color-scheme: light)');
    const root = document.documentElement;
    const storageKeys = ['adminer_dark_mode', 'adminer_color_scheme', 'adminer_palette_pairs'];
    let mode;
    let scheme;
    let pairs;

    function read(key) {
        try { return localStorage.getItem(key); } catch (_) { return null; }
    }
    function persist(key, value) {
        try { localStorage.setItem(key, value); } catch (_) { /* In-memory choices still work. */ }
    }
    function provider(id) { return catalog.find(item => item.id === id); }
    function validPalette(family, id) { return family.palettes.some(palette => palette.id === id); }
    function restore() {
        const savedMode = read(storageKeys[0]);
        mode = modes.includes(savedMode) ? savedMode : 'system';
        const savedScheme = read(storageKeys[1]);
        scheme = savedScheme === 'catppuccin-latte' ? 'catppuccin' : provider(savedScheme)?.id || 'catppuccin';
        let savedPairs = null;
        try { savedPairs = JSON.parse(read(storageKeys[2])); } catch (_) {}
        pairs = Object.create(null);
        catalog.forEach(family => {
            const saved = savedPairs && typeof savedPairs === 'object' ? savedPairs[family.id] : null;
            pairs[family.id] = {
                light: validPalette(family, saved?.light) ? saved.light : family.defaultLight,
                dark: validPalette(family, saved?.dark) ? saved.dark : family.defaultDark
            };
        });
        if (savedScheme === 'catppuccin-latte') {
            if (!savedPairs?.catppuccin) pairs.catppuccin = { light: 'latte', dark: 'mocha' };
            persist(storageKeys[1], scheme);
            persist(storageKeys[2], JSON.stringify(pairs));
        }
    }
    function getState() {
        return {
            mode, scheme,
            lightPalette: pairs[scheme].light,
            darkPalette: pairs[scheme].dark,
            resolvedMode: mode === 'system' ? (media.matches ? 'light' : 'dark') : mode
        };
    }
    function populate(select, items, value, signature) {
        if (!select) return;
        if (select.dataset.catalog !== signature) {
            select.replaceChildren(...items.map(item => new Option(item.label, item.id)));
            select.dataset.catalog = signature;
        }
        select.value = value;
    }
    function syncNative(state) {
        const nativeMode = document.getElementById('appearance-mode');
        if (nativeMode) nativeMode.value = state.mode;
        populate(document.getElementById('appearance-scheme'), catalog, state.scheme, 'providers');
        const family = provider(state.scheme);
        const options = family.palettes.map(palette => ({ id: palette.id, label: palette.label + ' (' + palette.kind + ')' }));
        populate(document.getElementById('appearance-light-palette'), options, state.lightPalette, family.id);
        populate(document.getElementById('appearance-dark-palette'), options, state.darkPalette, family.id);
        const note = document.querySelector('#arc-appearance > .appearance-hint');
        if (note) note.textContent = 'Choose a palette for each mode. A dark palette can also be used in the light slot.';
    }
    function apply() {
        const state = getState();
        const selected = provider(scheme).palettes.find(palette => palette.id === pairs[scheme][state.resolvedMode]);
        Object.entries(selected.tokens).forEach(([name, value]) => root.style.setProperty('--arc-' + name, value));
        root.style.colorScheme = selected.kind;
        root.classList.toggle('light-theme', selected.kind === 'light');
        root.dataset.colorScheme = scheme;
        root.dataset.palette = selected.id;
        root.dataset.theme = selected.kind;
        root.dataset.appearance = state.resolvedMode;
        syncNative(state);
        document.dispatchEvent(new CustomEvent('adminer:appearance', { detail: state }));
    }

    restore();
    window.adminerAppearance = {
        getState,
        setMode: function (value) {
            if (!modes.includes(value)) return;
            mode = value;
            persist(storageKeys[0], mode);
            apply();
        },
        setScheme: function (value) {
            if (!provider(value)) return;
            scheme = value;
            persist(storageKeys[1], scheme);
            apply();
        },
        setPalette: function (slot, value) {
            if (!['light', 'dark'].includes(slot) || !validPalette(provider(scheme), value)) return;
            pairs[scheme][slot] = value;
            persist(storageKeys[2], JSON.stringify(pairs));
            apply();
        }
    };
    apply();
    media.addEventListener('change', function () { if (mode === 'system') apply(); });
    window.addEventListener('storage', function (event) {
        if (event.key !== null && !storageKeys.includes(event.key)) return;
        restore();
        apply();
    });
    document.addEventListener('DOMContentLoaded', function () {
        const fallback = document.getElementById('arc-appearance');
        ['light', 'dark'].forEach(slot => {
            const id = 'appearance-' + slot + '-palette';
            if (!fallback || document.getElementById(id)) return;
            const label = document.createElement('label');
            label.htmlFor = id;
            label.textContent = (slot === 'light' ? 'Light' : 'Dark') + ' palette';
            const select = document.createElement('select');
            select.id = id;
            select.setAttribute('data-arc-native', '');
            select.addEventListener('change', event => window.adminerAppearance.setPalette(slot, event.target.value));
            const note = fallback.querySelector('.appearance-hint');
            fallback.insertBefore(label, note);
            fallback.insertBefore(select, note);
        });
        const schemeLabel = document.querySelector('label[for="appearance-scheme"]');
        if (schemeLabel) schemeLabel.textContent = 'Theme family';
        apply();
        document.getElementById('appearance-mode')?.addEventListener('change', function (event) {
            window.adminerAppearance.setMode(event.target.value);
        });
        document.getElementById('appearance-scheme')?.addEventListener('change', function (event) {
            window.adminerAppearance.setScheme(event.target.value);
        });
        const navigation = document.querySelector('.custom-nav-menu');
        const logoutForm = document.querySelector('.logout')?.closest('form');
        if (navigation && logoutForm && !logoutForm.contains(navigation)) navigation.appendChild(logoutForm);
        if (navigation) document.getElementById('menu')?.appendChild(navigation);
        const settings = document.querySelector('.appearance-settings');
        settings?.addEventListener('keydown', function (event) {
            if (event.key === 'Escape' && settings.open && !document.querySelector('[role="listbox"]')) {
                settings.open = false;
                settings.querySelector('summary').focus();
            }
        });
    });
})();
