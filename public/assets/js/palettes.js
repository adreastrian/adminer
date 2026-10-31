/* Palette catalog shared by the early theme script and Arc controls. */
(function () {
    'use strict';
    const names = ['canvas', 'surface', 'raised', 'hover', 'border', 'text', 'muted', 'primary', 'on-primary', 'focus', 'success', 'warning', 'danger', 'danger-bg', 'string'];
    function palette(id, label, kind, colors) {
        const tokens = Object.fromEntries(names.map((name, index) => [name, colors[index]]));
        tokens.shadow = kind === 'light' ? '0 16px 40px #0000001f, 0 2px 8px #00000014' : '0 16px 40px #00000055, 0 2px 8px #00000033';
        return { id, label, kind, tokens };
    }
    window.adminerPalettes = [
        {
            id: 'catppuccin', label: 'Catppuccin', defaultLight: 'frappe', defaultDark: 'macchiato',
            palettes: [
                palette('latte', 'Latte', 'light', ['#eff1f5','#e6e9ef','#eff1f5','#dce0e8','#bcc0cc','#4c4f69','#5c5f77','#8839ef','#ffffff','#1e66f5','#3e7015','#8b6100','#d20f39','#f7dfe6','#8b6100']),
                palette('frappe', 'Frappé', 'dark', ['#303446','#292c3c','#414559','#51576d','#51576d','#c6d0f5','#b5bfe2','#ca9ee6','#303446','#8caaee','#a6d189','#e5c890','#e78284','#483542','#e5c890']),
                palette('macchiato', 'Macchiato', 'dark', ['#24273a','#1e2030','#363a4f','#494d64','#494d64','#cad3f5','#b8c0e0','#c6a0f6','#24273a','#8aadf4','#a6da95','#eed49f','#ed8796','#3b2d40','#eed49f']),
                palette('mocha', 'Mocha', 'dark', ['#1e1e2e','#181825','#313244','#45475a','#45475a','#cdd6f4','#bac2de','#cba6f7','#1e1e2e','#89b4fa','#a6e3a1','#f9e2af','#f38ba8','#3c293b','#f9e2af'])
            ]
        },
        {
            id: 'one', label: 'One', defaultLight: 'one-light', defaultDark: 'one-dark',
            palettes: [
                palette('one-light', 'One Light', 'light', ['#fafafa','#f0f0f0','#ffffff','#e5e5e6','#c5c7cc','#3e4451','#696c77','#2c5fc9','#ffffff','#4078f2','#367334','#906000','#b63a31','#fae6e4','#906000']),
                palette('one-dark', 'One Dark', 'dark', ['#282c34','#21252b','#2c313a','#3e4451','#4b5363','#abb2bf','#abb2bf','#61afef','#21252b','#61afef','#98c379','#e5c07b','#e9959c','#402c33','#e5c07b'])
            ]
        },
        {
            id: 'ayu', label: 'Ayu', defaultLight: 'ayu-light', defaultDark: 'ayu-dark',
            palettes: [
                palette('ayu-light', 'Light', 'light', ['#f8f9fa','#eef0f2','#ffffff','#e7eaed','#c0c6ce','#5c6166','#5c6166','#985500','#ffffff','#176b9e','#4b7016','#895d00','#ba4334','#fde6df','#895d00']),
                palette('ayu-mirage', 'Mirage', 'dark', ['#1f2430','#191e2a','#272d38','#343c4a','#465062','#cccac2','#b3b4b0','#ffcc66','#1f2430','#73d0ff','#d5ff80','#ffcc66','#f29b8a','#422e35','#d5ff80']),
                palette('ayu-dark', 'Dark', 'dark', ['#0b0e14','#080b10','#141922','#222a35','#354151','#bfbdb6','#aaa9a3','#e6b450','#0b0e14','#59c2ff','#aad94c','#e6b450','#f07178','#352128','#aad94c'])
            ]
        },
        {
            id: 'monokai', label: 'Monokai', defaultLight: 'monokai-paper', defaultDark: 'monokai-classic',
            palettes: [
                palette('monokai-paper', 'Paper (light companion)', 'light', ['#faf9f2','#efeee5','#fffef8','#e5e3d8','#c6c3b5','#3e3d32','#666451','#a84400','#ffffff','#186b7e','#4f6e15','#7c6400','#ad2755','#fae6eb','#7c6400']),
                palette('monokai-classic', 'Classic', 'dark', ['#272822','#1e1f1c','#3e3d32','#49483e','#626051','#f8f8f2','#b8b6a5','#fd971f','#272822','#66d9ef','#a6e22e','#e6db74','#ff75a4','#432932','#e6db74'])
            ]
        },
        {
            id: 'nord', label: 'Nord', defaultLight: 'nord-light', defaultDark: 'nord-dark',
            palettes: [
                palette('nord-light', 'Snow Storm', 'light', ['#eceff4','#e5e9f0','#f8f9fc','#d8dee9','#b8c2d1','#2e3440','#4c566a','#425f83','#ffffff','#425f83','#47652e','#805a12','#a33c48','#f7e1e5','#805a12']),
                palette('nord-dark', 'Polar Night', 'dark', ['#2e3440','#272d38','#3b4252','#434c5e','#4c566a','#eceff4','#d8dee9','#88c0d0','#2e3440','#81a1c1','#a3be8c','#ebcb8b','#e59aa3','#482e3b','#ebcb8b'])
            ]
        },
        {
            id: 'solarized', label: 'Solarized', defaultLight: 'solarized-light', defaultDark: 'solarized-dark',
            palettes: [
                palette('solarized-light', 'Light', 'light', ['#fdf6e3','#eee8d5','#fdf6e3','#e3dcc8','#b6ae99','#073642','#586e75','#176b9e','#ffffff','#176b9e','#627000','#856100','#bd322b','#f9e2d3','#856100']),
                palette('solarized-dark', 'Dark', 'dark', ['#002b36','#00252e','#073642','#164653','#35616a','#eee8d5','#93a1a1','#6db9e7','#002b36','#6db9e7','#b0b94a','#d9ae41','#ff8b7b','#3d3035','#d9ae41'])
            ]
        },
        {
            id: 'gruvbox', label: 'Gruvbox', defaultLight: 'gruvbox-light', defaultDark: 'gruvbox-dark',
            palettes: [
                palette('gruvbox-light', 'Light', 'light', ['#fbf1c7','#ebdbb2','#f9f5d7','#e1d3ac','#bdae93','#3c3836','#665c54','#8f3f71','#ffffff','#076678','#586300','#805200','#9d0006','#f4d9b7','#805200']),
                palette('gruvbox-dark', 'Dark', 'dark', ['#282828','#1d2021','#3c3836','#504945','#665c54','#ebdbb2','#bdae93','#fabd2f','#282828','#83a598','#b8bb26','#fabd2f','#fb8574','#452f2b','#fabd2f'])
            ]
        },
        {
            id: 'tokyo-night', label: 'Tokyo Night', defaultLight: 'tokyo-day', defaultDark: 'tokyo-night',
            palettes: [
                palette('tokyo-day', 'Day', 'light', ['#e1e2e7','#d0d5e3','#eef0f5','#c4c8da','#a8aecb','#343b58','#4d5a87','#6840ac','#ffffff','#2e5eaa','#446527','#805c2a','#a62f38','#f1dce3','#805c2a']),
                palette('tokyo-night', 'Night', 'dark', ['#1a1b26','#16161e','#24283b','#2a2f41','#414868','#c0caf5','#a9b1d6','#7aa2f7','#1a1b26','#7dcfff','#9ece6a','#e0af68','#f7768e','#3c283e','#e0af68'])
            ]
        }
    ];
})();
