# AGENTS.md - AI Coding Agent Guidelines

This document provides guidelines for AI coding agents working on the **ec-validador-cedula-ruc** project, a PHP library for validating Ecuadorian identification documents (Cedula and RUC).

## Project Overview

- **Language**: PHP 8.2+
- **Purpose**: Validate Ecuadorian Cedula and RUC numbers
- **Structure**: Single main class library with comprehensive test suite
- **License**: MIT

## Project Structure

```
ec-validador-cedula-ruc/
├── src/
│   └── ValidadorEc.php       # Main library class (single file)
├── tests/
│   ├── TestCase.php          # Base test class with setUp()
│   ├── CedulaValidationTest.php
│   ├── NaturalPersonRucValidationTest.php
│   ├── PrivateCompanyRucValidationTest.php
│   ├── PublicCompanyRucValidationTest.php
│   ├── StaticMethodsTest.php
│   └── UniversalValidationTest.php
├── composer.json
├── phpunit.xml
└── readme.md
```

---

## Build / Lint / Test Commands

### Install Dependencies
```bash
composer install
```

### Run All Tests
```bash
./vendor/bin/phpunit
```

### Run Tests with Detailed Output
```bash
./vendor/bin/phpunit --testdox
```

### Run a Single Test File
```bash
./vendor/bin/phpunit tests/CedulaValidationTest.php
```

### Run a Single Test Method
```bash
./vendor/bin/phpunit --filter test_valid_cedulas
```

### Run Tests for a Specific Class
```bash
./vendor/bin/phpunit --filter CedulaValidationTest
```

### Run Tests with Coverage
```bash
./vendor/bin/phpunit --coverage-text
```

### No Build Step Required
This is a library - no compilation or build process needed.

### No Linter Configured
Follow existing code style patterns. No PHP-CS-Fixer or PHPStan configuration exists.

---

## Code Style Guidelines

### Language
- **All code and comments must be in English**
- Use clear, descriptive names that explain intent

### PHP Version Features
- Target PHP 8.2+ features
- Use `declare(strict_types=1)` at the top of every file
- Use `final class` for non-extendable classes
- Use `match` expressions where appropriate

### File Header Pattern
```php
<?php

declare(strict_types=1);

namespace Tavo;

use InvalidArgumentException;
```

### Imports
- One class per `use` statement (no grouped imports)
- Import only what you use
- Order: PHP built-in classes first, then external packages

### Naming Conventions

| Element | Convention | Example |
|---------|------------|---------|
| Class | PascalCase | `ValidadorEc` |
| Public method | camelCase | `validateCedula()`, `getError()` |
| Private method | camelCase | `performCedulaValidation()` |
| Property | camelCase | `$documentType`, `$error` |
| Constants | SCREAMING_SNAKE_CASE | `TYPE_CEDULA`, `MODULO_10` |
| Test methods | snake_case with `test_` prefix | `test_valid_cedulas()` |

### Type Declarations
- Always use return type declarations
- Always use property type declarations
- Use PHPDoc for complex types (arrays, generics):
  ```php
  /** @var list<int> */
  private const COEFFICIENTS = [2, 1, 2, 1, 2, 1, 2, 1, 2];
  ```

### Class Organization
Organize class members in this order with section headers:

```php
final class ValidadorEc
{
    // Constants (public first, then private)
    public const TYPE_CEDULA = 'cedula';
    private const FOREIGN_RESIDENT_CODE = 30;
    
    // Properties
    private string $error = '';
    
    // ==================== Static Methods ====================
    public static function validateCedula(string $number): bool { }
    
    // ==================== Instance Methods ====================
    public function validate(string $number): bool { }
    
    // ==================== Internal Validation Methods ====================
    private function performValidation(): bool { }
    
    // ==================== Helper Methods ====================
    private function extractDigits(): array { }
}
```

### Error Handling Pattern
The library uses a specific pattern: internal methods throw exceptions, public methods catch and store errors.

```php
// Internal methods throw InvalidArgumentException
private function assertValidProvinceCode(int $code): void
{
    if (!$isValid) {
        throw new InvalidArgumentException('Province code must be between 1 and 24, or 30');
    }
}

// Public methods catch exceptions and store error message
public function validateCedula(string $number = ''): bool
{
    try {
        $this->assertValidInitialFormat($number, 10);
        $this->performCedulaValidation($number);
    } catch (InvalidArgumentException $e) {
        $this->error = $e->getMessage();
        return false;
    }
    return true;
}

// Error accessible via getter
public function getError(): string
{
    return $this->error;
}
```

---

## Testing Guidelines

### Test File Naming
- Test files: `{Feature}Test.php` (e.g., `CedulaValidationTest.php`)
- Extend `Tavo\Tests\TestCase`

### Test Method Naming
- Use snake_case with `test_` prefix
- Be descriptive: `test_invalid_cedula_with_wrong_check_digit()`

### Test Structure
```php
<?php

declare(strict_types=1);

namespace Tavo\Tests;

final class CedulaValidationTest extends TestCase
{
    // ==================== Section Name ====================
    
    public function test_descriptive_name(): void
    {
        $this->assertFalse($this->validator->validateCedula('1234567890'));
        $this->assertEquals('Expected error message', $this->validator->getError());
    }
}
```

### Base TestCase
The `TestCase` class provides a `$this->validator` instance via `setUp()`:
```php
protected function setUp(): void
{
    $this->validator = new ValidadorEc();
}
```

---

## CI/CD

GitHub Actions runs tests on PHP 8.2, 8.3, and 8.4.

Ensure all tests pass before committing:
```bash
./vendor/bin/phpunit --testdox
```

---

## Common Patterns

### Adding a New Validation Type
1. Add public constant for the type
2. Add private validation method
3. Add public method that catches exceptions and stores errors
4. Add static wrapper method if appropriate
5. Add comprehensive tests

### Modifying Validation Logic
1. Update the private validation method
2. Update or add tests to cover the change
3. Run full test suite to check for regressions
