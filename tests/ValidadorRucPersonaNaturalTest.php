<?php

namespace Tavo\Tests;

class ValidadorRucPersonaNaturalTest extends TestCase
{
    public function test_mostrar_error_cuando_parametro_ruc_persona_natural_este_vacio_o_sea_nulo()
    {
        $validarRucPersonaNatural = $this->validador->validarRucPersonaNatural('');
        $this->assertEquals($validarRucPersonaNatural, false);
        $this->assertEquals($this->validador->getError(), 'Valor no puede estar vacio');

        $validarRucPersonaNatural = $this->validador->validarRucPersonaNatural();
        $this->assertEquals($validarRucPersonaNatural, false);
        $this->assertEquals($this->validador->getError(), 'Valor no puede estar vacio');
    }

    public function test_mostrar_error_si_parametro_pasado_es_un_tipo_de_dato_entero_en_ruc_persona_natural()
    {
        $ruc = (int) '0926687856001';
        // parametro con 0 adelante pero como integer, debe dar false ya que php lo convierte a 0
        $validarRucPersonaNatural = $this->validador->validarRucPersonaNatural($ruc);
        $this->assertEquals($validarRucPersonaNatural, false);
        $this->assertEquals($this->validador->getError(), 'Valor ingresado debe tener 13 caracteres');
    }

    public function test_validacion_falla_si_se_ingresa_letras_en_ruc_persona_natural()
    {
        // parametro debe tener solo digitos
        $ruc = 'abcdsa';
        $validarRucPersonaNatural = $this->validador->validarRucPersonaNatural($ruc);
        $this->assertEquals($validarRucPersonaNatural, false);
        $this->assertEquals($this->validador->getError(), 'Valor ingresado solo puede tener dígitos');

        $validarRucPersonaNatural = $this->validador->validarRucPersonaNatural($ruc);
        $this->assertEquals($validarRucPersonaNatural, false);
        $this->assertEquals($this->validador->getError(), 'Valor ingresado solo puede tener dígitos');
    }

    public function test_ruc_de_persona_natural_debe_tener_trece_caracteres_exactos()
    {
        $ruc = '0926687864777009';

        $validarRucPersonaNatural = $this->validador->validarRucPersonaNatural($ruc);

        $this->assertEquals($validarRucPersonaNatural, false);
        $this->assertEquals($this->validador->getError(), 'Valor ingresado debe tener 13 caracteres');
    }

    public function test_revisar_codigo_de_provincia_debe_estar_entre_cero_y_veinticuatro_ruc_persona_natural()
    {
        $ruc = '2526687856001';

        $validarRucPersonaNatural = $this->validador->validarRucPersonaNatural($ruc);
        $this->assertEquals($validarRucPersonaNatural, false);
        $this->assertEquals($this->validador->getError(), 'Codigo de Provincia (dos primeros dígitos) no deben ser mayor a 24 ni menores a 0');
    }

    public function test_revisar_tercer_digito_debe_ser_mayor_igual_a_cero_y_menor_a_seis_persona_natural()
    {
        // revisar tercer digito, debe ser mayor/igual a 0 y menor a 6
        $ruc = '0186687856001';

        $validarRucPersonaNatural = $this->validador->validarRucPersonaNatural($ruc);
        $this->assertEquals($validarRucPersonaNatural, false);
        $this->assertEquals($this->validador->getError(), 'Tercer dígito debe ser mayor o igual a 0 y menor a 6 para cédulas y RUC de persona natural');
    }

    public function test_revisar_que_codigo_de_establecimiento_en_los_tres_ultimos_digitos_no_sean_menores_a_uno_persona_natural()
    {
        // revisar que codigo de establecimiento (3 últimos dígitos) no sean menores a 1.
        $ruc = '0926687856000';

        $validarRucPersonaNatural = $this->validador->validarRucPersonaNatural($ruc);
        $this->assertEquals($validarRucPersonaNatural, false);
        $this->assertEquals($this->validador->getError(), 'Código de establecimiento no puede ser 0');
    }

    public function test_ruc_persona_natural_incorrecta_de_acuerdo_al_algoritmo_modulo_diez()
    {
        // ruc persona natural incorrecto de acuerdo a algoritmo modulo10
        $ruc = '0926687858001';

        $validarRucPersonaNatural = $this->validador->validarRucPersonaNatural($ruc);
        $this->assertEquals($validarRucPersonaNatural, false);
        $this->assertEquals($this->validador->getError(), 'Dígitos iniciales no validan contra Dígito Idenficador');
    }

    public function test_revisar_que_ruc_correctos_validen_persona_natural()
    {
        // revisar que numeros ruc correctas validen
        $ruc = '0602910945001';
        $validarRucPersonaNatural = $this->validador->validarRucPersonaNatural($ruc);
        $this->assertEquals($validarRucPersonaNatural, true);

        $validarRucPersonaNatural = $this->validador->validarRucPersonaNatural($ruc);
        $this->assertEquals($validarRucPersonaNatural, true);

        $validarRucPersonaNatural = $this->validador->validarRucPersonaNatural($ruc);
        $this->assertEquals($validarRucPersonaNatural, true);
    }
}
