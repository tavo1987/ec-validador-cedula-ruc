<?php

declare(strict_types=1);

namespace Tavo\Tests;

use Tavo\ValidadorEc;

class PublicCompanyRucValidationTest extends TestCase
{
    public function test_empty_ruc_fails(): void
    {
        $this->assertFalse($this->validator->validatePublicCompanyRuc(''));
        $this->assertEquals('Value cannot be empty', $this->validator->getError());

        $this->assertFalse($this->validator->validatePublicCompanyRuc());
        $this->assertEquals('Value cannot be empty', $this->validator->getError());
    }

    public function test_invalid_check_digit_fails(): void
    {
        $this->assertFalse($this->validator->validatePublicCompanyRuc('0960001550001'));
        $this->assertEquals('Check digit validation failed', $this->validator->getError());
    }

    public function test_letters_fail(): void
    {
        $this->assertFalse($this->validator->validatePublicCompanyRuc('asdaddadad'));
        $this->assertEquals('Value can only contain digits', $this->validator->getError());
    }

    public function test_more_than_thirteen_digits_fails(): void
    {
        $this->assertFalse($this->validator->validatePublicCompanyRuc('1760001550001990999'));
        $this->assertEquals('Value must have 13 characters', $this->validator->getError());
    }

    public function test_invalid_province_code_fails(): void
    {
        $this->assertFalse($this->validator->validatePublicCompanyRuc('2760001550001'));
        $this->assertEquals('Province code (first two digits) must be between 01-24 or 30', $this->validator->getError());
    }

    public function test_third_digit_must_be_six(): void
    {
        // Third digit 9 is for private companies
        $this->assertFalse($this->validator->validatePublicCompanyRuc('1790001550001'));
        $this->assertEquals('Third digit must be 6 for public companies', $this->validator->getError());
    }

    public function test_third_digit_nine_fails(): void
    {
        // Third digit 9 is for private companies
        $this->assertFalse($this->validator->validatePublicCompanyRuc('0992893970001'));
        $this->assertEquals('Third digit must be 6 for public companies', $this->validator->getError());
    }

    public function test_third_digit_natural_person_fails(): void
    {
        // Third digit 0-5 is for natural persons
        $this->assertFalse($this->validator->validatePublicCompanyRuc('0902893970001'));
        $this->assertEquals('Third digit must be 6 for public companies', $this->validator->getError());
    }

    public function test_establishment_code_zero_fails(): void
    {
        $this->assertFalse($this->validator->validatePublicCompanyRuc('1760001550000'));
        $this->assertEquals('Establishment code cannot be 0', $this->validator->getError());
    }

    public function test_modulo_11_algorithm_validation(): void
    {
        $this->assertFalse($this->validator->validatePublicCompanyRuc('1760001520001'));
        $this->assertEquals('Check digit validation failed', $this->validator->getError());
    }

    public function test_valid_public_company_ruc(): void
    {
        $this->assertTrue($this->validator->validatePublicCompanyRuc('1760001550001'));
    }

    public function test_multiple_establishments_are_valid(): void
    {
        $this->assertTrue($this->validator->validatePublicCompanyRuc('1760001550002'));
        $this->assertTrue($this->validator->validatePublicCompanyRuc('1760001559999'));
    }

    // ==================== Issue #3 Analysis ====================

    /**
     * Issue #3 - RUC that was reported as not passing validation
     *
     * RUC: 0962893970001
     * - First 2 digits: 09 (Guayas - valid)
     * - Third digit: 6 (Public company)
     * - Check digit (9th position): 7
     *
     * Mathematical verification using Modulo 11:
     * - Coefficients: [3, 2, 7, 6, 5, 4, 3, 2]
     * - Sum: 0*3 + 9*2 + 6*7 + 2*6 + 8*5 + 9*4 + 3*3 + 9*2 = 175
     * - Remainder: 175 % 11 = 10
     * - Expected check digit: 11 - 10 = 1
     * - Actual check digit: 7
     *
     * CONCLUSION: RUC is mathematically INVALID (check digit should be 1, not 7)
     */
    public function test_issue_3_ruc_is_mathematically_invalid(): void
    {
        $result = $this->validator->validatePublicCompanyRuc('0962893970001');

        $this->assertFalse($result, 'RUC 0962893970001 has wrong check digit (7 instead of 1)');
        $this->assertEquals('Check digit validation failed', $this->validator->getError());

        // Also fails as natural person RUC (third digit is 6, not 0-5)
        $this->assertFalse($this->validator->validateNaturalPersonRuc('0962893970001'));
    }
}
