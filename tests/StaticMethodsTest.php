<?php

declare(strict_types=1);

namespace Tavo\Tests;

use Tavo\ValidadorEc;

/**
 * Tests for static methods and utility functions.
 */
class StaticMethodsTest extends TestCase
{
    // ==================== Static isValid() ====================

    public function test_static_is_valid_with_cedula(): void
    {
        $this->assertTrue(ValidadorEc::isValid('0926687856'));
    }

    public function test_static_is_valid_with_invalid_cedula(): void
    {
        $this->assertFalse(ValidadorEc::isValid('0926687858'));
    }

    public function test_static_is_valid_with_natural_person_ruc(): void
    {
        $this->assertTrue(ValidadorEc::isValid('0602910945001'));
    }

    public function test_static_is_valid_with_private_company_ruc(): void
    {
        $this->assertTrue(ValidadorEc::isValid('0992397535001'));
    }

    public function test_static_is_valid_with_public_company_ruc(): void
    {
        $this->assertTrue(ValidadorEc::isValid('1760001550001'));
    }

    // ==================== Static isValidCedula() ====================

    public function test_static_is_valid_cedula(): void
    {
        $this->assertTrue(ValidadorEc::isValidCedula('0926687856'));
        $this->assertFalse(ValidadorEc::isValidCedula('0926687858'));
    }

    // ==================== Static isValidNaturalPersonRuc() ====================

    public function test_static_is_valid_natural_person_ruc(): void
    {
        $this->assertTrue(ValidadorEc::isValidNaturalPersonRuc('0602910945001'));
        $this->assertFalse(ValidadorEc::isValidNaturalPersonRuc('0602910945000'));
    }

    // ==================== Static isValidPrivateCompanyRuc() ====================

    public function test_static_is_valid_private_company_ruc(): void
    {
        $this->assertTrue(ValidadorEc::isValidPrivateCompanyRuc('0992397535001'));
        $this->assertFalse(ValidadorEc::isValidPrivateCompanyRuc('0992397535000'));
    }

    // ==================== Static isValidPublicCompanyRuc() ====================

    public function test_static_is_valid_public_company_ruc(): void
    {
        $this->assertTrue(ValidadorEc::isValidPublicCompanyRuc('1760001550001'));
        $this->assertFalse(ValidadorEc::isValidPublicCompanyRuc('1760001550000'));
    }

    // ==================== extractCedulaFromRuc() ====================

    public function test_extract_cedula_from_valid_natural_person_ruc(): void
    {
        $cedula = $this->validator->extractCedulaFromRuc('0926687856001');
        $this->assertEquals('0926687856', $cedula);
    }

    public function test_extract_cedula_from_ruc_with_different_establishment(): void
    {
        $cedula = $this->validator->extractCedulaFromRuc('0926687856002');
        $this->assertEquals('0926687856', $cedula);
    }

    public function test_extract_cedula_returns_null_for_private_company_ruc(): void
    {
        $cedula = $this->validator->extractCedulaFromRuc('0992397535001');
        $this->assertNull($cedula);
    }

    public function test_extract_cedula_returns_null_for_public_company_ruc(): void
    {
        $cedula = $this->validator->extractCedulaFromRuc('1760001550001');
        $this->assertNull($cedula);
    }

    public function test_extract_cedula_returns_null_for_invalid_length(): void
    {
        $this->assertNull($this->validator->extractCedulaFromRuc('0926687856'));
        $this->assertNull($this->validator->extractCedulaFromRuc('092668785600100'));
    }

    public function test_extract_cedula_returns_null_for_non_digits(): void
    {
        $this->assertNull($this->validator->extractCedulaFromRuc('092668785600a'));
    }

    public function test_extract_cedula_returns_null_for_invalid_cedula(): void
    {
        // RUC with invalid cedula (wrong check digit)
        $this->assertNull($this->validator->extractCedulaFromRuc('0926687858001'));
    }
}
