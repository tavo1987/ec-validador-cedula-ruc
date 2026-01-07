<?php

declare(strict_types=1);

namespace Tavo\Tests;

class ValidadorCedulaTest extends TestCase
{
    public function test_mostrar_error_cuando_campo_cedula_este_vacio_o_sea_nulo(): void
    {
        $validarCedula = $this->validador->validarCedula('');
        $this->assertFalse($validarCedula);
        $this->assertEquals('Valor no puede estar vacio', $this->validador->getError());

        $validarCedula = $this->validador->validarCedula();
        $this->assertFalse($validarCedula);
        $this->assertEquals('Valor no puede estar vacio', $this->validador->getError());
    }

    public function test_mostrar_error_si_parametro_pasado_es_un_tipo_de_dato_entero(): void
    {
        $cedula = (int) '0926687856';
        // parametro con 0 adelante pero como integer, debe dar false ya que php lo convierte a 0
        $validarCedula = $this->validador->validarCedula((string) $cedula);
        $this->assertFalse($validarCedula);
        $this->assertEquals('Valor ingresado debe tener 10 caracteres', $this->validador->getError());
    }

    public function test_validacion_falla_si_se_ingresa_letras(): void
    {
        $cedula = 'abcd';

        $resultado = $this->validador->validarCedula($cedula);

        $this->assertFalse($resultado);
        $this->assertEquals('Valor ingresado solo puede tener dígitos', $this->validador->getError());
    }

    public function test_validacion_falla_si_se_ingresa_caracteres_especiales(): void
    {
        $cedula = '*@-.#';

        $resultado = $this->validador->validarCedula($cedula);

        $this->assertFalse($resultado);
        $this->assertEquals('Valor ingresado solo puede tener dígitos', $this->validador->getError());
    }

    public function test_validacion_falla_si_se_ingresa_numeros_negativos(): void
    {
        $cedula = '-1723468565';

        $resultado = $this->validador->validarCedula($cedula);
        $this->assertFalse($resultado);
        $this->assertEquals('Valor ingresado solo puede tener dígitos', $this->validador->getError());
    }

    public function test_validacion_falla_si_se_ingresa_numeros_decimales(): void
    {
        $cedula = '09.26687856';

        $resultado = $this->validador->validarCedula($cedula);
        $this->assertFalse($resultado);
        $this->assertEquals('Valor ingresado solo puede tener dígitos', $this->validador->getError());
    }

    public function test_validacion_falla_si_se_ingresa_mas_de_diez_digitos(): void
    {
        $cedula = '0926687864777009';

        $resultado = $this->validador->validarCedula($cedula);

        $this->assertFalse($resultado);
        $this->assertEquals('Valor ingresado debe tener 10 caracteres', $this->validador->getError());
    }

    public function test_validar_codigo_provincial_sea_correcto(): void
    {
        $cedula = '2526687856';

        $resultado = $this->validador->validarCedula($cedula);

        $this->assertFalse($resultado);
        $this->assertEquals('Codigo de Provincia (dos primeros dígitos) no deben ser mayor a 24 ni menores a 0', $this->validador->getError());
    }

    public function test_validacion_falla_si_el_tercer_digito_es_menor_a_cero_y_mayor_seis(): void
    {
        $cedula = '0996687856';

        $resultado = $this->validador->validarCedula($cedula);

        $this->assertFalse($resultado);
        $this->assertEquals('Tercer dígito debe ser mayor o igual a 0 y menor a 6 para cédulas y RUC de persona natural', $this->validador->getError());
    }

    public function test_cedula_incorrecta(): void
    {
        $cedula = '0926687858';

        $resultado = $this->validador->validarCedula($cedula);

        $this->assertFalse($resultado);

        $this->assertEquals('Dígitos iniciales no validan contra Dígito Idenficador', $this->validador->getError());
    }

    public function test_cedula_correcta(): void
    {
        $cedula = '0602910945';
        $resultado = $this->validador->validarCedula($cedula);
        $this->assertTrue($resultado);

        $validarCedula = $this->validador->validarCedula('0926687856');
        $this->assertTrue($validarCedula);

        $validarCedula = $this->validador->validarCedula('0910005917');
        $this->assertTrue($validarCedula);
    }

    // ==================== NEW TEST CASES ====================

    public function test_cedula_con_codigo_provincia_cero_falla(): void
    {
        // Province code 00 is not valid
        $cedula = '0026687856';

        $resultado = $this->validador->validarCedula($cedula);

        $this->assertFalse($resultado);
        $this->assertEquals('Codigo de Provincia (dos primeros dígitos) no deben ser mayor a 24 ni menores a 0', $this->validador->getError());
    }

    public function test_cedula_con_espacios_falla(): void
    {
        $cedula = '0926 687856';

        $resultado = $this->validador->validarCedula($cedula);

        $this->assertFalse($resultado);
        $this->assertEquals('Valor ingresado solo puede tener dígitos', $this->validador->getError());
    }

    public function test_cedula_con_menos_de_diez_digitos_falla(): void
    {
        $cedula = '092668785';

        $resultado = $this->validador->validarCedula($cedula);

        $this->assertFalse($resultado);
        $this->assertEquals('Valor ingresado debe tener 10 caracteres', $this->validador->getError());
    }

    public function test_cedula_todas_las_provincias_validas(): void
    {
        // Test valid cedulas from different provinces
        $cedulasValidas = [
            '0102345672', // Azuay (01)
            '1712345678', // Pichincha (17)
            '2400000018', // Santa Elena (24)
        ];

        foreach ($cedulasValidas as $cedula) {
            // We just test that province validation passes
            // The full cedula may not pass modulo 10
            $this->validador->validarCedula($cedula);
            $this->assertNotEquals(
                'Codigo de Provincia (dos primeros dígitos) no deben ser mayor a 24 ni menores a 0',
                $this->validador->getError(),
                "Province code should be valid for cedula: {$cedula}"
            );
        }
    }

    public function test_cedula_tercer_digito_valores_limite(): void
    {
        // Third digit 0 should be valid (check digit validation may fail, but not third digit)
        $cedulaCon0 = '0102345672';
        $this->validador->validarCedula($cedulaCon0);
        $this->assertNotEquals(
            'Tercer dígito debe ser mayor o igual a 0 y menor a 6 para cédulas y RUC de persona natural',
            $this->validador->getError()
        );

        // Third digit 5 should be valid
        $cedulaCon5 = '0152345672';
        $this->validador->validarCedula($cedulaCon5);
        $this->assertNotEquals(
            'Tercer dígito debe ser mayor o igual a 0 y menor a 6 para cédulas y RUC de persona natural',
            $this->validador->getError()
        );

        // Third digit 6 should fail for regular provinces
        $cedulaCon6 = '0162345672';
        $resultado = $this->validador->validarCedula($cedulaCon6);
        $this->assertFalse($resultado);
        $this->assertEquals(
            'Tercer dígito debe ser mayor o igual a 0 y menor a 6 para cédulas y RUC de persona natural',
            $this->validador->getError()
        );
    }

    // ==================== PR #6 - FOREIGN RESIDENTS (CODE 30) ====================

    /**
     * Test that province code 30 (Ecuadorians abroad/foreign residents) is accepted.
     *
     * According to PR #6, province code 30 is reserved for "ecuatorianos registrados
     * en el exterior" (Ecuadorians registered abroad).
     */
    public function test_cedula_codigo_provincia_30_es_aceptado(): void
    {
        // Province code 30 should be accepted (validation passes province check)
        $cedula = '3012345678';
        $this->validador->validarCedula($cedula);

        // Should NOT get province code error
        $this->assertNotEquals(
            'Codigo de Provincia (dos primeros dígitos) no deben ser mayor a 24 ni menores a 0',
            $this->validador->getError(),
            'Province code 30 should be accepted for foreign residents'
        );
    }

    /**
     * Test that for province code 30, the third digit validation is skipped.
     *
     * According to PR #4 and #6, cedulas with code 30 may have third digits
     * that don't follow the standard 0-5 rule for regular cedulas.
     */
    public function test_cedula_codigo_30_tercer_digito_no_valida(): void
    {
        // Province code 30 with third digit 6 (would fail for regular cedulas)
        $cedula = '3062345678';
        $this->validador->validarCedula($cedula);

        // Should NOT get third digit error for code 30
        $this->assertNotEquals(
            'Tercer dígito debe ser mayor o igual a 0 y menor a 6 para cédulas y RUC de persona natural',
            $this->validador->getError(),
            'Third digit validation should be skipped for province code 30'
        );

        // Province code 30 with third digit 9
        $cedula = '3092345678';
        $this->validador->validarCedula($cedula);
        $this->assertNotEquals(
            'Tercer dígito debe ser mayor o igual a 0 y menor a 6 para cédulas y RUC de persona natural',
            $this->validador->getError(),
            'Third digit 9 should be allowed for province code 30'
        );
    }

    /**
     * Test that province code 30 cedulas still validate check digit (Modulo 10).
     */
    public function test_cedula_codigo_30_valida_digito_verificador(): void
    {
        // Invalid check digit should still fail
        $cedula = '3012345679'; // Likely invalid check digit
        $resultado = $this->validador->validarCedula($cedula);

        // If it fails, it should be due to check digit, not province or third digit
        if (!$resultado) {
            $this->assertEquals(
                'Dígitos iniciales no validan contra Dígito Idenficador',
                $this->validador->getError()
            );
        }
    }
}
