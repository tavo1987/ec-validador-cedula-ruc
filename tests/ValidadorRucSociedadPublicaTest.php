<?php

declare(strict_types=1);

namespace Tavo\Tests;

use Tavo\ValidadorEc;

class ValidadorRucSociedadPublicaTest extends TestCase
{
    public function test_mostrar_error_cuando_campo_ruc_este_vacio_o_sea_nulo_en_sociedad_publica(): void
    {
        // parametro vacio o sin parametro (numero ci) deben dar false
        $validarRucSociedadPublica = $this->validador->validarRucSociedadPublica('');
        $this->assertFalse($validarRucSociedadPublica);
        $this->assertEquals('Valor no puede estar vacio', $this->validador->getError());

        $validarRucSociedadPublica = $this->validador->validarRucSociedadPublica();
        $this->assertFalse($validarRucSociedadPublica);
        $this->assertEquals('Valor no puede estar vacio', $this->validador->getError());
    }

    public function test_mostrar_error_si_parametro_pasado_es_un_tipo_de_dato_entero_en_sociedad_publica(): void
    {
        // parametro con 0 adelante pero como integer, debe dar false ya que php lo convierte a 0
        $ruc = '0960001550001';
        $validarRucSociedadPublica = $this->validador->validarRucSociedadPublica($ruc);
        $this->assertFalse($validarRucSociedadPublica);
        $this->assertEquals('Dígitos iniciales no validan contra Dígito Idenficador', $this->validador->getError());
    }

    public function test_parametro_debe_tener_solo_digitos_en_sociedad_publica(): void
    {
        // parametro debe tener solo digitos
        $ruc = 'asdaddadad';

        $validarRucSociedadPublica = $this->validador->validarRucSociedadPublica($ruc);
        $this->assertFalse($validarRucSociedadPublica);
        $this->assertEquals('Valor ingresado solo puede tener dígitos', $this->validador->getError());

        $validarRucSociedadPublica = $this->validador->validarRucSociedadPublica($ruc);
        $this->assertFalse($validarRucSociedadPublica);
        $this->assertEquals('Valor ingresado solo puede tener dígitos', $this->validador->getError());
    }

    public function test_ruc_de_sociedad_publica_debe_tener_trece_caracteres_exactos(): void
    {
        // ruc de sociedad pública debe tener 13 caracteres exactos
        $ruc = '1760001550001990999';
        $validarRucSociedadPublica = $this->validador->validarRucSociedadPublica($ruc);
        $this->assertFalse($validarRucSociedadPublica);
        $this->assertEquals('Valor ingresado debe tener 13 caracteres', $this->validador->getError());
    }

    public function test_revisar_codigo_de_provincia_debe_estar_entre_cero_y_veinticuatro_ruc_sociedad_publica(): void
    {
        // revisar codigo de provincia, debe estar entre 0 y 24
        $ruc = '2760001550001';
        $validarRucSociedadPublica = $this->validador->validarRucSociedadPublica($ruc);
        $this->assertFalse($validarRucSociedadPublica);
        $this->assertEquals('Codigo de Provincia (dos primeros dígitos) no deben ser mayor a 24 ni menores a 0', $this->validador->getError());
    }

    public function test_revisar_tercer_digito_debe_ser_mayor_igual_a_cero_y_menor_a_seis_sociedad_publica(): void
    {
        // revisar tercer digito, debe ser igual a 6
        $ruc = '1790001550001';
        $validarRucSociedadPublica = $this->validador->validarRucSociedadPublica($ruc);
        $this->assertFalse($validarRucSociedadPublica);
        $this->assertEquals('Tercer dígito debe ser igual a 6 para sociedades públicas', $this->validador->getError());
    }

    public function test_revisar_que_codigo_de_establecimiento_en_los_cuatro_ultimos_digitos_no_sean_menores_a_uno_persona_natural(): void
    {
        // revisar que codigo de establecimiento (4 últimos dígitos) no sean menores a 1.
        $ruc = '1760001550000';
        $validarRucSociedadPublica = $this->validador->validarRucSociedadPublica($ruc);
        $this->assertFalse($validarRucSociedadPublica);
        $this->assertEquals('Código de establecimiento no puede ser 0', $this->validador->getError());
    }

    public function test_ruc_persona_natural_incorrecta_de_acuerdo_al_algoritmo_modulo_once(): void
    {
        // ruc sociedad privada incorrecto de acuerdo a algoritmo modulo11
        $ruc = '1760001520001';
        $validarRucSociedadPublica = $this->validador->validarRucSociedadPublica($ruc);
        $this->assertFalse($validarRucSociedadPublica);
        $this->assertEquals('Dígitos iniciales no validan contra Dígito Idenficador', $this->validador->getError());
    }

    public function test_revisar_que_ruc_correctos_validen_sociedad_publica(): void
    {
        // revisar que ruc correcto valide
        $ruc = '1760001550001';
        $validarRucSociedadPublica = $this->validador->validarRucSociedadPublica($ruc);
        $this->assertTrue($validarRucSociedadPublica);
    }

    // ==================== NEW TEST CASES ====================

    /**
     * Issue #3 - RUC that was reported as not passing validation
     *
     * RUC: 0962893970001
     * Analysis:
     * - First 2 digits: 09 (Guayas province - valid)
     * - Third digit: 6 (Public company indicator)
     * - Check digit (9th position): 7
     *
     * After mathematical verification using Modulo 11:
     * - Coefficients: [3, 2, 7, 6, 5, 4, 3, 2]
     * - Sum: 0*3 + 9*2 + 6*7 + 2*6 + 8*5 + 9*4 + 3*3 + 9*2 = 175
     * - Residuo: 175 % 11 = 10
     * - Expected check digit: 11 - 10 = 1
     * - Actual check digit: 7
     *
     * CONCLUSION: The RUC reported in Issue #3 is mathematically INVALID.
     * The check digit should be 1, not 7. The user likely had a typo or
     * was using an incorrect RUC number. The correct behavior is to reject it.
     */
    public function test_issue_3_ruc_0962893970001_analysis(): void
    {
        // This RUC from Issue #3 has third digit 6, which means it's detected as public company
        $ruc = '0962893970001';

        // The RUC fails validation because the check digit is incorrect (7 instead of 1)
        $resultado = $this->validador->validarRucSociedadPublica($ruc);
        $this->assertFalse($resultado, 'RUC 0962893970001 is mathematically invalid (wrong check digit)');
        $this->assertEquals('Dígitos iniciales no validan contra Dígito Idenficador', $this->validador->getError());

        // Verify it also fails as natural person RUC (because third digit is 6, not 0-5)
        $resultadoNatural = $this->validador->validarRucPersonaNatural($ruc);
        $this->assertFalse($resultadoNatural, 'RUC 0962893970001 should NOT pass as natural person RUC');
    }

    /**
     * Test with a VALID public company RUC from Guayas (09)
     * This demonstrates correct validation for public RUCs
     */
    public function test_ruc_sociedad_publica_guayas_valido(): void
    {
        // Known valid public RUC: 1760001550001 (SRI)
        $ruc = '1760001550001';
        $resultado = $this->validador->validarRucSociedadPublica($ruc);
        $this->assertTrue($resultado);
    }

    public function test_ruc_sociedad_publica_tercer_digito_no_es_nueve(): void
    {
        // Third digit 9 is for private companies, not public
        $ruc = '0992893970001';
        $resultado = $this->validador->validarRucSociedadPublica($ruc);
        $this->assertFalse($resultado);
        $this->assertEquals('Tercer dígito debe ser igual a 6 para sociedades públicas', $this->validador->getError());
    }

    public function test_ruc_sociedad_publica_tercer_digito_no_es_natural(): void
    {
        // Third digit 0-5 is for natural persons, not public companies
        $ruc = '0902893970001';
        $resultado = $this->validador->validarRucSociedadPublica($ruc);
        $this->assertFalse($resultado);
        $this->assertEquals('Tercer dígito debe ser igual a 6 para sociedades públicas', $this->validador->getError());
    }

    public function test_ruc_sociedad_publica_con_multiples_establecimientos(): void
    {
        // Valid RUC with different establishment codes
        // Using the known valid RUC: 1760001550001

        // Establishment 002
        $ruc = '1760001550002';
        $resultado = $this->validador->validarRucSociedadPublica($ruc);
        $this->assertTrue($resultado);

        // Establishment 9999
        $ruc = '1760001559999';
        $resultado = $this->validador->validarRucSociedadPublica($ruc);
        $this->assertTrue($resultado);
    }
}
