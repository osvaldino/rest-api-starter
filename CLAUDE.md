# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

This is a Laravel 12 skeleton/starter kit API using PHP 8.2+ with PostgreSQL as the database. It uses Pest for testing and Laravel Sail for Docker-based local development.

## Development Commands

### Initial Setup
```bash
composer run setup
```
This installs dependencies, copies .env, generates app key, runs migrations, and builds frontend assets.

### Running the Development Environment
```bash
composer run dev
```
This concurrently runs:
- Laravel development server (port 80 or APP_PORT)
- Queue worker
- Laravel Pail (log viewer)
- Vite dev server (port 5173)

Alternatively, use Laravel Sail for Docker-based development:
```bash
./vendor/bin/sail up
./vendor/bin/sail artisan migrate
```

### Testing
Run all tests:
```bash
composer run test
# or directly:
php artisan test
```

Run specific test suites:
```bash
php artisan test --testsuite=Feature
php artisan test --testsuite=Unit
```

Run a single test file:
```bash
php artisan test tests/Feature/ExampleTest.php
```

This project uses **Pest**, not PHPUnit. Test files use Pest's function-based syntax (`it()`, `test()`, `expect()`) rather than PHPUnit classes.

### Database Commands
```bash
php artisan migrate              # Run migrations
php artisan migrate:fresh        # Drop all tables and re-run migrations
php artisan migrate:rollback     # Rollback the last migration
php artisan db:seed              # Run database seeders
php artisan migrate:fresh --seed # Fresh migration with seeding
```

### Code Quality
```bash
./vendor/bin/pint                # Run Laravel Pint (code formatter)
./vendor/bin/pint --test         # Check formatting without changes
```

### Other Useful Commands
```bash
php artisan tinker               # Interactive REPL
php artisan route:list           # List all routes
php artisan make:controller      # Generate controller
php artisan make:model           # Generate model
php artisan make:migration       # Generate migration
php artisan queue:work           # Start queue worker
```

## Architecture

### Directory Structure
- `app/Http/Controllers/` - HTTP controllers
- `app/Models/` - Eloquent models
- `app/Providers/` - Service providers
- `routes/web.php` - Web routes
- `routes/console.php` - Console commands
- `database/migrations/` - Database migrations
- `database/factories/` - Model factories for testing
- `database/seeders/` - Database seeders
- `tests/Feature/` - Feature tests (HTTP requests, database interactions)
- `tests/Unit/` - Unit tests (isolated logic)
- `resources/views/` - Blade templates
- `resources/js/` - JavaScript assets
- `resources/css/` - CSS assets

### Testing with Pest
All tests inherit from `Tests\TestCase` and Feature tests have access to Laravel's testing helpers. The `tests/Pest.php` file contains shared configuration and custom expectations. Use `RefreshDatabase` trait when tests need database access (currently commented out in Pest.php - uncomment if needed).

### Database
Uses PostgreSQL via Laravel Sail (Docker). Connection configured in `.env` file. Testing uses a separate `testing` database (automatically created by Sail).

### Frontend Assets
Built with Vite and Tailwind CSS 4. Frontend tooling configured in `vite.config.js` and `package.json`.
