## Development Stack Overview

The Club CMS development environment is built on a modern PHP/Laravel stack with Node.js build tools for frontend asset compilation and development workflow automation.

### Core Development Dependencies

**Sources:** [club\_cms/composer.json8-23]() [club\_cms/package-lock.json6-14]()

## PHP Backend Environment

### Required Dependencies

The Laravel application requires PHP 8.2 or higher with the following core packages:

| Package | Version | Purpose |
| --- | --- | --- |
| `laravel/framework` | ^12.0 | Core Laravel framework |
| `laravel/passport` | ^13.0 | OAuth2 authentication server |
| `darkaonline/l5-swagger` | ^9.0 | OpenAPI documentation generation |
| `laravel/tinker` | ^2.10.1 | Interactive REPL for debugging |

**Sources:** [club\_cms/composer.json8-13]()

### Development Dependencies

Development and testing tools are configured through Composer dev dependencies:

| Package | Version | Purpose |
| --- | --- | --- |
| `phpunit/phpunit` | ^11.5.3 | Unit and feature testing framework |
| `laravel/pint` | ^1.13 | Code style fixer |
| `laravel/sail` | ^1.41 | Docker development environment |
| `mockery/mockery` | ^1.6 | Mocking framework for tests |
| `fakerphp/faker` | ^1.23 | Fake data generation for testing |
| `laravel/pail` | ^1.2.2 | Enhanced log monitoring |

**Sources:** [club\_cms/composer.json15-22]()

### Autoloading Configuration

The application uses PSR-4 autoloading standards:

**Sources:** [club\_cms/composer.json24-34]()

## Node.js Frontend Environment

### Build Tool Dependencies

The frontend build system uses modern JavaScript tooling:

| Package | Version | Purpose |
| --- | --- | --- |
| `vite` | ^6.2.4 | Frontend build tool and development server |
| `laravel-vite-plugin` | ^1.2.0 | Laravel integration for Vite |
| `@tailwindcss/vite` | ^4.0.0 | TailwindCSS Vite integration |
| `tailwindcss` | ^4.0.0 | Utility-first CSS framework |
| `axios` | ^1.8.2 | HTTP client for API requests |
| `concurrently` | ^9.0.1 | Run multiple development processes |

**Sources:** [club\_cms/package-lock.json7-13]()

### Development Workflow Integration

The Node.js environment integrates with Laravel's development workflow through the Vite plugin, enabling hot module replacement and optimized asset compilation.

**Sources:** [club\_cms/package-lock.json1-14]()

## Development Scripts and Automation

### Composer Scripts

The development environment includes several automated scripts for common tasks:

**Sources:** [club\_cms/composer.json52-58]()

### Development Script Configuration

The `dev` script runs multiple processes concurrently:

* **PHP Server**: `php artisan serve` on default port
* **Queue Worker**: `php artisan queue:listen --tries=1` for background jobs
* **Log Monitor**: `php artisan pail --timeout=0` for real-time log viewing
* **Vite Server**: `npm run dev` for frontend asset compilation

**Sources:** [club\_cms/composer.json52-55]()

### Test Script Configuration

The `test` script performs:

1. Configuration cache clearing: `php artisan config:clear --ansi`
2. Test suite execution: `php artisan test`

**Sources:** [club\_cms/composer.json56-58]()

## Environment Configuration

### Git Ignore Configuration

The development environment excludes the following from version control:

| Path/Pattern | Purpose |
| --- | --- |
| `/.phpunit.cache` | PHPUnit test cache |
| `/node_modules` | Node.js dependencies |
| `/public/build` | Compiled frontend assets |
| `/public/hot` | Vite hot reload files |
| `/storage/*.key` | Laravel encryption keys |
| `/vendor` | Composer dependencies |
| `.env.backup`, `.env.production` | Environment files |
| `/.idea`, `/.vscode`, `/.fleet` | IDE configuration |

**Sources:** [club\_cms/.gitignore1-23]()

### Request Validation Setup

The development environment includes form request validation classes for API endpoints:

**Sources:** [club\_cms/app/Http/Requests/AuthRequest.php7-28]()

The `AuthRequest` class demonstrates the validation pattern used throughout the application:

* Extends Laravel's `FormRequest` base class
* Implements `authorize()` method returning `true` for unrestricted access
* Defines validation rules in `rules()` method using Laravel validation syntax

**Sources:** [club\_cms/app/Http/Requests/AuthRequest.php22-28]()

## Development Workflow

### Package Management

The environment uses dual package managers:

* **Composer**: Manages PHP dependencies and Laravel packages
* **NPM**: Manages Node.js dependencies and frontend build tools

### Asset Compilation

Frontend assets are processed through Vite with TailwindCSS integration, providing:

* Hot module replacement during development
* Optimized production builds
* PostCSS processing for TailwindCSS utilities

### Code Quality Tools

Built-in quality assurance includes:

* **Laravel Pint**: Automatic code style fixing
* **PHPUnit**: Comprehensive testing framework
* **Mockery**: Advanced mocking capabilities for unit tests
* **Faker**: Realistic test data generation

**Sources:** [club\_cms/composer.json15-22]()

Auto-refresh not enabled yet

Try DeepWiki on your private codebase with [Devin]()

### On this page

* [Development Environment]()
* [Development Stack Overview]()
* [Core Development Dependencies]()
* [PHP Backend Environment]()
* [Required Dependencies]()
* [Development Dependencies]()
* [Autoloading Configuration]()
* [Node.js Frontend Environment]()
* [Build Tool Dependencies]()
* [Development Workflow Integration]()
* [Development Scripts and Automation]()
* [Composer Scripts]()
* [Development Script Configuration]()
* [Test Script Configuration]()
* [Environment Configuration]()
* [Git Ignore Configuration]()
* [Request Validation Setup]()
* [Development Workflow]()
* [Package Management]()
* [Asset Compilation]()
* [Code Quality Tools]()