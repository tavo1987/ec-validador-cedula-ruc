# Ecuador ID (Cedula) and RUC Validator

<p align="center"><img src="http://res.cloudinary.com/edwin/image/upload/v1496095463/cedulaLogo_lmct8r.png"/></p>

<p align="center">
<a href="https://packagist.org/packages/tavo1987/ec-validador-cedula-ruc"><img src="https://img.shields.io/badge/PHP-8.1+-blue.svg?style=flat-square"></a>
<a href="https://packagist.org/packages/tavo1987/ec-validador-cedula-ruc"><img src="https://img.shields.io/github/license/mashape/apistatus.svg?style=flat-square"></a>
<a href="https://packagist.org/packages/tavo1987/ec-validador-cedula-ruc"><img src="https://poser.pugx.org/tavo1987/ec-validador-cedula-ruc/downloads" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/tavo1987/ec-validador-cedula-ruc"><img src="https://poser.pugx.org/tavo1987/ec-validador-cedula-ruc/v/stable" alt="Latest Stable Version"></a>
</p>

This package provides an easy way to validate Ecuadorian identification documents:

- **Cedula** (National ID - 10 digits)
- **RUC for Natural Persons** (13 digits, third digit 0-5)
- **RUC for Private Companies** (13 digits, third digit 9)
- **RUC for Public Companies** (13 digits, third digit 6)

## Introduction

This package is based on the repository [validacion-cedula-ruc-ecuador](https://github.com/diaspar/validacion-cedula-ruc-ecuador) created by [diaspar](https://github.com/diaspar), modified to be easily installable and usable in any PHP project via Composer.

For more information about the validation logic, visit this article: [How to validate Cedula and RUC in Ecuador](https://medium.com/@bryansuarez/c%C3%B3mo-validar-c%C3%A9dula-y-ruc-en-ecuador-b62c5666186f) (Spanish).

## Requirements

- PHP 8.1 or higher

## Installation

```bash
composer require tavo1987/ec-validador-cedula-ruc
```

## Usage

### Basic Setup

First, make sure to require Composer's autoload file:

```php
require 'vendor/autoload.php';
```

Then instantiate the class and call the appropriate validation method:

```php
use Tavo\ValidadorEc;

$validator = new ValidadorEc();
```

### Universal Validation (Auto-detect document type)

The `validar()` method automatically detects and validates any document type:

```php
// Auto-detect and validate any document
if ($validator->validar('0926687856')) {
    echo 'Valid document: ' . $validator->getTipoDocumento();
    // Output: Valid document: cedula
}

if ($validator->validar('0926687856001')) {
    echo 'Valid document: ' . $validator->getTipoDocumento();
    // Output: Valid document: ruc_natural
}

if ($validator->validar('1760001550001')) {
    echo 'Valid document: ' . $validator->getTipoDocumento();
    // Output: Valid document: ruc_publica
}

if ($validator->validar('0992397535001')) {
    echo 'Valid document: ' . $validator->getTipoDocumento();
    // Output: Valid document: ruc_privada
}
```

### Specific Validation Methods

If you know the document type beforehand:

```php
// Validate Cedula (National ID)
if ($validator->validarCedula('0926687856')) {
    echo 'Valid Cedula';
} else {
    echo 'Invalid Cedula: ' . $validator->getError();
}

// Validate RUC for Natural Person
if ($validator->validarRucPersonaNatural('0926687856001')) {
    echo 'Valid RUC';
} else {
    echo 'Invalid RUC: ' . $validator->getError();
}

// Validate RUC for Private Company
if ($validator->validarRucSociedadPrivada('0992397535001')) {
    echo 'Valid RUC';
} else {
    echo 'Invalid RUC: ' . $validator->getError();
}

// Validate RUC for Public Company
if ($validator->validarRucSociedadPublica('1760001550001')) {
    echo 'Valid RUC';
} else {
    echo 'Invalid RUC: ' . $validator->getError();
}
```

### Available Constants

```php
ValidadorEc::TIPO_CEDULA       // 'cedula'
ValidadorEc::TIPO_RUC_NATURAL  // 'ruc_natural'
ValidadorEc::TIPO_RUC_PRIVADA  // 'ruc_privada'
ValidadorEc::TIPO_RUC_PUBLICA  // 'ruc_publica'
```

## Document Structure

| Document Type | Digits | Third Digit | Algorithm |
|--------------|--------|-------------|-----------|
| Cedula | 10 | 0-5* | Modulo 10 |
| RUC Natural Person | 13 | 0-5* | Modulo 10 |
| RUC Private Company | 13 | 9 | Modulo 11 |
| RUC Public Company | 13 | 6 | Modulo 11 |

*For province code 30, third digit validation is skipped (see below).

### Province Codes

The first two digits represent the province where the document was issued:

| Code | Province |
|------|----------|
| 01 | Azuay |
| 02 | Bolivar |
| 03 | Canar |
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
| **30** | **Ecuadorians abroad / Foreign residents** |

### Foreign Residents (Code 30)

Province code 30 is reserved for "ecuatorianos registrados en el exterior" (Ecuadorians registered abroad) and foreign residents. For documents with this code:

- The third digit validation is skipped, as different rules may apply
- The check digit (Modulo 10) validation is still performed

## Validation Limitations

This library uses algorithmic validation based on Modulo 10 and Modulo 11 algorithms. However, there are some known limitations:

1. **RUC for foreign natural persons without cedula**: According to Ecuador's SRI (Tax Authority), RUCs issued to foreign natural persons without an Ecuadorian cedula may not follow the standard algorithmic validation. The SRI recommends verifying such RUCs through their official web services.

2. **Extended sequential numbers**: For some RUCs with sequential numbers exceeding 6 digits, the Modulo 11 validation may not apply.

3. **For critical applications**: Consider complementing this validation with a query to the official SRI database or web services.

## Tests

The package includes a comprehensive test suite using PHPUnit. Tests are located in the `tests/` directory.

```bash
# Run all tests
./vendor/bin/phpunit

# Run with coverage
./vendor/bin/phpunit --coverage-text
```

## Contributing

If you find a bug or want to add new functionality, feel free to open an issue or submit a pull request. Please ensure:

1. All tests pass (`./vendor/bin/phpunit`)
2. New features include corresponding tests
3. Code follows PSR-12 coding standards

## Changelog

### v2.0.0
- **Breaking**: Minimum PHP version is now 8.1
- Added `validar()` method for auto-detecting document type
- Added `getTipoDocumento()` method to get the detected document type
- Added support for province code 30 (Ecuadorians abroad / foreign residents)
- For code 30 documents, third digit validation is skipped (PR #6)
- Added PHP 8.x type hints and return types
- Added `match` expression for modern PHP
- Updated PHPUnit to version 10/11
- Improved error messages and documentation
- Added 79 comprehensive tests
- Fixed various edge cases

### v1.0.2
- Fixed namespace issues
- Added namespace prefix to test suite

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
