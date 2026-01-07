<?php

declare(strict_types=1);

namespace Tavo\Tests;

class ValidadorRucPersonaNaturalTest extends TestCase
{
    public function test_mostrar_error_cuando_parametro_ruc_persona_natural_este_vacio_o_sea_nulo(): void
    {
        $validarRucPersonaNatural = $this->validador->validarRucPersonaNatural('');
        $this->assertFalse($validarRucPersonaNatural);
        $this->assertEquals('Valor no puede estar vacio', $this->validador->getError());

        $validarRucPersonaNatural = $this->validador->validarRucPersonaNatural();
        $this->assertFalse($validarRucPersonaNatural);
        $this->assertEquals('Valor no puede estar vacio', $this->validador->getError());
    }

    public function test_mostrar_error_si_parametro_pasado_es_un_tipo_de_dato_entero_en_ruc_persona_natural(): void
    {
        $ruc = (int) '0926687856001';
        // parametro con 0 adelante pero como integer, debe dar false ya que php lo convierte a 0
        $validarRucPersonaNatural = $this->validador->validarRucPersonaNatural((string) $ruc);
        $this->assertFalse($validarRucPersonaNatural);
        $this->assertEquals('Valor ingresado debe tener 13 caracteres', $this->validador->getError());
    }

    public function test_validacion_falla_si_se_ingresa_letras_en_ruc_persona_natural(): void
    {
        // parametro debe tener solo digitos
        $ruc = 'abcdsa';
        $validarRucPersonaNatural = $this->validador->validarRucPersonaNatural($ruc);
        $this->assertFalse($validarRucPersonaNatural);
        $this->assertEquals('Valor ingresado solo puede tener dígitos', $this->validador->getError());

        $validarRucPersonaNatural = $this->validador->validarRucPersonaNatural($ruc);
        $this->assertFalse($validarRucPersonaNatural);
        $this->assertEquals('Valor ingresado solo puede tener dígitos', $this->validador->getError());
    }

    public function test_ruc_de_persona_natural_debe_tener_trece_caracteres_exactos(): void
    {
        $ruc = '0926687864777009';

        $validarRucPersonaNatural = $this->validador->validarRucPersonaNatural($ruc);

        $this->assertFalse($validarRucPersonaNatural);
        $this->assertEquals('Valor ingresado debe tener 13 caracteres', $this->validador->getError());
    }

    public function test_revisar_codigo_de_provincia_debe_estar_entre_cero_y_veinticuatro_ruc_persona_natural(): void
    {
        $ruc = '2526687856001';

        $validarRucPersonaNatural = $this->validador->validarRucPersonaNatural($ruc);
        $this->assertFalse($validarRucPersonaNatural);
        $this->assertEquals('Codigo de Provincia (dos primeros dígitos) no deben ser mayor a 24 ni menores a 0', $this->validador->getError());
    }

    public function test_revisar_tercer_digito_debe_ser_mayor_igual_a_cero_y_menor_a_seis_persona_natural(): void
    {
        // revisar tercer digito, debe ser mayor/igual a 0 y menor a 6
        $ruc = '0186687856001';

        $validarRucPersonaNatural = $this->validador->validarRucPersonaNatural($ruc);
        $this->assertFalse($validarRucPersonaNatural);
        $this->assertEquals('Tercer dígito debe ser mayor o igual a 0 y menor a 6 para cédulas y RUC de persona natural', $this->validador->getError());
    }

    public function test_revisar_que_codigo_de_establecimiento_en_los_tres_ultimos_digitos_no_sean_menores_a_uno_persona_natural(): void
    {
        // revisar que codigo de establecimiento (3 últimos dígitos) no sean menores a 1.
        $ruc = '0926687856000';

        $validarRucPersonaNatural = $this->validador->validarRucPersonaNatural($ruc);
        $this->assertFalse($validarRucPersonaNatural);
        $this->assertEquals('Código de establecimiento no puede ser 0', $this->validador->getError());
    }

    public function test_ruc_persona_natural_incorrecta_de_acuerdo_al_algoritmo_modulo_diez(): void
    {
        // ruc persona natural incorrecto de acuerdo a algoritmo modulo10
        $ruc = '0926687858001';

        $validarRucPersonaNatural = $this->validador->validarRucPersonaNatural($ruc);
        $this->assertFalse($validarRucPersonaNatural);
        $this->assertEquals('Dígitos iniciales no validan contra Dígito Idenficador', $this->validador->getError());
    }

    public function test_revisar_que_ruc_correctos_validen_persona_natural(): void
    {
        // revisar que numeros ruc correctas validen
        $ruc = '0602910945001';
        $validarRucPersonaNatural = $this->validador->validarRucPersonaNatural($ruc);
        $this->assertTrue($validarRucPersonaNatural);

        $validarRucPersonaNatural = $this->validador->validarRucPersonaNatural($ruc);
        $this->assertTrue($validarRucPersonaNatural);

        $validarRucPersonaNatural = $this->validador->validarRucPersonaNatural($ruc);
        $this->assertTrue($validarRucPersonaNatural);
    }

    // ==================== NEW TEST CASES ====================

    public function test_ruc_persona_natural_con_multiples_establecimientos(): void
    {
        // Valid RUC with establishment code 002
        $ruc = '0602910945002';
        $validarRucPersonaNatural = $this->validador->validarRucPersonaNatural($ruc);
        $this->assertTrue($validarRucPersonaNatural);

        // Valid RUC with establishment code 999
        $ruc = '0602910945999';
        $validarRucPersonaNatural = $this->validador->validarRucPersonaNatural($ruc);
        $this->assertTrue($validarRucPersonaNatural);
    }

    public function test_ruc_persona_natural_con_provincia_30_extranjero(): void
    {
        // Province code 30 is for foreign residents
        // This is a synthetic RUC - checking that province 30 is accepted
        $ruc = '3012345678001';
        $validarRucPersonaNatural = $this->validador->validarRucPersonaNatural($ruc);
        // Province validation should pass (may fail on check digit)
        $this->assertNotEquals(
            'Codigo de Provincia (dos primeros dígitos) no deben ser mayor a 24 ni menores a 0',
            $this->validador->getError()
        );
    }

    public function test_ruc_persona_natural_tercer_digito_limite_superior(): void
    {
        // Third digit 5 should be valid for natural person RUC
        $ruc = '0152345678001';
        $this->validador->validarRucPersonaNatural($ruc);
        $this->assertNotEquals(
            'Tercer dígito debe ser mayor o igual a 0 y menor a 6 para cédulas y RUC de persona natural',
            $this->validador->getError()
        );

        // Third digit 6 should NOT be valid for natural person RUC (it's for public entities)
        $ruc = '0162345678001';
        $validarRucPersonaNatural = $this->validador->validarRucPersonaNatural($ruc);
        $this->assertFalse($validarRucPersonaNatural);
        $this->assertEquals(
            'Tercer dígito debe ser mayor o igual a 0 y menor a 6 para cédulas y RUC de persona natural',
            $this->validador->getError()
        );
    }

    // ==================== PR #6 - FOREIGN RESIDENTS (CODE 30) ====================

    /**
     * Test that RUC with province code 30 accepts any third digit.
     *
     * For foreign residents (code 30), the third digit validation is skipped
     * as different rules may apply.
     */
    public function test_ruc_persona_natural_codigo_30_acepta_tercer_digito_diferente(): void
    {
        // Province code 30 with third digit 6 (would fail for regular provinces)
        $ruc = '3062345678001';
        $this->validador->validarRucPersonaNatural($ruc);

        // Should NOT get third digit error for code 30
        $this->assertNotEquals(
            'Tercer dígito debe ser mayor o igual a 0 y menor a 6 para cédulas y RUC de persona natural',
            $this->validador->getError(),
            'Third digit validation should be skipped for province code 30'
        );
    }

    /**
     * Test that province code 30 is accepted for RUC.
     */
    public function test_ruc_persona_natural_codigo_provincia_30_aceptado(): void
    {
        $ruc = '3012345678001';
        $this->validador->validarRucPersonaNatural($ruc);

        // Should NOT get province code error
        $this->assertNotEquals(
            'Codigo de Provincia (dos primeros dígitos) no deben ser mayor a 24 ni menores a 0',
            $this->validador->getError(),
            'Province code 30 should be accepted for RUC'
        );
    }
}
