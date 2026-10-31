<?php

class Appearance extends \Adminer\Plugin
{
    public function css()
    {
        // The appearance controller owns both color modes, not Adminer's dark CSS.
        return [
            $this->assetUrl('build/arc-ui.css') => 'light',
            $this->assetUrl('css/adminer.css') => 'light',
        ];
    }

    public function head($dark = null)
    {
        foreach (['workspace', 'palettes', 'appearance', 'sidebar'] as $script) {
            echo '<script' . \Adminer\nonce() . ' src="' . \Adminer\h($this->assetUrl("js/$script.js")) . '"></script>';
        }
        echo '<script' . \Adminer\nonce() . ' defer src="' . \Adminer\h($this->assetUrl('build/arc-ui.js')) . '"></script>';
    }

    private function assetUrl($path)
    {
        $asset = 'assets/' . $path;
        return $asset . '?v=' . filemtime(dirname(__DIR__, 2) . '/public/' . $asset);
    }

    public function navigation($missing)
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
                    <label for="appearance-scheme">Theme family</label>
                    <select id="appearance-scheme" data-arc-native>
                        <option value="catppuccin">Catppuccin</option>
                    </select>
                    <p class="appearance-hint">Choose a palette for each mode. A dark palette can also be used in the light slot.</p>
                </div>
            </details>
        </div>
        HTML;
    }
}
