# Mutable Observer Package for Laravel

[![Latest Version on Packagist](https://img.shields.io/packagist/v/akaunting/laravel-mutable-observer.svg?style=flat-square)](https://packagist.org/packages/akaunting/laravel-mutable-observer)
[![Total Downloads](https://img.shields.io/packagist/dt/akaunting/laravel-mutable-observer.svg?style=flat-square)](https://packagist.org/packages/akaunting/laravel-mutable-observer)
[![Tests](https://img.shields.io/github/actions/workflow/status/akaunting/laravel-mutable-observer/tests.yml?branch=master&label=tests&style=flat-square)](https://github.com/akaunting/laravel-mutable-observer/actions/workflows/tests.yml)
[![Code Quality](https://img.shields.io/github/actions/workflow/status/akaunting/laravel-mutable-observer/tests.yml?branch=master&label=code%20quality&style=flat-square)](https://github.com/akaunting/laravel-mutable-observer/actions/workflows/tests.yml)
[![codecov](https://img.shields.io/codecov/c/github/akaunting/laravel-mutable-observer?style=flat-square)](https://codecov.io/gh/akaunting/laravel-mutable-observer)
[![StyleCI](https://github.styleci.io/repos/462492001/shield?style=flat-square)](https://styleci.io/repos/462492001)
[![PHPStan Level](https://img.shields.io/badge/PHPStan-level%20max-brightgreen.svg?style=flat-square)](phpstan.neon)
[![Psalm Type Coverage](https://img.shields.io/badge/Psalm-level%201-brightgreen.svg?style=flat-square)](psalm.xml)
[![License](https://img.shields.io/github/license/akaunting/laravel-mutable-observer?style=flat-square)](LICENSE.md)

This package allows you to `mute` and `unmute` specific observer events at will. It ships with a trait that adds mutable methods to your observer classes, making it perfect for testing scenarios and preventing unwanted side effects.

## Features

- ✅ **Mute specific events** - Target individual observer methods
- ✅ **Mute all events** - Silence entire observers with a single call
- ✅ **Framework integration** - Seamlessly works with Laravel's observer system
- ✅ **Laravel 9-12 support** - Compatible with all modern Laravel versions
- ✅ **PHP 8.0-8.4 support** - Uses modern PHP features like readonly properties
- ✅ **Type-safe** - Full type declarations for better IDE support
- ✅ **Zero dependencies** - Only requires Laravel framework
- ✅ **Well tested** - Comprehensive test coverage

## Installation

Run the following command:

```bash
composer require akaunting/laravel-mutable-observer
```

## Usage

### Basic Setup

Add the `Mutable` trait to your observer class:

```php
namespace App\Observers;

use Akaunting\MutableObserver\Traits\Mutable;

class UserObserver
{
    use Mutable;

    public function creating($user)
    {
        // Send welcome email
        Mail::to($user->email)->send(new WelcomeEmail($user));
    }

    public function created($user)
    {
        // Log user creation
        Log::info("User created: {$user->email}");
    }

    public function updating($user)
    {
        // Validate changes
        // ...
    }
}
```

### Muting All Events

Perfect for testing scenarios where you want to disable all observer actions:

```php
UserObserver::mute();

// Create users without triggering any observer events
$user = User::create([
    'name' => 'John Doe',
    'email' => 'john@example.com',
]);
// No emails sent, no logs created

UserObserver::unmute();
```

### Muting Specific Events

Target individual observer methods while keeping others active:

```php
// Mute only the creating event (no welcome emails)
UserObserver::mute('creating');

$user = User::create(['name' => 'Jane Doe']);
// Creating event is muted, but created event still fires
```

### Muting Multiple Events

```php
// Mute multiple specific events
UserObserver::mute(['creating', 'updating']);

$user = User::create(['name' => 'Test User']);
$user->update(['name' => 'Updated Name']);
// Both creating and updating events are muted

UserObserver::unmute();
```

### Real-World Examples

#### Testing Scenario

```php
use Tests\TestCase;

class UserTest extends TestCase
{
    public function test_user_can_be_created_without_side_effects()
    {
        // Prevent emails and logs during testing
        UserObserver::mute();

        $user = User::factory()->create();

        $this->assertDatabaseHas('users', [
            'email' => $user->email,
        ]);

        UserObserver::unmute();
    }

    public function test_only_email_is_muted()
    {
        // Mute only email sending, keep logging
        UserObserver::mute('creating');

        Log::shouldReceive('info')->once();

        $user = User::factory()->create();
        // Email not sent, but log was created

        UserObserver::unmute();
    }
}
```

#### Bulk Operations

```php
// Disable observer for bulk operations
UserObserver::mute();

User::insert([
    ['name' => 'User 1', 'email' => 'user1@example.com'],
    ['name' => 'User 2', 'email' => 'user2@example.com'],
    ['name' => 'User 3', 'email' => 'user3@example.com'],
]);
// No observers triggered, much faster

UserObserver::unmute();
```

#### Conditional Muting

```php
use Illuminate\Support\Facades\App;

// Mute observers in specific environments
if (App::environment('testing')) {
    UserObserver::mute();
}

// Or use it for specific operations
$shouldNotify = false;

if (!$shouldNotify) {
    UserObserver::mute('creating');
}

$user = User::create($data);

if (!$shouldNotify) {
    UserObserver::unmute();
}
```

#### Using Constants

```php
use Akaunting\MutableObserver\Traits\Mutable;

// Use the built-in wildcard constant
UserObserver::mute(Mutable::WILDCARD_EVENT); // Same as mute()
```

## API Reference

### `Mutable::mute($events = null)`

Mutes the specified observer events.

**Parameters:**
- `$events` (string|array|null) - The events to mute. Pass `null` or no arguments to mute all events.

**Returns:** void

**Examples:**
```php
UserObserver::mute();                    // Mute all events
UserObserver::mute('created');           // Mute single event
UserObserver::mute(['created', 'updated']); // Mute multiple events
```

### `Mutable::unmute()`

Unmutes all previously muted observer events.

**Returns:** void

**Example:**
```php
UserObserver::unmute(); // Restore all observer functionality
```

### `Mutable::WILDCARD_EVENT`

A constant representing the wildcard event that mutes all observer methods.

**Value:** `'*'`

## Requirements

- PHP 8.0 or higher
- Laravel 9.0 or higher

## Code Quality

This package maintains the highest code quality standards:

- ✅ **PHPStan Level Max** - Maximum static analysis level
- ✅ **Psalm Level 1** - Strictest type checking
- ✅ **PHP CS Fixer** - PSR-12 compliant code style
- ✅ **Infection** - Mutation testing with 90%+ MSI
- ✅ **100% Test Coverage** - Comprehensive test suite
- ✅ **Strict Types** - All files use `declare(strict_types=1)`

### Running Quality Checks

```bash
# Run all quality checks
composer quality

# Individual checks
composer test              # Run tests
composer test:coverage     # Run tests with coverage report
composer analyse           # Run PHPStan + Psalm
composer phpstan           # Run PHPStan only
composer psalm             # Run Psalm only
composer format            # Fix code style issues
composer format:check      # Check code style without fixing
composer infection         # Run mutation testing

# Using Makefile (Unix/Mac/WSL)
make quality              # Run all quality checks
make test                 # Run tests
make analyse              # Run static analysis
make format               # Fix code style
make help                 # Show all available commands
```

## How It Works

The package uses a proxy pattern to intercept calls to your observer methods. When you call `mute()`, it registers a proxy in Laravel's service container that swallows the specified events. When you call `unmute()`, it removes the proxy and restores normal observer functionality.

## Best Practices

1. **Always unmute after muting** - Especially in tests, use try-finally blocks:
   ```php
   try {
       UserObserver::mute();
       // Your code here
   } finally {
       UserObserver::unmute();
   }
   ```

2. **Use setUp and tearDown in tests**:
   ```php
   protected function setUp(): void
   {
       parent::setUp();
       UserObserver::mute();
   }

   protected function tearDown(): void
   {
       UserObserver::unmute();
       parent::tearDown();
   }
   ```

3. **Be specific when possible** - Mute only the events you need to disable:
   ```php
   UserObserver::mute('creating'); // Better than mute()
   ```

4. **Document muted events** - Add comments explaining why observers are muted:
   ```php
   // Mute email notifications during bulk import
   UserObserver::mute('created');
   ```

## Troubleshooting

**Q: Observer still firing after muting?**  
A: Make sure the observer is registered through Laravel's observer system and the trait is properly imported.

**Q: Can I mute observers globally?**  
A: Yes, mute them in a service provider's `boot()` method or middleware.

**Q: Does this work with model events?**  
A: Yes, it works with any observer registered via `Model::observe()`.

## Changelog

Please see [Releases](../../releases) for more information what has changed recently.

## Contributing

Pull requests are more than welcome. You must follow the PSR coding standards.

## Security

Please review [our security policy](https://github.com/akaunting/laravel-sortable/security/policy) on how to report security vulnerabilities.

## Credits

- [Cüneyt Şentürk](https://github.com/cuneytsenturk)
- [Denis Duliçi](https://github.com/denisdulici)
- [Stephen Lewis](https://github.com/monooso)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [LICENSE](LICENSE.md) for more information.
