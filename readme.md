# Custom Adminer Package

Custom Adminer bundle with:

- Plugin-enabled `index.php` (Adminer 5.3.0)
- Custom UI/theme styling
- Dark/Light/System theme switcher
- Auto login support using a `.env` file

## Screenshots

![Light theme](/screenshot-light.jpeg?raw=true "Screenshot")

![Dark theme](/screenshot-dark.jpeg?raw=true "Screenshot")

## Project Structure

- `index.php`: Main entrypoint with plugin loader enabled
- `adminer.php`: Standalone Adminer file (no custom plugin stack)
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

## Notes

- Use `index.php` if you want custom plugins and theming.
- Opening `adminer.php` directly bypasses this custom plugin setup.
