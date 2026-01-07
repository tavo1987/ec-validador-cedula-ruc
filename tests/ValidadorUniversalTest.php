<?php

declare(strict_types=1);

namespace Tavo\Tests;

use Tavo\ValidadorEc;

/**
 * Test suite for the universal validar() method.
 *
 * This method auto-detects document type and validates accordingly:
 * - 10 digits: Cedula
 * - 13 digits with third digit 0-5: Natural Person RUC
 * - 13 digits with third digit 6: Public Company RUC
 * - 13 digits with third digit 9: Private Company RUC
 */
class ValidadorUniversalTest extends TestCase
{
    // ==================== BASIC VALIDATION ====================

    public function test_validar_empty_value_fails(): void
    {
        $resultado = $this->validador->validar('');
        $this->assertFalse($resultado);
        $this->assertEquals('Value cannot be empty', $this->validador->getError());
    }

    public function test_validar_non_digit_value_fails(): void
    {
        $resultado = $this->validador->validar('abc123');
        $this->assertFalse($resultado);
        $this->assertEquals('Value can only contain digits', $this->validador->getError());
    }

    public function test_validar_invalid_length_fails(): void
    {
        // Too short
        $resultado = $this->validador->validar('12345');
        $this->assertFalse($resultado);
        $this->assertEquals('Invalid document length. Cedula must have 10 digits, RUC must have 13 digits', $this->validador->getError());

        // Between 10 and 13
        $resultado = $this->validador->validar('12345678901');
        $this->assertFalse($resultado);
        $this->assertEquals('Invalid document length. Cedula must have 10 digits, RUC must have 13 digits', $this->validador->getError());

        // Too long
        $resultado = $this->validador->validar('12345678901234');
        $this->assertFalse($resultado);
        $this->assertEquals('Invalid document length. Cedula must have 10 digits, RUC must have 13 digits', $this->validador->getError());
    }

    // ==================== CEDULA AUTO-DETECTION ====================

    public function test_validar_detects_and_validates_cedula(): void
    {
        $cedula = '0926687856';

        $resultado = $this->validador->validar($cedula);

        $this->assertTrue($resultado);
        $this->assertEquals(ValidadorEc::TIPO_CEDULA, $this->validador->getTipoDocumento());
    }

    public function test_validar_invalid_cedula_returns_error(): void
    {
        $cedula = '0926687858'; // Invalid check digit

        $resultado = $this->validador->validar($cedula);

        $this->assertFalse($resultado);
        $this->assertEquals(ValidadorEc::TIPO_CEDULA, $this->validador->getTipoDocumento());
    }

    // ==================== RUC NATURAL PERSON AUTO-DETECTION ====================

    public function test_validar_detects_ruc_natural_person(): void
    {
        $ruc = '0602910945001';

        $resultado = $this->validador->validar($ruc);

        $this->assertTrue($resultado);
        $this->assertEquals(ValidadorEc::TIPO_RUC_NATURAL, $this->validador->getTipoDocumento());
    }

    public function test_validar_detects_ruc_natural_with_third_digit_0(): void
    {
        // Third digit 0 should be natural person RUC
        $ruc = '0102345678001';
        $this->validador->validar($ruc);
        $this->assertEquals(ValidadorEc::TIPO_RUC_NATURAL, $this->validador->getTipoDocumento());
    }

    public function test_validar_detects_ruc_natural_with_third_digit_5(): void
    {
        // Third digit 5 should be natural person RUC
        $ruc = '0152345678001';
        $this->validador->validar($ruc);
        $this->assertEquals(ValidadorEc::TIPO_RUC_NATURAL, $this->validador->getTipoDocumento());
    }

    // ==================== RUC PUBLIC COMPANY AUTO-DETECTION ====================

    public function test_validar_detects_ruc_public_company(): void
    {
        $ruc = '1760001550001';

        $resultado = $this->validador->validar($ruc);

        $this->assertTrue($resultado);
        $this->assertEquals(ValidadorEc::TIPO_RUC_PUBLICA, $this->validador->getTipoDocumento());
    }

    /**
     * Issue #3 - Using universal validator to auto-detect RUC type.
     *
     * The RUC 0962893970001 is detected as public company (third digit 6),
     * but fails validation because the check digit is incorrect.
     * See ValidadorRucSociedadPublicaTest for full analysis.
     */
    public function test_validar_issue_3_ruc_auto_detected_as_public(): void
    {
        $ruc = '0962893970001';

        $resultado = $this->validador->validar($ruc);

        // RUC is detected as public company but fails check digit validation
        $this->assertFalse($resultado);
        $this->assertEquals(ValidadorEc::TIPO_RUC_PUBLICA, $this->validador->getTipoDocumento());
    }

    // ==================== RUC PRIVATE COMPANY AUTO-DETECTION ====================

    public function test_validar_detects_ruc_private_company(): void
    {
        $ruc = '0992397535001';

        $resultado = $this->validador->validar($ruc);

        $this->assertTrue($resultado);
        $this->assertEquals(ValidadorEc::TIPO_RUC_PRIVADA, $this->validador->getTipoDocumento());
    }

    // ==================== INVALID THIRD DIGIT ====================

    public function test_validar_invalid_third_digit_7_fails(): void
    {
        $ruc = '0172345678001';

        $resultado = $this->validador->validar($ruc);

        $this->assertFalse($resultado);
        $this->assertEquals('Invalid third digit for RUC. Must be 0-5 (natural), 6 (public), or 9 (private)', $this->validador->getError());
    }

    public function test_validar_invalid_third_digit_8_fails(): void
    {
        $ruc = '0182345678001';

        $resultado = $this->validador->validar($ruc);

        $this->assertFalse($resultado);
        $this->assertEquals('Invalid third digit for RUC. Must be 0-5 (natural), 6 (public), or 9 (private)', $this->validador->getError());
    }

    // ==================== WHITESPACE HANDLING ====================

    public function test_validar_trims_whitespace(): void
    {
        $cedula = '  0926687856  ';

        $resultado = $this->validador->validar($cedula);

        $this->assertTrue($resultado);
    }

    // ==================== DOCUMENT TYPE GETTER ====================

    public function test_get_tipo_documento_empty_before_validation(): void
    {
        $validador = new ValidadorEc();
        $this->assertEquals('', $validador->getTipoDocumento());
    }

    public function test_get_tipo_documento_returns_type_after_successful_validation(): void
    {
        // Cedula
        $this->validador->validar('0926687856');
        $this->assertEquals(ValidadorEc::TIPO_CEDULA, $this->validador->getTipoDocumento());

        // RUC Natural
        $this->validador->validar('0602910945001');
        $this->assertEquals(ValidadorEc::TIPO_RUC_NATURAL, $this->validador->getTipoDocumento());

        // RUC Public
        $this->validador->validar('1760001550001');
        $this->assertEquals(ValidadorEc::TIPO_RUC_PUBLICA, $this->validador->getTipoDocumento());

        // RUC Private
        $this->validador->validar('0992397535001');
        $this->assertEquals(ValidadorEc::TIPO_RUC_PRIVADA, $this->validador->getTipoDocumento());
    }

    public function test_get_tipo_documento_returns_type_even_on_failed_validation(): void
    {
        // Invalid cedula (wrong check digit)
        $this->validador->validar('0926687858');
        $this->assertEquals(ValidadorEc::TIPO_CEDULA, $this->validador->getTipoDocumento());
    }
}
