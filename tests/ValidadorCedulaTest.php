<?php

namespace Tavo\Tests;

class ValidadorCedulaTest extends TestCase
{
    public function test_mostrar_error_cuando_campo_cedula_este_vacio_o_sea_nulo()
    {
        $validarCedula = $this->validador->validarCedula('');
        $this->assertFalse($validarCedula);
        $this->assertEquals($this->validador->getError(), 'Valor no puede estar vacio');

        $validarCedula = $this->validador->validarCedula();
        $this->assertFalse($validarCedula);
        $this->assertEquals($this->validador->getError(), 'Valor no puede estar vacio');
    }

    public function test_mostrar_error_si_parametro_pasado_es_un_tipo_de_dato_entero()
    {
        $cedula = (int) '0926687856';
        // parametro con 0 adelante pero como integer, debe dar false ya que php lo convierte a 0
        $validarCedula = $this->validador->validarCedula($cedula);
        $this->assertFalse($validarCedula);
        $this->assertEquals($this->validador->getError(), 'Valor ingresado debe tener 10 caracteres');
    }

    public function test_validacion_falla_si_se_ingresa_letras()
    {
        $cedula = 'abcd';

        $resultado = $this->validador->validarCedula($cedula);

        $this->assertFalse($resultado);
        $this->assertEquals($this->validador->getError(), 'Valor ingresado solo puede tener dígitos');
    }

    public function test_validacion_falla_si_se_ingresa_caracteres_especiales()
    {
        $cedula = '*@-.#';

        $resultado = $this->validador->validarCedula($cedula);

        $this->assertFalse($resultado);
        $this->assertEquals($this->validador->getError(), 'Valor ingresado solo puede tener dígitos');
    }

    public function test_validacion_falla_si_se_ingresa_numeros_negativos()
    {
        $cedula = '-1723468565';

        $resultado = $this->validador->validarCedula($cedula);
        $this->assertFalse($resultado);
        $this->assertEquals($this->validador->getError(), 'Valor ingresado solo puede tener dígitos');
    }

    public function test_validacion_falla_si_se_ingresa_numeros_decimales()
    {
        $cedula = '09.26687856';

        $resultado = $this->validador->validarCedula($cedula);
        $this->assertFalse($resultado);
        $this->assertEquals($this->validador->getError(), 'Valor ingresado solo puede tener dígitos');
    }

    public function test_validacion_falla_si_se_ingresa_mas_de_diez_digitos()
    {
        $cedula = '0926687864777009';

        $resultado = $this->validador->validarCedula($cedula);

        $this->assertFalse($resultado);
        $this->assertEquals($this->validador->getError(), 'Valor ingresado debe tener 10 caracteres');
    }

    public function test_validar_codigo_provincial_sea_correcto()
    {
        $cedula = '2526687856';

        $resultado = $this->validador->validarCedula($cedula);

        $this->assertFalse($resultado);
        $this->assertEquals($this->validador->getError(), 'Codigo de Provincia (dos primeros dígitos) no deben ser mayor a 24 ni menores a 0');
    }

    public function test_validacion_falla_si_el_tercer_digito_es_menor_a_cero_y_mayor_seis()
    {
        $cedula = '0996687856';

        $resultado = $this->validador->validarCedula($cedula);

        $this->assertFalse($resultado);
        $this->assertEquals($this->validador->getError(), 'Tercer dígito debe ser mayor o igual a 0 y menor a 6 para cédulas y RUC de persona natural');
    }

    public function test_cedula_incorrecta()
    {
        $cedula = '0926687858';

        $resultado = $this->validador->validarCedula($cedula);

        $this->assertFalse($resultado);

        $this->assertEquals($this->validador->getError(), 'Dígitos iniciales no validan contra Dígito Idenficador');
    }

    public function test_cedula_correcta()
    {
        $cedula = '0602910945';
        $resultado = $this->validador->validarCedula($cedula);
        $this->assertTrue($resultado);

        $validarCedula = $this->validador->validarCedula('0926687856');
        $this->assertEquals($validarCedula, true);

        $validarCedula = $this->validador->validarCedula('0910005917');
        $this->assertEquals($validarCedula, true);
    }
}
