# Custom Adminer Package with Theme and Plugin Support

Custom Adminer package with a clean dark/light theme and plugin support.

## Setup

### Casual use (quick local run)

```bash
git clone git@github.com:arif1205/authlab-adminer-theme.git adminer
cd adminer
php -S localhost:8080
```

Then open:

- `http://localhost:8080/index.php` (recommended)
- or `http://localhost:8080/adminer.php`

### Laravel Herd setup (served as `adminer.test`)

From your projects directory:

```bash
cd /Volumes/Projects
mkdir -p adminer
cd adminer
git clone git@github.com:arif1205/authlab-adminer-theme.git .
```

Herd serves this folder as `http://adminer.test` out of the box.

## Screenshots

![Light theme](/screenshot-light.jpeg?raw=true "Light theme screenshot")
![Dark theme](/screenshot-dark.jpeg?raw=true "Dark theme screenshot")
