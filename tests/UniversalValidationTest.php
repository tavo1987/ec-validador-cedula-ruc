<?php

declare(strict_types=1);

namespace Tavo\Tests;

use Tavo\ValidadorEc;

/**
 * Test suite for the universal validate() method.
 *
 * Auto-detects document type:
 * - 10 digits: Cedula
 * - 13 digits with third digit 0-5: Natural Person RUC
 * - 13 digits with third digit 6: Public Company RUC
 * - 13 digits with third digit 9: Private Company RUC
 */
class UniversalValidationTest extends TestCase
{
    // ==================== Basic Validation ====================

    public function test_empty_value_fails(): void
    {
        $this->assertFalse($this->validator->validate(''));
        $this->assertEquals('Value cannot be empty', $this->validator->getError());
    }

    public function test_non_digit_value_fails(): void
    {
        $this->assertFalse($this->validator->validate('abc123'));
        $this->assertEquals('Value can only contain digits', $this->validator->getError());
    }

    public function test_invalid_length_fails(): void
    {
        // Too short
        $this->assertFalse($this->validator->validate('12345'));
        $this->assertEquals(
            'Invalid document length. Cedula must have 10 digits, RUC must have 13 digits',
            $this->validator->getError()
        );

        // Between 10 and 13
        $this->assertFalse($this->validator->validate('12345678901'));
        $this->assertEquals(
            'Invalid document length. Cedula must have 10 digits, RUC must have 13 digits',
            $this->validator->getError()
        );

        // Too long
        $this->assertFalse($this->validator->validate('12345678901234'));
        $this->assertEquals(
            'Invalid document length. Cedula must have 10 digits, RUC must have 13 digits',
            $this->validator->getError()
        );
    }

    // ==================== Cedula Auto-Detection ====================

    public function test_detects_and_validates_cedula(): void
    {
        $this->assertTrue($this->validator->validate('0926687856'));
        $this->assertEquals(ValidadorEc::TYPE_CEDULA, $this->validator->getDocumentType());
    }

    public function test_invalid_cedula_returns_correct_type(): void
    {
        $this->assertFalse($this->validator->validate('0926687858'));
        $this->assertEquals(ValidadorEc::TYPE_CEDULA, $this->validator->getDocumentType());
    }

    // ==================== RUC Natural Person Auto-Detection ====================

    public function test_detects_natural_person_ruc(): void
    {
        $this->assertTrue($this->validator->validate('0602910945001'));
        $this->assertEquals(ValidadorEc::TYPE_RUC_NATURAL, $this->validator->getDocumentType());
    }

    public function test_detects_ruc_natural_with_third_digit_0(): void
    {
        $this->validator->validate('0102345678001');
        $this->assertEquals(ValidadorEc::TYPE_RUC_NATURAL, $this->validator->getDocumentType());
    }

    public function test_detects_ruc_natural_with_third_digit_5(): void
    {
        $this->validator->validate('0152345678001');
        $this->assertEquals(ValidadorEc::TYPE_RUC_NATURAL, $this->validator->getDocumentType());
    }

    // ==================== RUC Public Company Auto-Detection ====================

    public function test_detects_public_company_ruc(): void
    {
        $this->assertTrue($this->validator->validate('1760001550001'));
        $this->assertEquals(ValidadorEc::TYPE_RUC_PUBLIC, $this->validator->getDocumentType());
    }

    public function test_issue_3_ruc_auto_detected_as_public(): void
    {
        // RUC 0962893970001 is detected as public (third digit 6) but fails check digit
        $this->assertFalse($this->validator->validate('0962893970001'));
        $this->assertEquals(ValidadorEc::TYPE_RUC_PUBLIC, $this->validator->getDocumentType());
    }

    // ==================== RUC Private Company Auto-Detection ====================

    public function test_detects_private_company_ruc(): void
    {
        $this->assertTrue($this->validator->validate('0992397535001'));
        $this->assertEquals(ValidadorEc::TYPE_RUC_PRIVATE, $this->validator->getDocumentType());
    }

    // ==================== Invalid Third Digit ====================

    public function test_invalid_third_digit_7_fails(): void
    {
        $this->assertFalse($this->validator->validate('0172345678001'));
        $this->assertEquals(
            'Invalid third digit for RUC. Must be 0-5 (natural), 6 (public), or 9 (private)',
            $this->validator->getError()
        );
    }

    public function test_invalid_third_digit_8_fails(): void
    {
        $this->assertFalse($this->validator->validate('0182345678001'));
        $this->assertEquals(
            'Invalid third digit for RUC. Must be 0-5 (natural), 6 (public), or 9 (private)',
            $this->validator->getError()
        );
    }

    // ==================== Whitespace Handling ====================

    public function test_trims_whitespace(): void
    {
        $this->assertTrue($this->validator->validate('  0926687856  '));
    }

    // ==================== Document Type Getter ====================

    public function test_document_type_empty_before_validation(): void
    {
        $validator = new ValidadorEc();
        $this->assertEquals('', $validator->getDocumentType());
    }

    public function test_document_type_returns_correct_type_after_validation(): void
    {
        // Cedula
        $this->validator->validate('0926687856');
        $this->assertEquals(ValidadorEc::TYPE_CEDULA, $this->validator->getDocumentType());

        // RUC Natural
        $this->validator->validate('0602910945001');
        $this->assertEquals(ValidadorEc::TYPE_RUC_NATURAL, $this->validator->getDocumentType());

        // RUC Public
        $this->validator->validate('1760001550001');
        $this->assertEquals(ValidadorEc::TYPE_RUC_PUBLIC, $this->validator->getDocumentType());

        // RUC Private
        $this->validator->validate('0992397535001');
        $this->assertEquals(ValidadorEc::TYPE_RUC_PRIVATE, $this->validator->getDocumentType());
    }

    public function test_document_type_available_even_on_validation_failure(): void
    {
        $this->validator->validate('0926687858'); // Invalid check digit
        $this->assertEquals(ValidadorEc::TYPE_CEDULA, $this->validator->getDocumentType());
    }
}
