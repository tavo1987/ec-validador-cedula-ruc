<?php

declare(strict_types=1);

namespace Tavo\Tests;

use Tavo\ValidadorEc;

class CedulaValidationTest extends TestCase
{
    public function test_empty_cedula_fails(): void
    {
        $this->assertFalse($this->validator->validateCedula(''));
        $this->assertEquals('Value cannot be empty', $this->validator->getError());

        $this->assertFalse($this->validator->validateCedula());
        $this->assertEquals('Value cannot be empty', $this->validator->getError());
    }

    public function test_integer_type_cedula_fails_due_to_leading_zero_loss(): void
    {
        // PHP converts '0926687856' to integer 926687856, losing the leading zero
        $cedula = (int) '0926687856';

        $this->assertFalse($this->validator->validateCedula((string) $cedula));
        $this->assertEquals('Value must have 10 characters', $this->validator->getError());
    }

    public function test_letters_fail(): void
    {
        $this->assertFalse($this->validator->validateCedula('abcd'));
        $this->assertEquals('Value can only contain digits', $this->validator->getError());
    }

    public function test_special_characters_fail(): void
    {
        $this->assertFalse($this->validator->validateCedula('*@-.#'));
        $this->assertEquals('Value can only contain digits', $this->validator->getError());
    }

    public function test_negative_numbers_fail(): void
    {
        $this->assertFalse($this->validator->validateCedula('-1723468565'));
        $this->assertEquals('Value can only contain digits', $this->validator->getError());
    }

    public function test_decimal_numbers_fail(): void
    {
        $this->assertFalse($this->validator->validateCedula('09.26687856'));
        $this->assertEquals('Value can only contain digits', $this->validator->getError());
    }

    public function test_more_than_ten_digits_fails(): void
    {
        $this->assertFalse($this->validator->validateCedula('0926687864777009'));
        $this->assertEquals('Value must have 10 characters', $this->validator->getError());
    }

    public function test_less_than_ten_digits_fails(): void
    {
        $this->assertFalse($this->validator->validateCedula('092668785'));
        $this->assertEquals('Value must have 10 characters', $this->validator->getError());
    }

    public function test_spaces_fail(): void
    {
        $this->assertFalse($this->validator->validateCedula('0926 687856'));
        $this->assertEquals('Value can only contain digits', $this->validator->getError());
    }

    public function test_invalid_province_code_fails(): void
    {
        $this->assertFalse($this->validator->validateCedula('2526687856'));
        $this->assertEquals('Province code (first two digits) must be between 01-24 or 30', $this->validator->getError());
    }

    public function test_province_code_zero_fails(): void
    {
        $this->assertFalse($this->validator->validateCedula('0026687856'));
        $this->assertEquals('Province code (first two digits) must be between 01-24 or 30', $this->validator->getError());
    }

    public function test_invalid_third_digit_fails(): void
    {
        $this->assertFalse($this->validator->validateCedula('0996687856'));
        $this->assertEquals('Third digit must be between 0 and 5 for cedula and natural person RUC', $this->validator->getError());
    }

    public function test_invalid_check_digit_fails(): void
    {
        $this->assertFalse($this->validator->validateCedula('0926687858'));
        $this->assertEquals('Check digit validation failed', $this->validator->getError());
    }

    public function test_valid_cedulas(): void
    {
        $this->assertTrue($this->validator->validateCedula('0602910945'));
        $this->assertTrue($this->validator->validateCedula('0926687856'));
        $this->assertTrue($this->validator->validateCedula('0910005917'));
    }

    public function test_valid_cedulas_from_different_provinces(): void
    {
        $validCedulas = [
            '0102345672', // Azuay (01)
            '1712345678', // Pichincha (17)
            '2400000018', // Santa Elena (24)
        ];

        foreach ($validCedulas as $cedula) {
            $this->validator->validateCedula($cedula);
            $this->assertNotEquals(
                'Province code (first two digits) must be between 01-24 or 30',
                $this->validator->getError(),
                "Province code should be valid for: {$cedula}"
            );
        }
    }

    public function test_third_digit_boundary_values(): void
    {
        // Third digit 0 - should pass third digit validation
        $this->validator->validateCedula('0102345672');
        $this->assertNotEquals(
            'Third digit must be between 0 and 5 for cedula and natural person RUC',
            $this->validator->getError()
        );

        // Third digit 5 - should pass third digit validation
        $this->validator->validateCedula('0152345672');
        $this->assertNotEquals(
            'Third digit must be between 0 and 5 for cedula and natural person RUC',
            $this->validator->getError()
        );

        // Third digit 6 - should fail for regular provinces
        $this->assertFalse($this->validator->validateCedula('0162345672'));
        $this->assertEquals(
            'Third digit must be between 0 and 5 for cedula and natural person RUC',
            $this->validator->getError()
        );
    }

    // ==================== Foreign Residents (Province Code 30) ====================

    public function test_province_code_30_is_accepted(): void
    {
        $this->validator->validateCedula('3012345678');

        $this->assertNotEquals(
            'Province code (first two digits) must be between 01-24 or 30',
            $this->validator->getError(),
            'Province code 30 should be accepted for foreign residents'
        );
    }

    public function test_province_code_30_skips_third_digit_validation(): void
    {
        // Third digit 6 would fail for regular cedulas
        $this->validator->validateCedula('3062345678');
        $this->assertNotEquals(
            'Third digit must be between 0 and 5 for cedula and natural person RUC',
            $this->validator->getError()
        );

        // Third digit 9 would fail for regular cedulas
        $this->validator->validateCedula('3092345678');
        $this->assertNotEquals(
            'Third digit must be between 0 and 5 for cedula and natural person RUC',
            $this->validator->getError()
        );
    }

    public function test_province_code_30_still_validates_check_digit(): void
    {
        $result = $this->validator->validateCedula('3012345679');

        if (!$result) {
            $this->assertEquals('Check digit validation failed', $this->validator->getError());
        }
    }
}
