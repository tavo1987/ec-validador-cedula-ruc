<?php

declare(strict_types=1);

namespace Tavo\Tests;

use Tavo\ValidadorEc;

class ValidadorRucSociedadPrivadaTest extends TestCase
{
    public function test_validacion_falla_cuando_parametro_esta_vacio_o_es_nulo(): void
    {
        $validarRucSociedadPrivada = $this->validador->validarRucSociedadPrivada('');
        $this->assertFalse($validarRucSociedadPrivada);
        $this->assertEquals('Valor no puede estar vacio', $this->validador->getError());

        $validarRucSociedadPrivada = $this->validador->validarRucSociedadPrivada();
        $this->assertFalse($validarRucSociedadPrivada);
        $this->assertEquals('Valor no puede estar vacio', $this->validador->getError());
    }

    public function test_validacion_falla_si_parametro_pasado_es_un_tipo_de_dato_entero(): void
    {
        $ruc = (int) '0992397535001';

        $validarRucSociedadPrivada = $this->validador->validarRucSociedadPrivada((string) $ruc);

        $this->assertFalse($validarRucSociedadPrivada);
        $this->assertEquals('Valor ingresado debe tener 13 caracteres', $this->validador->getError());
    }

    public function test_validacion_falla_si_se_ingresa_letras(): void
    {
        $ruc = 'abcd';

        $validarRucSociedadPrivada = $this->validador->validarRucSociedadPrivada($ruc);

        $this->assertFalse($validarRucSociedadPrivada);
        $this->assertEquals('Valor ingresado solo puede tener dígitos', $this->validador->getError());
    }

    public function test_validacion_falla_si_se_ingresa_caracteres_especiales(): void
    {
        $ruc = '*@-.#';

        $resultado = $this->validador->validarRucSociedadPrivada($ruc);

        $this->assertFalse($resultado);
        $this->assertEquals('Valor ingresado solo puede tener dígitos', $this->validador->getError());
    }

    public function test_validacion_falla_si_se_ingresa_numeros_negativos(): void
    {
        $ruc = '-0992397535001';

        $validarRucSociedadPrivada = $this->validador->validarRucSociedadPrivada($ruc);

        $this->assertFalse($validarRucSociedadPrivada);
        $this->assertEquals('Valor ingresado solo puede tener dígitos', $this->validador->getError());
    }

    public function test_validacion_falla_si_se_ingresa_numeros_decimales(): void
    {
        $ruc = '099,2397535001';

        $validarRucSociedadPrivada = $this->validador->validarRucSociedadPrivada($ruc);

        $this->assertFalse($validarRucSociedadPrivada);
        $this->assertEquals('Valor ingresado solo puede tener dígitos', $this->validador->getError());
    }

    public function test_validacion_falla_si_se_ingresa_mas_de_trece_digitos(): void
    {
        $ruc = '0992397535001998';

        $validarRucSociedadPrivada = $this->validador->validarRucSociedadPrivada($ruc);

        $this->assertFalse($validarRucSociedadPrivada);
        $this->assertEquals('Valor ingresado debe tener 13 caracteres', $this->validador->getError());
    }

    public function test_validar_que_el_codigo_provincial_sea_correcto(): void
    {
        $ruc = '9992397535001';

        $validarRucSociedadPrivada = $this->validador->validarRucSociedadPrivada($ruc);

        $this->assertFalse($validarRucSociedadPrivada);
        $this->assertEquals('Codigo de Provincia (dos primeros dígitos) no deben ser mayor a 24 ni menores a 0', $this->validador->getError());
    }

    public function test_tercer_digito_deber_ser_igual_a_nueve(): void
    {
        $ruc = '0982397535001';

        $validarRucSociedadPrivada = $this->validador->validarRucSociedadPrivada($ruc);

        $this->assertFalse($validarRucSociedadPrivada);
        $this->assertEquals('Tercer dígito debe ser igual a 9 para sociedades privadas', $this->validador->getError());
    }

    public function test_codigo_de_establecimiento_no_deben_ser_menores_a_uno(): void
    {
        $ruc = '0992397535000';

        $validarRucSociedadPrivada = $this->validador->validarRucSociedadPrivada($ruc);

        $this->assertFalse($validarRucSociedadPrivada);
        $this->assertEquals('Código de establecimiento no puede ser 0', $this->validador->getError());
    }

    public function test_ruc_sociedad_privada_incorrecto(): void
    {
        $ruc = '0992397532001';

        $validarRucSociedadPrivada = $this->validador->validarRucSociedadPrivada($ruc);

        $this->assertFalse($validarRucSociedadPrivada);
        $this->assertEquals('Dígitos iniciales no validan contra Dígito Idenficador', $this->validador->getError());
    }

    public function test_ruc_sociedad_privada_correcto(): void
    {
        $ruc = '0992397535001';

        $validarRucSociedadPrivada = $this->validador->validarRucSociedadPrivada($ruc);
        $this->assertTrue($validarRucSociedadPrivada);
    }

    // ==================== NEW TEST CASES ====================

    public function test_ruc_sociedad_privada_con_multiples_establecimientos(): void
    {
        // Valid RUC with establishment code 002
        $ruc = '0992397535002';
        $resultado = $this->validador->validarRucSociedadPrivada($ruc);
        $this->assertTrue($resultado);

        // Valid RUC with establishment code 999
        $ruc = '0992397535999';
        $resultado = $this->validador->validarRucSociedadPrivada($ruc);
        $this->assertTrue($resultado);
    }

    public function test_ruc_sociedad_privada_tercer_digito_no_es_seis(): void
    {
        // Third digit 6 is for public companies, not private
        $ruc = '0962397535001';
        $resultado = $this->validador->validarRucSociedadPrivada($ruc);
        $this->assertFalse($resultado);
        $this->assertEquals('Tercer dígito debe ser igual a 9 para sociedades privadas', $this->validador->getError());
    }

    public function test_ruc_sociedad_privada_tercer_digito_no_es_natural(): void
    {
        // Third digit 0-5 is for natural persons, not private companies
        $ruc = '0902397535001';
        $resultado = $this->validador->validarRucSociedadPrivada($ruc);
        $this->assertFalse($resultado);
        $this->assertEquals('Tercer dígito debe ser igual a 9 para sociedades privadas', $this->validador->getError());
    }
}
