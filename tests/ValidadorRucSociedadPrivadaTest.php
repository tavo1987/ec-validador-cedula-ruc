<?php

namespace Tavo\Tests;

class ValidadorRucSociedadPrivadaTest extends TestCase
{
    public function test_validacion_falla_cuando_parametro_esta_vacio_o_es_nulo()
    {
        $validarRucSociedadPrivada = $this->validador->validarRucSociedadPrivada('');
        $this->assertFalse($validarRucSociedadPrivada);
        $this->assertEquals($this->validador->getError(), 'Valor no puede estar vacio');

        $validarRucSociedadPrivada = $this->validador->validarRucSociedadPrivada();
        $this->assertFalse($validarRucSociedadPrivada);
        $this->assertEquals($this->validador->getError(), 'Valor no puede estar vacio');
    }

    public function test_validacion_falla_si_parametro_pasado_es_un_tipo_de_dato_entero()
    {
        $ruc = (int) '0992397535001';

        $validarRucSociedadPrivada = $this->validador->validarRucSociedadPrivada($ruc);

        $this->assertFalse($validarRucSociedadPrivada);
        $this->assertEquals($this->validador->getError(), 'Valor ingresado debe tener 13 caracteres');
    }

    public function test_validacion_falla_si_se_ingresa_letras()
    {
        $ruc = 'abcd';

        $validarRucSociedadPrivada = $this->validador->validarRucSociedadPrivada($ruc);

        $this->assertFalse($validarRucSociedadPrivada);
        $this->assertEquals($this->validador->getError(), 'Valor ingresado solo puede tener dígitos');
    }

    public function test_validacion_falla_si_se_ingresa_caracteres_especiales()
    {
        $ruc = '*@-.#';

        $resultado = $this->validador->validarRucSociedadPrivada($ruc);

        $this->assertFalse($resultado);
        $this->assertEquals($this->validador->getError(), 'Valor ingresado solo puede tener dígitos');
    }

    public function test_validacion_falla_si_se_ingresa_numeros_negativos()
    {
        $ruc = '-0992397535001';

        $validarRucSociedadPrivada = $this->validador->validarRucSociedadPrivada($ruc);

        $this->assertFalse($validarRucSociedadPrivada);
        $this->assertEquals($this->validador->getError(), 'Valor ingresado solo puede tener dígitos');
    }

    public function test_validacion_falla_si_se_ingresa_numeros_decimales()
    {
        $ruc = '099,2397535001';

        $validarRucSociedadPrivada = $this->validador->validarRucSociedadPrivada($ruc);

        $this->assertFalse($validarRucSociedadPrivada);
        $this->assertEquals($this->validador->getError(), 'Valor ingresado solo puede tener dígitos');
    }

    public function test_validacion_falla_si_se_ingresa_mas_de_trece_digitos()
    {
        $ruc = '0992397535001998';

        $validarRucSociedadPrivada = $this->validador->validarRucSociedadPrivada($ruc);

        $this->assertFalse($validarRucSociedadPrivada);
        $this->assertEquals($this->validador->getError(), 'Valor ingresado debe tener 13 caracteres');
    }

    public function test_validar_que_el_codigo_provincial_sea_correcto()
    {
        $ruc = '9992397535001';

        $validarRucSociedadPrivada = $this->validador->validarRucSociedadPrivada($ruc);

        $this->assertFalse($validarRucSociedadPrivada);
        $this->assertEquals($this->validador->getError(), 'Codigo de Provincia (dos primeros dígitos) no deben ser mayor a 24 ni menores a 0');
    }

    public function test_tercer_digito_deber_ser_igual_a_nueve()
    {
        $ruc = '0982397535001';

        $validarRucSociedadPrivada = $this->validador->validarRucSociedadPrivada($ruc);

        $this->assertFalse($validarRucSociedadPrivada);
        $this->assertEquals($this->validador->getError(), 'Tercer dígito debe ser igual a 9 para sociedades privadas');
    }

    public function test_codigo_de_establecimiento_no_deben_ser_menores_a_uno()
    {
        $ruc = '0992397535000';

        $validarRucSociedadPrivada = $this->validador->validarRucSociedadPrivada($ruc);

        $this->assertFalse($validarRucSociedadPrivada);
        $this->assertEquals($this->validador->getError(), 'Código de establecimiento no puede ser 0');
    }

    public function test_ruc_sociedad_privada_incorrecto()
    {
        $ruc = '0992397532001';

        $validarRucSociedadPrivada = $this->validador->validarRucSociedadPrivada($ruc);

        $this->assertFalse($validarRucSociedadPrivada);
        $this->assertEquals($this->validador->getError(), 'Dígitos iniciales no validan contra Dígito Idenficador');
    }

    public function test_ruc_sociedad_privada_correcto()
    {
        $ruc = '0992397535001';

        $validarRucSociedadPrivada = $this->validador->validarRucSociedadPrivada($ruc);
        $this->assertTrue($validarRucSociedadPrivada);
    }
}
