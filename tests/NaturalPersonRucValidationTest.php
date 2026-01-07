<?php

declare(strict_types=1);

namespace Tavo\Tests;

class NaturalPersonRucValidationTest extends TestCase
{
    public function test_empty_ruc_fails(): void
    {
        $this->assertFalse($this->validator->validateNaturalPersonRuc(''));
        $this->assertEquals('Value cannot be empty', $this->validator->getError());

        $this->assertFalse($this->validator->validateNaturalPersonRuc());
        $this->assertEquals('Value cannot be empty', $this->validator->getError());
    }

    public function test_integer_type_fails_due_to_leading_zero_loss(): void
    {
        $ruc = (int) '0926687856001';

        $this->assertFalse($this->validator->validateNaturalPersonRuc((string) $ruc));
        $this->assertEquals('Value must have 13 characters', $this->validator->getError());
    }

    public function test_letters_fail(): void
    {
        $this->assertFalse($this->validator->validateNaturalPersonRuc('abcdsa'));
        $this->assertEquals('Value can only contain digits', $this->validator->getError());
    }

    public function test_more_than_thirteen_digits_fails(): void
    {
        $this->assertFalse($this->validator->validateNaturalPersonRuc('0926687864777009'));
        $this->assertEquals('Value must have 13 characters', $this->validator->getError());
    }

    public function test_invalid_province_code_fails(): void
    {
        $this->assertFalse($this->validator->validateNaturalPersonRuc('2526687856001'));
        $this->assertEquals('Province code (first two digits) must be between 01-24 or 30', $this->validator->getError());
    }

    public function test_invalid_third_digit_fails(): void
    {
        $this->assertFalse($this->validator->validateNaturalPersonRuc('0186687856001'));
        $this->assertEquals('Third digit must be between 0 and 5 for cedula and natural person RUC', $this->validator->getError());
    }

    public function test_establishment_code_zero_fails(): void
    {
        $this->assertFalse($this->validator->validateNaturalPersonRuc('0926687856000'));
        $this->assertEquals('Establishment code cannot be 0', $this->validator->getError());
    }

    public function test_invalid_check_digit_fails(): void
    {
        $this->assertFalse($this->validator->validateNaturalPersonRuc('0926687858001'));
        $this->assertEquals('Check digit validation failed', $this->validator->getError());
    }

    public function test_valid_natural_person_ruc(): void
    {
        $this->assertTrue($this->validator->validateNaturalPersonRuc('0602910945001'));
    }

    public function test_multiple_establishments_are_valid(): void
    {
        $this->assertTrue($this->validator->validateNaturalPersonRuc('0602910945002'));
        $this->assertTrue($this->validator->validateNaturalPersonRuc('0602910945999'));
    }

    public function test_third_digit_boundary_values(): void
    {
        // Third digit 5 should pass
        $this->validator->validateNaturalPersonRuc('0152345678001');
        $this->assertNotEquals(
            'Third digit must be between 0 and 5 for cedula and natural person RUC',
            $this->validator->getError()
        );

        // Third digit 6 should fail
        $this->assertFalse($this->validator->validateNaturalPersonRuc('0162345678001'));
        $this->assertEquals(
            'Third digit must be between 0 and 5 for cedula and natural person RUC',
            $this->validator->getError()
        );
    }

    // ==================== Foreign Residents (Province Code 30) ====================

    public function test_province_code_30_is_accepted(): void
    {
        $this->validator->validateNaturalPersonRuc('3012345678001');

        $this->assertNotEquals(
            'Province code (first two digits) must be between 01-24 or 30',
            $this->validator->getError()
        );
    }

    public function test_province_code_30_skips_third_digit_validation(): void
    {
        $this->validator->validateNaturalPersonRuc('3062345678001');

        $this->assertNotEquals(
            'Third digit must be between 0 and 5 for cedula and natural person RUC',
            $this->validator->getError()
        );
    }
}
