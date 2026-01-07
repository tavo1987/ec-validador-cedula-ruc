# Ecuador ID (Cedula) and RUC Validator

<p align="center"><img src="http://res.cloudinary.com/edwin/image/upload/v1496095463/cedulaLogo_lmct8r.png"/></p>

<p align="center">
<a href="https://github.com/tavo1987/ec-validador-cedula-ruc/actions/workflows/tests.yml"><img src="https://github.com/tavo1987/ec-validador-cedula-ruc/actions/workflows/tests.yml/badge.svg" alt="Tests"></a>
<a href="https://packagist.org/packages/tavo1987/ec-validador-cedula-ruc"><img src="https://img.shields.io/badge/PHP-8.2+-blue.svg?style=flat-square" alt="PHP 8.2+"></a>
<a href="https://packagist.org/packages/tavo1987/ec-validador-cedula-ruc"><img src="https://img.shields.io/github/license/mashape/apistatus.svg?style=flat-square" alt="License"></a>
<a href="https://packagist.org/packages/tavo1987/ec-validador-cedula-ruc"><img src="https://poser.pugx.org/tavo1987/ec-validador-cedula-ruc/downloads" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/tavo1987/ec-validador-cedula-ruc"><img src="https://poser.pugx.org/tavo1987/ec-validador-cedula-ruc/v/stable" alt="Latest Stable Version"></a>
</p>

A PHP library for validating Ecuadorian identification documents:

- **Cedula** (National ID - 10 digits)
- **RUC for Natural Persons** (13 digits, third digit 0-5)
- **RUC for Private Companies** (13 digits, third digit 9)
- **RUC for Public Companies** (13 digits, third digit 6)

## Requirements

- PHP 8.2 or higher

## Installation

```bash
composer require tavo1987/ec-validador-cedula-ruc
```

## Usage

### Basic Setup

```php
require 'vendor/autoload.php';

use Tavo\ValidadorEc;

$validator = new ValidadorEc();
```

### Quick Validation (Static Methods)

For simple validation without creating an instance:

```php
// Validate any document type
if (ValidadorEc::isValid('0926687856')) {
    echo 'Valid document';
}

// Validate specific document types
ValidadorEc::isValidCedula('0926687856');              // true
ValidadorEc::isValidNaturalPersonRuc('0926687856001'); // true
ValidadorEc::isValidPrivateCompanyRuc('0992397535001'); // true
ValidadorEc::isValidPublicCompanyRuc('1760001550001'); // true
```

### Universal Validation (Auto-detect document type)

The `validate()` method automatically detects and validates any document type:

```php
// Cedula (10 digits)
if ($validator->validate('0926687856')) {
    echo 'Valid: ' . $validator->getDocumentType(); // "cedula"
}

// Natural Person RUC (13 digits, third digit 0-5)
if ($validator->validate('0926687856001')) {
    echo 'Valid: ' . $validator->getDocumentType(); // "ruc_natural"
}

// Public Company RUC (13 digits, third digit 6)
if ($validator->validate('1760001550001')) {
    echo 'Valid: ' . $validator->getDocumentType(); // "ruc_public"
}

// Private Company RUC (13 digits, third digit 9)
if ($validator->validate('0992397535001')) {
    echo 'Valid: ' . $validator->getDocumentType(); // "ruc_private"
}
```

### Specific Validation Methods

If you know the document type beforehand:

```php
// Validate Cedula
if ($validator->validateCedula('0926687856')) {
    echo 'Valid Cedula';
} else {
    echo 'Error: ' . $validator->getError();
}

// Validate Natural Person RUC
if ($validator->validateNaturalPersonRuc('0926687856001')) {
    echo 'Valid RUC';
}

// Validate Private Company RUC
if ($validator->validatePrivateCompanyRuc('0992397535001')) {
    echo 'Valid RUC';
}

// Validate Public Company RUC
if ($validator->validatePublicCompanyRuc('1760001550001')) {
    echo 'Valid RUC';
}
```

### Extract Cedula from RUC

For Natural Person RUC, you can extract the cedula portion:

```php
$cedula = $validator->extractCedulaFromRuc('0926687856001');
// Returns: '0926687856'

// Returns null for Private/Public Company RUC (no cedula)
$result = $validator->extractCedulaFromRuc('0992397535001');
// Returns: null

// Returns null for invalid RUC
$result = $validator->extractCedulaFromRuc('invalid');
// Returns: null
```

### Available Constants

```php
ValidadorEc::TYPE_CEDULA       // 'cedula'
ValidadorEc::TYPE_RUC_NATURAL  // 'ruc_natural'
ValidadorEc::TYPE_RUC_PRIVATE  // 'ruc_private'
ValidadorEc::TYPE_RUC_PUBLIC   // 'ruc_public'
```

## API Reference

### Static Methods

| Method | Description |
|--------|-------------|
| `ValidadorEc::isValid(string $number)` | Quick validation of any document type |
| `ValidadorEc::isValidCedula(string $number)` | Quick cedula validation |
| `ValidadorEc::isValidNaturalPersonRuc(string $number)` | Quick natural person RUC validation |
| `ValidadorEc::isValidPrivateCompanyRuc(string $number)` | Quick private company RUC validation |
| `ValidadorEc::isValidPublicCompanyRuc(string $number)` | Quick public company RUC validation |

### Instance Methods

| Method | Description |
|--------|-------------|
| `validate(string $number)` | Auto-detect and validate any document |
| `validateCedula(string $number)` | Validate Cedula (10 digits) |
| `validateNaturalPersonRuc(string $number)` | Validate Natural Person RUC (13 digits) |
| `validatePrivateCompanyRuc(string $number)` | Validate Private Company RUC (13 digits) |
| `validatePublicCompanyRuc(string $number)` | Validate Public Company RUC (13 digits) |
| `extractCedulaFromRuc(string $ruc)` | Extract cedula from natural person RUC |
| `getDocumentType()` | Get detected document type after validation |
| `getError()` | Get error message from last validation |

## Document Structure

| Document Type | Digits | Third Digit | Algorithm |
|--------------|--------|-------------|-----------|
| Cedula | 10 | 0-5* | Modulo 10 |
| RUC Natural Person | 13 | 0-5* | Modulo 10 |
| RUC Private Company | 13 | 9 | Modulo 11 |
| RUC Public Company | 13 | 6 | Modulo 11 |

*For province code 30, third digit validation is skipped (see below).

### Province Codes

The first two digits represent the province:

| Code | Province |
|------|----------|
| 01 | Azuay |
| 02 | Bolivar |
| 03 | Cañar |
| 04 | Carchi |
| 05 | Cotopaxi |
| 06 | Chimborazo |
| 07 | El Oro |
| 08 | Esmeraldas |
| 09 | Guayas |
| 10 | Imbabura |
| 11 | Loja |
| 12 | Los Rios |
| 13 | Manabi |
| 14 | Morona Santiago |
| 15 | Napo |
| 16 | Pastaza |
| 17 | Pichincha |
| 18 | Tungurahua |
| 19 | Zamora Chinchipe |
| 20 | Galapagos |
| 21 | Sucumbios |
| 22 | Orellana |
| 23 | Santo Domingo de los Tsachilas |
| 24 | Santa Elena |
| **30** | **Foreign residents** |

### Foreign Residents (Code 30)

Province code 30 is for "ecuatorianos registrados en el exterior" (Ecuadorians abroad) and foreign residents:

- Third digit validation is skipped
- Check digit (Modulo 10) validation is still performed

## Validation Limitations

This library uses algorithmic validation (Modulo 10/11). Known limitations:

1. **Foreign natural persons without cedula**: RUCs for foreigners may not follow standard validation. Verify through SRI web services.

2. **Critical applications**: Consider complementing with official SRI database queries.

## Testing

```bash
# Run all tests
./vendor/bin/phpunit

# Run with coverage
./vendor/bin/phpunit --coverage-text
```

## Changelog

### v2.0.0
- **Breaking**: Minimum PHP version is now 8.2
- **Breaking**: All method names now in English
- **Breaking**: Constants renamed (`TIPO_*` → `TYPE_*`)
- **Breaking**: Removed all Spanish method names
- New universal `validate()` method with auto-detection
- New `getDocumentType()` method
- New static methods for quick validation (`isValid()`, `isValidCedula()`, etc.)
- New `extractCedulaFromRuc()` method to extract cedula from natural person RUC
- Support for province code 30 (foreign residents)
- Class is now `final`
- Optimized with `match` expressions
- 93 comprehensive tests

### v1.0.2
- Fixed namespace issues

### v1.0.0
- Initial release

## License

MIT License - see [LICENSE](LICENCE) file for details.

## Authors

**Edwin Ramirez**
- Twitter: [@edwin_tavo](https://twitter.com/edwin_tavo)
- GitHub: [@tavo1987](https://github.com/tavo1987)

**Bryan Suarez**
- Twitter: [@BryanSC_7](https://twitter.com/BryanSC_7)
