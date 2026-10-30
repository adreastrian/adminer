# Custom Adminer package

Run Adminer 6.2.0 with a collapsible sidebar, saved color palettes, and locally bundled Arc Select, Combobox, and Segmented Control components.

## Screenshots

These screenshots use sample SQLite data with Catppuccin Latte for light mode and Macchiato for dark mode.

![Adminer in Catppuccin Latte, with the sidebar and projects table visible](screenshot-light.png)

![Adminer in Catppuccin Macchiato, with the sidebar and projects table visible](screenshot-dark.png)

## Run locally

Use PHP with the extension for your database, such as `mysqli`, `pdo_pgsql`, or `sqlite3`. Node.js is not required to run the app. The built theme assets are committed.

1. Clone the repository and open its directory:

   ```bash
	git clone https://github.com/adreastrian/adminer.git
	cd adminer
   ```

2. Start the PHP development server:

   ```bash
	php -S 127.0.0.1:8080
   ```

3. Open [Adminer on localhost](http://127.0.0.1:8080/index.php).
4. Choose your database system and enter your connection details.
5. Click **Login**.

Keep the development server bound to `127.0.0.1`. Do not expose it to the internet.

Use `index.php` for the custom plugins and theme. To bypass the custom plugin stack, open `adminer.php` instead. Both entrypoints load the official core from `vendor/adminer/adminer.php`.

## Save local credentials

Skip this section if you prefer to enter credentials on the login form.

1. Copy the example file to the project root:

   ```bash
	cp .env.example .env
   ```

2. Set `USERNAME` and `PASSWORD` in `.env`:

   ```dotenv
	USERNAME=your_db_username
	PASSWORD=your_db_password
   ```

Keep both keys, with one unquoted `KEY=value` pair per line. The parser does not support comments or `=` inside values. The `AutoLogin` plugin uses these values as credential defaults. You still choose the database connection in Adminer.

The repository ignores `.env`. Never commit credentials. On a web server, block HTTP access to `.env` and restrict access to Adminer itself.

## Use the sidebar

Click the toolbar's sidebar button to collapse or expand the sidebar. The browser remembers the desktop sidebar state.

Press **⌘B** on Mac or **Ctrl+B** elsewhere to toggle the sidebar. The shortcut does not run while you edit text or use a dropdown.

To find a table, use **Find a table**. When the sidebar is collapsed, click its search icon to expand it and focus the search field.

Click the database name to show the database selector and tools for SQL, import, export, and table creation. On table selection pages, open **Query options** to change filters, columns, sort order, and limits.

On narrow screens, the sidebar opens over the page. Press **Escape** or click outside the sidebar to close it.

## Choose an appearance

Open **Appearance** at the bottom of the sidebar. Choose **Light**, **Dark**, or **System**, then select a **Theme family**. Set **Light palette** and **Dark palette** separately. The browser saves your choices.

The default Catppuccin pair is Frappé for light mode and Macchiato for dark mode. Both palettes use dark colors. For a light background, choose **Latte** as the **Light palette**.

Other theme families include One, Ayu, Monokai, Nord, Solarized, Gruvbox, and Tokyo Night. Monokai Paper is a custom light companion, not an official Monokai variant. Palette sources are recorded in [`PALETTES.txt`](PALETTES.txt).

To change the interface language, use the language selector inside **Appearance**.

## Change the plugins

Edit [`adminer-plugins.php`](adminer-plugins.php) to enable or disable plugins. Keep `LoadDependencies` first. Custom plugin implementations are in [`adminer-plugins/`](adminer-plugins/).

The default stack includes table search, JSON previews, foreign-key editing, credential defaults, and appearance controls.

## Rebuild the theme components

After you change files under `ui/`, run these commands with Node.js and npm:

```bash
	npm ci
	npm run typecheck
	npm run build
```

Deploy the PHP files, `adminer-plugins/`, `vendor/`, the root CSS files, `assets/`, `js/`, and `fonts/`. Include the rebuilt `assets/arc-ui.js` and `assets/arc-ui.css`, and keep `assets/arc-ui.LICENSE.txt` beside them.

The component sources and license are in [`ui/arc/`](ui/arc/). The Adminer release source and checksum are in [`vendor/adminer/UPSTREAM.txt`](vendor/adminer/UPSTREAM.txt).
