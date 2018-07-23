<?php

namespace Tavo\Tests;

class ValidadorRucSociedadPublicaTest extends TestCase
{
    public function test_mostrar_error_cuando_campo_ruc_este_vacio_o_sea_nulo_en_sociedad_publica()
    {
        // parametro vacio o sin parametro (numero ci) deben dar false
        $validarRucSociedadPublica = $this->validador->validarRucSociedadPublica('');
        $this->assertFalse($validarRucSociedadPublica);
        $this->assertEquals($this->validador->getError(), 'Valor no puede estar vacio');

        $validarRucSociedadPublica = $this->validador->validarRucSociedadPublica();
        $this->assertFalse($validarRucSociedadPublica);
        $this->assertEquals($this->validador->getError(), 'Valor no puede estar vacio');
    }

    public function test_mostrar_error_si_parametro_pasado_es_un_tipo_de_dato_entero_en_sociedad_publica()
    {
        // parametro con 0 adelante pero como integer, debe dar false ya que php lo convierte a 0
        $ruc = '0960001550001';
        $validarRucSociedadPublica = $this->validador->validarRucSociedadPublica($ruc);
        $this->assertFalse($validarRucSociedadPublica);
        $this->assertEquals($this->validador->getError(), 'Dígitos iniciales no validan contra Dígito Idenficador');
    }

    public function test_parametro_debe_tener_solo_digitos_en_sociedad_publica()
    {
        // parametro debe tener solo digitos
        $ruc = 'asdaddadad';

        $validarRucSociedadPublica = $this->validador->validarRucSociedadPublica($ruc);
        $this->assertFalse($validarRucSociedadPublica);
        $this->assertEquals($this->validador->getError(), 'Valor ingresado solo puede tener dígitos');

        $validarRucSociedadPublica = $this->validador->validarRucSociedadPublica($ruc);
        $this->assertFalse($validarRucSociedadPublica);
        $this->assertEquals($this->validador->getError(), 'Valor ingresado solo puede tener dígitos');
    }

    public function test_ruc_de_sociedad_publica_debe_tener_trece_caracteres_exactos()
    {
        // ruc de sociedad pública debe tener 13 caracteres exactos
        $ruc = '1760001550001990999';
        $validarRucSociedadPublica = $this->validador->validarRucSociedadPublica($ruc);
        $this->assertFalse($validarRucSociedadPublica);
        $this->assertEquals($this->validador->getError(), 'Valor ingresado debe tener 13 caracteres');
    }

    public function test_revisar_codigo_de_provincia_debe_estar_entre_cero_y_veinticuatro_ruc_sociedad_publica()
    {
        // revisar codigo de provincia, debe estar entre 0 y 24
        $ruc = '2760001550001';
        $validarRucSociedadPublica = $this->validador->validarRucSociedadPublica($ruc);
        $this->assertFalse($validarRucSociedadPublica);
        $this->assertEquals($this->validador->getError(), 'Codigo de Provincia (dos primeros dígitos) no deben ser mayor a 24 ni menores a 0');
    }

    public function test_revisar_tercer_digito_debe_ser_mayor_igual_a_cero_y_menor_a_seis_sociedad_publica()
    {
        // revisar tercer digito, debe ser igual a 6
        $ruc = '1790001550001';
        $validarRucSociedadPublica = $this->validador->validarRucSociedadPublica($ruc);
        $this->assertFalse($validarRucSociedadPublica);
        $this->assertEquals($this->validador->getError(), 'Tercer dígito debe ser igual a 6 para sociedades públicas');
    }

    public function test_revisar_que_codigo_de_establecimiento_en_los_cuatro_ultimos_digitos_no_sean_menores_a_uno_persona_natural()
    {
        // revisar que codigo de establecimiento (4 últimos dígitos) no sean menores a 1.
        $ruc = '1760001550000';
        $validarRucSociedadPublica = $this->validador->validarRucSociedadPublica($ruc);
        $this->assertFalse($validarRucSociedadPublica);
        $this->assertEquals($this->validador->getError(), 'Código de establecimiento no puede ser 0');
    }

    public function test_ruc_persona_natural_incorrecta_de_acuerdo_al_algoritmo_modulo_once()
    {
        // ruc sociedad privada incorrecto de acuerdo a algoritmo modulo11
        $ruc = '1760001520001';
        $validarRucSociedadPublica = $this->validador->validarRucSociedadPublica($ruc);
        $this->assertFalse($validarRucSociedadPublica);
        $this->assertEquals($this->validador->getError(), 'Dígitos iniciales no validan contra Dígito Idenficador');
    }

    public function test_revisar_que_ruc_correctos_validen_sociedad_publica()
    {
        // revisar que ruc correcto valide
        $ruc = '1760001550001';
        $validarRucSociedadPublica = $this->validador->validarRucSociedadPublica($ruc);
        $this->assertTrue($validarRucSociedadPublica);
    }
}
