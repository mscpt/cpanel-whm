# CLAUDE.md

This file provides guidance for AI assistants working with the `gufy/cpanel-whm` codebase.

## Project Overview

**gufy/cpanel-whm** is a Laravel 5 package that provides a facade-based interface for the cPanel/WHM API. It wraps the lower-level `gufy/cpanel-php` library and integrates it into Laravel's service container, configuration, and facade systems.

- **Package name**: `gufy/cpanel-whm`
- **Current version**: v1.0.2
- **License**: MIT
- **PHP requirement**: >= 5.4.0 (no PHP 7+ syntax such as null coalescing `??` — see comment in `CpanelWhm.php:76`)
- **Laravel requirement**: ~5 (`illuminate/support`)
- **Upstream dependency**: `gufy/cpanel-php ~1.0`

## Repository Structure

```
cpanel-whm/
├── src/
│   └── Gufy/
│       └── CpanelWhm/
│           ├── CpanelWhm.php              # Core class — extends Cpanel, overrides runQuery()
│           ├── CpanelWhmServiceProvider.php # Laravel service provider (registers singleton + facade)
│           ├── Facades/
│           │   └── CpanelWhm.php          # Laravel facade returning 'cpanel-whm' from container
│           └── (Laravel scaffold dirs)
│               ├── config/
│               │   └── config.php         # Published config template
│               ├── controllers/           # Empty (reserved)
│               ├── lang/                  # Empty (reserved)
│               ├── migrations/            # Empty (classmap-autoloaded)
│               └── views/                 # Empty (reserved)
├── tests/                                 # PHPUnit test directory (currently empty)
├── public/                                # Public assets (currently empty)
├── composer.json                          # Package definition
├── phpunit.xml                            # PHPUnit configuration
├── .travis.yml                            # CI configuration (PHP 5.3–5.6, HHVM)
├── .gitignore                             # Ignores /vendor, composer.lock, .DS_Store, /tests/, .idea
├── VERSION                                # Plain-text version string (v1.0.2)
└── README.md                              # User-facing installation and usage docs
```

## Key Source Files

### `src/Gufy/CpanelWhm/CpanelWhm.php`

The primary class. Extends `Gufy\CpanelPhp\Cpanel` (the upstream library).

**Responsibilities:**
- Reads server credentials from Laravel config on construction
- Supports single-server and multi-server config layouts
- Overrides `runQuery()` to inject `username`, `password`/`auth`, `host`, and `authType` before every API call
- Exposes `server($key)` static helper to select a named server from config

**Important methods:**
| Method | Description |
|---|---|
| `__construct($server_key = false)` | Loads config; calls `abort(500)` if config is missing |
| `static server($server_key)` | Returns a new instance bound to the named server |
| `setConfig($server_key)` | Delegates to `fetchConfig()` and populates instance vars |
| `fetchConfig($server_key)` | Tries `servers[$key]`, then first element, then legacy flat config |
| `setHostname($hostname)` | Setter for `$hostName` (fluent) |
| `getHostname()` | Getter for `$hostName` |
| `setAuthenticationDetails($user, $pass, $host)` | Fluent auth override |
| `get($user, $pass, $host)` | Factory-style static-ish method creating a configured instance |
| `runQuery($action, $arguments)` | Overrides parent; sets auth/host/authType then calls `parent::runQuery()` |

### `src/Gufy/CpanelWhm/CpanelWhmServiceProvider.php`

Standard Laravel 5 service provider. Registered in the host application's `config/app.php`.

- Registers `cpanel-whm` as a singleton in the IoC container
- Publishes `src/config/config.php` → `config/cpanel-whm.php` in the host app
- Auto-aliases `CpanelWhm` to the facade class during application boot

### `src/Gufy/CpanelWhm/Facades/CpanelWhm.php`

Thin Laravel facade. Returns the `cpanel-whm` binding from the container via `getFacadeAccessor()`.

### `src/config/config.php`

The publishable config template. Structure:

```php
return [
    'servers' => [
        'example1' => [
            'host'     => 'https://127.0.0.1:2087',  // Full URL with protocol and port
            'auth'     => 'your_long_string_hash_key', // Remote Access Key hash
            'username' => 'root',
            // 'auth_type' => 'hash', // defaults to 'hash'; can be 'password'
        ],
        // Additional servers can be added here
    ],
];
```

**Auth type notes:**
- `hash` (default): uses the WHM Remote Access Key
- `password`: uses a plain-text password (less secure)

## Development Workflows

### Installing Dependencies

```bash
composer install
```

The `vendor/` directory and `composer.lock` are gitignored — do not commit them.

### Running Tests

```bash
phpunit
```

PHPUnit is bootstrapped from `vendor/autoload.php` (see `phpunit.xml`). The test suite scans `./tests/` for `*.php` files. The `tests/` directory is currently empty (only a `.gitkeep` placeholder) and the directory itself is gitignored — tests must be added before they can be run.

### CI

Travis CI (`.travis.yml`) tests against PHP 5.3, 5.4, 5.5, 5.6, and HHVM:

```yaml
before_script:
  - composer self-update
  - composer install --prefer-source --no-interaction --dev
script: phpunit
```

## Code Conventions

### PHP Compatibility

**This package targets PHP >= 5.4.** Do not use:
- Null coalescing operator `??` (PHP 7.0+)
- Short closures / arrow functions `fn() =>` (PHP 7.4+)
- Typed properties (PHP 7.4+)
- Named arguments (PHP 8.0+)
- Any other PHP 7+ syntax

There is an explicit comment in `CpanelWhm.php:76` marking the PHP 7+ alternative as a reminder:
```php
//$this->authType = $server['auth_type'] ?? 'hash'; // PHP 7+
```

### Autoloading

Uses **PSR-0** (not PSR-4) under the `Gufy\CpanelWhm` namespace rooted at `src/`:

```json
"autoload": {
    "classmap": ["src/migrations"],
    "psr-0": { "Gufy\\CpanelWhm\\": "src/" }
}
```

When adding new classes, follow the existing directory structure: `src/Gufy/CpanelWhm/ClassName.php`.

### Namespace

All package classes must use the `Gufy\CpanelWhm` namespace (or a sub-namespace like `Gufy\CpanelWhm\Facades`).

### Laravel Integration Pattern

This package follows the standard Laravel 5 package pattern:
1. **Service Provider** registers services with `$this->app->singleton()`
2. **Facade** provides static access via a registered container key
3. **Config** is publishable via `php artisan vendor:publish`

Do not add routes, migrations, or views unless the feature explicitly requires them — those directories exist as Laravel scaffolding placeholders only.

### Method Style

- Fluent setters return `$this`
- Factory methods return `new static` or `new self`
- No dependency injection in constructor beyond what Laravel provides via config

## Configuration Reference

The package reads from Laravel's `config('cpanel-whm')` at runtime.

**Multi-server format** (recommended):
```php
// config/cpanel-whm.php
return [
    'servers' => [
        'server_key' => [
            'host'      => 'https://hostname:2087',
            'auth'      => 'hash_or_password',
            'username'  => 'root',
            'auth_type' => 'hash', // optional, defaults to 'hash'
        ],
    ],
];
```

**Legacy flat format** (still supported via `fetchConfig()` fallback):
```php
return [
    'host'      => 'https://hostname:2087',
    'auth'      => 'hash_or_password',
    'username'  => 'root',
    'auth_type' => 'hash',
];
```

**Accessing a named server at runtime:**
```php
CpanelWhm::server('server_key')->listaccts();
```

## Common Pitfalls

- **Missing config**: If no servers are configured and `CpanelWhm` is instantiated, it calls `abort(500)`. Always publish and configure `config/cpanel-whm.php` before use.
- **PHP version**: Keep all code PHP 5.4-compatible. The Travis CI matrix and `composer.json` both enforce this.
- **Auth type**: The `auth_type` key in config is optional with a `'hash'` default. Use `empty()` checks rather than `isset()` + `??` to stay PHP 5.4 compatible.
- **`vendor/` is gitignored**: Run `composer install` after cloning; never commit the vendor directory.
- **`tests/` is gitignored**: New test files added locally will not be tracked by git unless `.gitignore` is updated.

## Dependencies

| Package | Version | Role |
|---|---|---|
| `php` | >= 5.4.0 | Runtime |
| `illuminate/support` | ~5 | Laravel service provider, config, facades |
| `gufy/cpanel-php` | ~1.0 | Underlying cPanel/WHM HTTP client |
