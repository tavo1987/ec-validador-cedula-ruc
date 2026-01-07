# Update Plan: ec-validador-cedula-ruc Library

## Current State Analysis

### Library Overview
- **Package**: `tavo1987/ec-validador-cedula-ruc`
- **Purpose**: Validates Ecuadorian ID (cedula), Natural Person RUC, Private Company RUC, and Public Company RUC
- **Current PHP requirement**: `>=5.4.0`
- **PHPUnit version**: `^6.1` (outdated)
- **Last update**: 7+ years ago

### Open Issues

#### Issue #7 - Foreign ID Validation Error (Critical)
- **Reporter**: CarmenBastidas (Feb 2024)
- **Problem**: Error message says "third digit is not correct" for foreign IDs
- **Root cause**: Missing `break` statement in switch case causes fall-through
- **Solution**: Add proper `break` statements in `validarTercerDigito()` method

#### Issue #3 - RUC Validation Failure
- **Reporter**: serobalino (Oct 2021)
- **Problem**: RUC `0962893970001` fails validation but should be valid
- **Analysis needed**: Verify if this is a valid RUC and fix algorithm if needed

#### Issue #5 - Video Request
- **Status**: Documentation request, not a bug

---

## Update Plan

### Phase 1: PHP 8.x Compatibility

#### 1.1 Update composer.json
- Change PHP requirement from `>=5.4.0` to `^8.1`
- Update PHPUnit from `^6.1` to `^10.0` or `^11.0`
- Add PHP-CS-Fixer for code style (optional)

#### 1.2 Update phpunit.xml
- Remove deprecated attributes (`backupStaticAttributes`, `convertErrorsToExceptions`, etc.)
- Update to modern PHPUnit XML schema
- Update coverage configuration to new format

#### 1.3 PHP 8.x Language Features
- Add type hints for parameters
- Add return type declarations
- Use constructor property promotion where applicable
- Replace deprecated functions if any

---

### Phase 2: Bug Fixes

#### 2.1 Fix Issue #7 - Switch Case Fall-through
**File**: `src/ValidadorEc.php`
**Method**: `validarTercerDigito()`

Current switch structure may have fall-through issues. Need to verify and add explicit `break` statements.

#### 2.2 Fix Issue #3 - RUC Validation
**RUC to test**: `0962893970001`

Analysis:
- First 2 digits: `09` (Guayas province - valid)
- Third digit: `6` - This indicates PUBLIC company RUC, NOT natural person
- This RUC should be validated with `validarRucSociedadPublica()`, not `validarRucPersonaNatural()`

**Solution**:
1. Add a unified `validar()` method that auto-detects document type
2. Document proper usage for each RUC type

---

### Phase 3: New Features

#### 3.1 Universal Validation Method
Add a new method `validar($numero)` that:
- Auto-detects if input is cedula (10 digits) or RUC (13 digits)
- For RUC, checks third digit to determine type:
  - 0-5: Natural Person RUC
  - 6: Public Company RUC
  - 9: Private Company RUC
- Returns validation result and document type

#### 3.2 Enhanced Error Messages
- Add English error messages option
- Improve error message clarity

---

### Phase 4: Test Suite Enhancement

#### 4.1 Update Existing Tests
- Migrate to PHPUnit 10/11 syntax
- Update TestCase to extend `PHPUnit\Framework\TestCase`
- Fix deprecated assertion methods

#### 4.2 New Test Cases
- Add test for RUC `0962893970001` (Issue #3)
- Add tests for province code 30 (foreign residents)
- Add tests for boundary conditions
- Add tests for new universal validation method

---

### Phase 5: Documentation

#### 5.1 Translate README to English
- Complete translation of all sections
- Keep examples and code samples
- Update badges if needed

#### 5.2 Update CHANGELOG
- Document all changes in this update

---

## Files to Modify

| File | Changes |
|------|---------|
| `composer.json` | Update PHP version, PHPUnit version |
| `phpunit.xml` | Modernize configuration |
| `src/ValidadorEc.php` | Add type hints, fix bugs, add new methods |
| `tests/TestCase.php` | Update for PHPUnit 10+ |
| `tests/ValidadorCedulaTest.php` | Add new test cases |
| `tests/ValidadorRucPersonaNaturalTest.php` | Add new test cases |
| `tests/ValidadorRucSociedadPrivadaTest.php` | Add new test cases |
| `tests/ValidadorRucSociedadPublicaTest.php` | Add new test cases, add Issue #3 case |
| `readme.md` | Translate to English |

---

## Technical Notes

### Ecuadorian ID System Reference

| Document Type | Digits | Third Digit | Algorithm |
|--------------|--------|-------------|-----------|
| Cedula | 10 | 0-5 | Modulo 10 |
| RUC Natural Person | 13 | 0-5 | Modulo 10 |
| RUC Private Company | 13 | 9 | Modulo 11 |
| RUC Public Company | 13 | 6 | Modulo 11 |

### Province Codes
- 01-24: Ecuadorian provinces
- 30: Foreign residents (need to verify if library supports this)

---

## Success Criteria

- [ ] All tests pass on PHP 8.1, 8.2, 8.3, 8.4
- [ ] Issue #7 resolved (switch case fix)
- [ ] Issue #3 resolved (RUC validation)
- [ ] New universal validation method working
- [ ] README translated to English
- [ ] Code follows PSR-12 standards
