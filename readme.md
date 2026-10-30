# Custom Adminer Package

Custom Adminer bundle with:

- Plugin-enabled `index.php` (Adminer 6.2.0)
- UI Arc Select, Combobox, and Segmented Control components, bundled locally
- Light, dark, and system appearance modes with saved color schemes
- Auto login support using a `.env` file

## Previous theme screenshots

These screenshots show the previous theme, not the current Arc components.

![Light theme](/screenshot-light.jpeg?raw=true "Screenshot")

![Dark theme](/screenshot-dark.jpeg?raw=true "Screenshot")

## Project Structure

- `index.php`: Main entrypoint with plugin loader enabled
- `adminer.php`: Standalone entrypoint without the custom plugin stack
- `vendor/adminer/adminer.php`: Unmodified official Adminer core
- `vendor/adminer/UPSTREAM.txt`: Release source and verified checksum
- `adminer-plugins.php`: Active plugin list and load order
- `adminer-plugins/`: Custom plugin implementations
- `.env`: Optional credentials for `AutoLogin`

## Quick Start

1. Clone/download this repository.
2. Create a local `.env` file in the project root from the example:

```bash
cp .env.example .env
```

3. Update `.env` with your own database credentials:

```dotenv
USERNAME=your_db_username
PASSWORD=your_db_password
```

4. Run a local PHP server from the project root:

```bash
php -S 127.0.0.1:8080
```

5. Open the plugin-enabled app:

`http://127.0.0.1:8080/index.php`

## AutoLogin Setup

`AutoLogin` reads credentials from `.env` in the project root.

`.env` is gitignored; commit only `.env.example`.

Requirements:

- File name must be exactly `.env`
- Keys must be exactly `USERNAME` and `PASSWORD`
- One `KEY=value` per line
- Do not place `.env` inside `adminer-plugins/`

If `.env` is missing, Adminer still works and you can log in manually.

## Enabled Plugins

The default plugin stack is configured in `adminer-plugins.php`:

- `LoadDependencies`
- `AdminerDatabaseHide`
- `AdminerEditTextarea`
- `AdminerEnumOption`
- `AdminerEditForeign`
- `AdminerTablesFilter`
- `AdminerJsonPreview`
- `AdminerCollations`
- `FixedFooter`
- `AutoLogin`
- `AdminerDarkSwitcher`

## Rebuild the theme components

The committed `assets/arc-ui.js` and `assets/arc-ui.css` files work without Node on the PHP server.

After changing files under `ui/`, rebuild the assets:

```bash
npm ci
npm run typecheck
npm run build
```

Deploy both generated assets with the PHP files, `vendor/`, the CSS files, `js/`, and `fonts/`. Both entrypoints require `vendor/`.

The component sources and their license are in `ui/arc/`. The native PHP forms still submit database requests.

## Sidebar

The toolbar toggle collapses the sidebar to an icon rail. Its arrow changes with the sidebar state. The browser remembers the desktop state across page loads.

The keyboard shortcut is **⌘B** on Mac and **Ctrl+B** elsewhere. The shortcut leaves text editing and open dropdown controls alone.

The rail keeps database selection, navigation, table search, appearance, and logout available. On narrow screens, the expanded sidebar overlays the workspace and closes with Escape or a click outside it.

The expanded sidebar keeps database tools behind the database-name disclosure. Table filtering and the table list remain visible. Appearance also contains the language selector.

Selection pages keep query controls behind one disclosure. The query summary shows the active filter count. Table navigation and SQL remain in their original positions.

Navigation still uses Adminer's server-rendered pages. Early layout rules reduce page-load shifts, and the sidebar restores its table-list scroll position.

## Color schemes

**Appearance** groups palettes by theme family and stores separate light and dark choices in the browser. Catppuccin includes Latte, Frappé, Macchiato, and Mocha. The default pair remains Frappé and Macchiato, both dark palettes.

Other families include One, Ayu, Monokai, Nord, Solarized, Gruvbox, and Tokyo Night. Monokai Paper is an Adminer light companion, not an official Monokai variant.

`PALETTES.txt` records the sources and the installed Tern and Rex Beta resources inspected for this catalog.

## Notes

- Use `index.php` if you want custom plugins and theming.
- Opening `adminer.php` directly bypasses this custom plugin setup.
