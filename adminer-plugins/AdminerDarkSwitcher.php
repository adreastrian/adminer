<?php

class AdminerDarkSwitcher extends \Adminer\Plugin
{
    public function head($dark = null)
    {
        $root = dirname(__DIR__);
        echo '<script' . \Adminer\nonce() . ' src="js/palettes.js?v=' . filemtime($root . '/js/palettes.js') . '"></script>';
        $appearanceVersion = filemtime($root . '/js/appearance.js');
        echo '<script' . \Adminer\nonce() . ' src="js/appearance.js?v=' . $appearanceVersion . '"></script>';
        echo '<script' . \Adminer\nonce() . ' src="js/sidebar.js?v=' . filemtime($root . '/js/sidebar.js') . '"></script>';
        // Production assets are committed, so PHP deployments do not need Node.
        if (is_file($root . '/assets/arc-ui.js') && is_file($root . '/assets/arc-ui.css')) {
            echo '<link rel="stylesheet" href="assets/arc-ui.css?v=' . filemtime($root . '/assets/arc-ui.css') . '">';
            echo '<script' . \Adminer\nonce() . ' defer src="assets/arc-ui.js?v=' . filemtime($root . '/assets/arc-ui.js') . '"></script>';
        }
    }

    public function navigation($missing)
    {
        $this->renderThemeDropdown();
    }

    public function renderThemeDropdown()
    {
        echo <<<'HTML'
        <div class="custom-nav-menu">
            <details class="appearance-settings">
                <summary>Appearance</summary>
                <div id="arc-appearance">
                    <label for="appearance-mode">Mode</label>
                    <select id="appearance-mode" data-arc-native>
                        <option value="light">Light</option>
                        <option value="dark">Dark</option>
                        <option value="system" selected>System</option>
                    </select>
                    <label for="appearance-scheme">Color scheme</label>
                    <select id="appearance-scheme" data-arc-native>
                        <option value="catppuccin">Catppuccin Frappé / Macchiato</option>
                        <option value="catppuccin-latte">Catppuccin Latte / Mocha</option>
                        <option value="nord">Nord</option>
                        <option value="solarized">Solarized</option>
                    </select>
                    <p class="appearance-hint">Frappé is a lighter dark palette. Choose Latte / Mocha for a true light mode.</p>
                </div>
            </details>
        </div>
        HTML;
    }
}
