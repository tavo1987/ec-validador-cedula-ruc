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
        $this->assertFalse($this->validator->validatePrivateCompanyRuc('0992397532001'));
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
}
