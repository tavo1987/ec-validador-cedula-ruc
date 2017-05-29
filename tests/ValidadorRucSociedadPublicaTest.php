<?php

namespace Tests;


class ValidadorRucSociedadPublicaTest extends TestCase
{

    public function test_ruc_sociedad_publica()
    {
        // parametro vacio o sin parametro (numero ci) deben dar false
        $validarRucSociedadPublica = $this->validador->validarRucSociedadPublica('');
        $this->assertEquals($validarRucSociedadPublica, false);
        $this->assertEquals($this->validador->getError(), 'Valor no puede estar vacio');

        $validarRucSociedadPublica = $this->validador->validarRucSociedadPublica();
        $this->assertEquals($validarRucSociedadPublica, false);
        $this->assertEquals($this->validador->getError(), 'Valor no puede estar vacio');

        // parametro con 0 adelante pero como integer, debe dar false ya que php lo convierte a 0
        $validarRucSociedadPublica = $this->validador->validarRucSociedadPublica('0960001550001');
        $this->assertEquals($validarRucSociedadPublica, false);
        $this->assertEquals($this->validador->getError(), 'Valor no puede estar vacio');

        // parametro debe tener solo digitos
        $validarRucSociedadPublica = $this->validador->validarRucSociedadPublica('-1760001550001');
        $this->assertEquals($validarRucSociedadPublica, false);
        $this->assertEquals($this->validador->getError(), 'Valor ingresado solo puede tener dígitos');

        $validarRucSociedadPublica = $this->validador->validarRucSociedadPublica('17600,01550001');
        $this->assertEquals($validarRucSociedadPublica, false);
        $this->assertEquals($this->validador->getError(), 'Valor ingresado solo puede tener dígitos');

        // ruc de sociedad pública debe tener 13 caracteres exactos
        $validarRucSociedadPublica = $this->validador->validarRucSociedadPublica('1760001550001990999');
        $this->assertEquals($validarRucSociedadPublica, false);
        $this->assertEquals($this->validador->getError(), 'Valor ingresado debe tener 13 caracteres');

        // revisar codigo de provincia, debe estar entre 0 y 24
        $validarRucSociedadPublica = $this->validador->validarRucSociedadPublica('2760001550001');
        $this->assertEquals($validarRucSociedadPublica, false);
        $this->assertEquals($this->validador->getError(), 'Codigo de Provincia (dos primeros dígitos) no deben ser mayor a 24 ni menores a 0');

        // revisar tercer digito, debe ser igual a 6
        $validarRucSociedadPublica = $this->validador->validarRucSociedadPublica('1790001550001');
        $this->assertEquals($validarRucSociedadPublica, false);
        $this->assertEquals($this->validador->getError(), 'Tercer dígito debe ser igual a 6 para sociedades públicas');

        // revisar que codigo de establecimiento (4 últimos dígitos) no sean menores a 1.
        $validarRucSociedadPublica = $this->validador->validarRucSociedadPublica('1760001550000');
        $this->assertEquals($validarRucSociedadPublica, false);
        $this->assertEquals($this->validador->getError(), 'Código de establecimiento no puede ser 0');

        // ruc sociedad privada incorrecto de acuerdo a algoritmo modulo11
        $validarRucSociedadPublica = $this->validador->validarRucSociedadPublica('1760001520001');
        $this->assertEquals($validarRucSociedadPublica, false);
        $this->assertEquals($this->validador->getError(), 'Dígitos iniciales no validan contra Dígito Idenficador');

        // revisar que ruc correcto valide
        $validarRucSociedadPublica = $this->validador->validarRucSociedadPublica('1760001550001');
        $this->assertEquals($validarRucSociedadPublica, true);

    }
}
