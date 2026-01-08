<?php

declare(strict_types=1);

namespace Tavo\Tests;

class PrivateCompanyRucValidationTest extends TestCase
{
    public function test_empty_ruc_fails(): void
    {
        $this->assertFalse($this->validator->validatePrivateCompanyRuc(''));
        $this->assertEquals('Value cannot be empty', $this->validator->getError());

        $this->assertFalse($this->validator->validatePrivateCompanyRuc());
        $this->assertEquals('Value cannot be empty', $this->validator->getError());
    }

    public function test_integer_type_fails_due_to_leading_zero_loss(): void
    {
        $ruc = (int) '0992397535001';

        $this->assertFalse($this->validator->validatePrivateCompanyRuc((string) $ruc));
        $this->assertEquals('Value must have 13 characters', $this->validator->getError());
    }

    public function test_letters_fail(): void
    {
        $this->assertFalse($this->validator->validatePrivateCompanyRuc('abcd'));
        $this->assertEquals('Value can only contain digits', $this->validator->getError());
    }

    public function test_special_characters_fail(): void
    {
        $this->assertFalse($this->validator->validatePrivateCompanyRuc('*@-.#'));
        $this->assertEquals('Value can only contain digits', $this->validator->getError());
    }

    public function test_negative_numbers_fail(): void
    {
        $this->assertFalse($this->validator->validatePrivateCompanyRuc('-0992397535001'));
        $this->assertEquals('Value can only contain digits', $this->validator->getError());
    }

    public function test_decimal_numbers_fail(): void
    {
        $this->assertFalse($this->validator->validatePrivateCompanyRuc('099,2397535001'));
        $this->assertEquals('Value can only contain digits', $this->validator->getError());
    }

    public function test_more_than_thirteen_digits_fails(): void
    {
        $this->assertFalse($this->validator->validatePrivateCompanyRuc('0992397535001998'));
        $this->assertEquals('Value must have 13 characters', $this->validator->getError());
    }

    public function test_invalid_province_code_fails(): void
    {
        $this->assertFalse($this->validator->validatePrivateCompanyRuc('9992397535001'));
        $this->assertEquals('Province code (first two digits) must be between 01-24 or 30', $this->validator->getError());
    }

    public function test_third_digit_must_be_nine(): void
    {
        $this->assertFalse($this->validator->validatePrivateCompanyRuc('0982397535001'));
        $this->assertEquals('Third digit must be 9 for private companies', $this->validator->getError());
    }

    public function test_third_digit_six_fails(): void
    {
        // Third digit 6 is for public companies
        $this->assertFalse($this->validator->validatePrivateCompanyRuc('0962397535001'));
        $this->assertEquals('Third digit must be 9 for private companies', $this->validator->getError());
    }

    public function test_third_digit_natural_person_fails(): void
    {
        // Third digit 0-5 is for natural persons
        $this->assertFalse($this->validator->validatePrivateCompanyRuc('0902397535001'));
        $this->assertEquals('Third digit must be 9 for private companies', $this->validator->getError());
    }

    public function test_establishment_code_zero_fails(): void
    {
        $this->assertFalse($this->validator->validatePrivateCompanyRuc('0992397535000'));
        $this->assertEquals('Establishment code cannot be 0', $this->validator->getError());
    }

    public function test_invalid_check_digit_fails(): void
    {
        // Use RUC with 6-digit sequential (<=999999) to ensure check digit validation runs
        // 0990999996001 is valid (check digit 6), 0990999997001 has wrong check digit
        $this->assertFalse($this->validator->validatePrivateCompanyRuc('0990999997001'));
        $this->assertEquals('Check digit validation failed', $this->validator->getError());
    }

    public function test_valid_private_company_ruc(): void
    {
        $this->assertTrue($this->validator->validatePrivateCompanyRuc('0992397535001'));
    }

    public function test_multiple_establishments_are_valid(): void
    {
        $this->assertTrue($this->validator->validatePrivateCompanyRuc('0992397535002'));
        $this->assertTrue($this->validator->validatePrivateCompanyRuc('0992397535999'));
    }

    // ==================== Extended Sequential RUC Tests ====================

    public function test_valid_private_company_ruc_with_extended_sequential(): void
    {
        // RUC with 7-digit sequential (>999999)
        // Per SRI rules, these RUCs don't have a validatable check digit
        $this->assertTrue($this->validator->validatePrivateCompanyRuc('1791000001001'));
    }

    public function test_extended_sequential_ruc_detected_correctly_by_validate(): void
    {
        $this->assertTrue($this->validator->validate('1791000001001'));
        $this->assertEquals('ruc_private', $this->validator->getDocumentType());
    }

    public function test_traditional_6digit_sequential_still_validates_check_digit(): void
    {
        // Traditional RUC with 6-digit sequential (<=999999) should still validate check digit
        // 0990999996001 is valid (check digit 6), 0990999998001 has wrong check digit
        $this->assertFalse($this->validator->validatePrivateCompanyRuc('0990999998001'));
        $this->assertEquals('Check digit validation failed', $this->validator->getError());
    }

    public function test_traditional_ruc_with_sequential_starting_with_zero(): void
    {
        // Sequential starting with 0 is always traditional (max 0999999 < 1000000)
        // 0990999996001 has sequential 099999, check digit 6
        $this->assertTrue($this->validator->validatePrivateCompanyRuc('0990999996001'));
    }

    public function test_extended_sequential_at_boundary_1000000(): void
    {
        // Sequential exactly 1000000 (first extended sequential)
        // Province 09, type 9, sequential 1000000, establishment 001
        $this->assertTrue($this->validator->validatePrivateCompanyRuc('0991000000001'));
    }
}
