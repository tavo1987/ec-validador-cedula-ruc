<?php

namespace Tests;

class ValidadorRucPersonaNaturalTest extends TestCase
{
    /**
     * Tests sobre método público validarRucPersonaNatural()
     */
    public function testRucPersonaNatural()
    {
        // parametro vacio o sin parametro (numero ci) deben dar false
        $validarRucPersonaNatural = $this->validador->validarRucPersonaNatural('');
        $this->assertEquals($validarRucPersonaNatural, false);
        $this->assertEquals($this->validador->getError(), 'Valor no puede estar vacio');

        $validarRucPersonaNatural = $this->validador->validarRucPersonaNatural();
        $this->assertEquals($validarRucPersonaNatural, false);
        $this->assertEquals($this->validador->getError(), 'Valor no puede estar vacio');

        // parametro con 0 adelante pero como integer, debe dar false ya que php lo convierte a 0
        $validarRucPersonaNatural = $this->validador->validarRucPersonaNatural('0926687856001');
        $this->assertEquals($validarRucPersonaNatural, false);
        $this->assertEquals($this->validador->getError(), 'Valor no puede estar vacio');

        // parametro debe tener solo digitos
        $validarRucPersonaNatural = $this->validador->validarRucPersonaNatural('-0926687856001');
        $this->assertEquals($validarRucPersonaNatural, false);
        $this->assertEquals($this->validador->getError(), 'Valor ingresado solo puede tener dígitos');

        $validarRucPersonaNatural = $this->validador->validarRucPersonaNatural('09.26687856001');
        $this->assertEquals($validarRucPersonaNatural, false);
        $this->assertEquals($this->validador->getError(), 'Valor ingresado solo puede tener dígitos');

        // ruc de persona natural debe tener 13 caracteres exactos
        $validarRucPersonaNatural = $this->validador->validarRucPersonaNatural('0926687864777009');
        $this->assertEquals($validarRucPersonaNatural, false);
        $this->assertEquals($this->validador->getError(), 'Valor ingresado debe tener 13 caracteres');

        // revisar codigo de provincia, debe estar entre 0 y 24
        $validarRucPersonaNatural = $this->validador->validarRucPersonaNatural('9926687856001');
        $this->assertEquals($validarRucPersonaNatural, false);
        $this->assertEquals($this->validador->getError(), 'Codigo de Provincia (dos primeros dígitos) no deben ser mayor a 24 ni menores a 0');

        // revisar tercer digito, debe ser mayor/igual a 0 y menor a 6
        $validarRucPersonaNatural = $this->validador->validarRucPersonaNatural('0996687856001');
        $this->assertEquals($validarRucPersonaNatural, false);
        $this->assertEquals($this->validador->getError(), 'Tercer dígito debe ser mayor o igual a 0 y menor a 6 para cédulas y RUC de persona natural');

        // revisar que codigo de establecimiento (3 últimos dígitos) no sean menores a 1.
        $validarRucPersonaNatural = $this->validador->validarRucPersonaNatural('0926687856000');
        $this->assertEquals($validarRucPersonaNatural, false);
        $this->assertEquals($this->validador->getError(), 'Código de establecimiento no puede ser 0');

        // ruc persona natural incorrecto de acuerdo a algoritmo modulo10
        $validarRucPersonaNatural = $this->validador->validarRucPersonaNatural('0926687858001');
        $this->assertEquals($validarRucPersonaNatural, false);
        $this->assertEquals($this->validador->getError(), 'Dígitos iniciales no validan contra Dígito Idenficador');

        // revisar que cedulas correctas validen
        $validarRucPersonaNatural = $this->validador->validarRucPersonaNatural('0602910945001');
        $this->assertEquals($validarRucPersonaNatural, true);

        $validarRucPersonaNatural = $this->validador->validarRucPersonaNatural('0926687856001');
        $this->assertEquals($validarRucPersonaNatural, true);

        $validarRucPersonaNatural = $this->validador->validarRucPersonaNatural('0910005917001');
        $this->assertEquals($validarRucPersonaNatural, true);
    }
}

