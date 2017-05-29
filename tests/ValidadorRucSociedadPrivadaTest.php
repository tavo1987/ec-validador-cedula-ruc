<?php

namespace Tests;


class ValidadorRucSociedadPrivadaTest extends TestCase
{
   /**
     * Tests sobre método público validarRucSociedadPrivada()
     */
    public function testRucSociedadPrivada()
    {
        // parametro vacio o sin parametro (numero ci) deben dar false
        $validarRucSociedadPrivada = $this->validador->validarRucSociedadPrivada('');
        $this->assertEquals($validarRucSociedadPrivada, false);
        $this->assertEquals($this->validador->getError(), 'Valor no puede estar vacio');

        $validarRucSociedadPrivada = $this->validador->validarRucSociedadPrivada();
        $this->assertEquals($validarRucSociedadPrivada, false);
        $this->assertEquals($this->validador->getError(), 'Valor no puede estar vacio');

        // parametro con 0 adelante pero como integer, debe dar false ya que php lo convierte a 0
        $validarRucSociedadPrivada = $this->validador->validarRucSociedadPrivada('0992397535001');
        $this->assertEquals($validarRucSociedadPrivada, false);
        $this->assertEquals($this->validador->getError(), 'Valor no puede estar vacio');

        // parametro debe tener solo digitos
        $validarRucSociedadPrivada = $this->validador->validarRucSociedadPrivada('-0992397535001');
        $this->assertEquals($validarRucSociedadPrivada, false);
        $this->assertEquals($this->validador->getError(), 'Valor ingresado solo puede tener dígitos');

        $validarRucSociedadPrivada = $this->validador->validarRucSociedadPrivada('099,2397535001');
        $this->assertEquals($validarRucSociedadPrivada, false);
        $this->assertEquals($this->validador->getError(), 'Valor ingresado solo puede tener dígitos');

        // ruc de sociedad privada debe tener 13 caracteres exactos
        $validarRucSociedadPrivada = $this->validador->validarRucSociedadPrivada('0992397535001998');
        $this->assertEquals($validarRucSociedadPrivada, false);
        $this->assertEquals($this->validador->getError(), 'Valor ingresado debe tener 13 caracteres');

        // revisar codigo de provincia, debe estar entre 0 y 24
        $validarRucSociedadPrivada = $this->validador->validarRucSociedadPrivada('9992397535001');
        $this->assertEquals($validarRucSociedadPrivada, false);
        $this->assertEquals($this->validador->getError(), 'Codigo de Provincia (dos primeros dígitos) no deben ser mayor a 24 ni menores a 0');

        // revisar tercer digito, debe ser igual a 9
        $validarRucSociedadPrivada = $this->validador->validarRucSociedadPrivada('0982397535001');
        $this->assertEquals($validarRucSociedadPrivada, false);
        $this->assertEquals($this->validador->getError(), 'Tercer dígito debe ser igual a 9 para sociedades privadas');

        // revisar que codigo de establecimiento (3 últimos dígitos) no sean menores a 1.
        $validarRucSociedadPrivada = $this->validador->validarRucSociedadPrivada('0992397535000');
        $this->assertEquals($validarRucSociedadPrivada, false);
        $this->assertEquals($this->validador->getError(), 'Código de establecimiento no puede ser 0');

        // ruc sociedad privada incorrecto de acuerdo a algoritmo modulo11
        $validarRucSociedadPrivada = $this->validador->validarRucSociedadPrivada('0992397532001');
        $this->assertEquals($validarRucSociedadPrivada, false);
        $this->assertEquals($this->validador->getError(), 'Dígitos iniciales no validan contra Dígito Idenficador');

        // revisar que ruc correcto valide
        $validarRucSociedadPrivada = $this->validador->validarRucSociedadPrivada('0992397535001');
        $this->assertEquals($validarRucSociedadPrivada, true);

    }
}
