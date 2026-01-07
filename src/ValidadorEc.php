<?php

declare(strict_types=1);

namespace Tavo;

use Exception;
use InvalidArgumentException;

/**
 * ValidadorEc - Ecuador ID and RUC Validator.
 *
 * Contains methods to validate Ecuadorian identification documents:
 * - Cedula (National ID)
 * - RUC for Natural Persons
 * - RUC for Private Companies
 * - RUC for Public Companies
 *
 * Public validation methods:
 * - validar() - Auto-detect and validate any document type
 * - validarCedula()
 * - validarRucPersonaNatural()
 * - validarRucSociedadPrivada()
 * - validarRucSociedadPublica()
 */
class ValidadorEc
{
    /**
     * Document type constants.
     */
    public const TIPO_CEDULA = 'cedula';
    public const TIPO_RUC_NATURAL = 'ruc_natural';
    public const TIPO_RUC_PRIVADA = 'ruc_privada';
    public const TIPO_RUC_PUBLICA = 'ruc_publica';

    /**
     * Error message from the last validation.
     */
    protected string $error = '';

    /**
     * Detected document type from the last validation.
     */
    protected string $tipoDocumento = '';

    /**
     * Auto-detect and validate any Ecuadorian identification document.
     *
     * This method automatically determines the document type based on:
     * - Length: 10 digits = Cedula, 13 digits = RUC
     * - For RUC, the third digit determines the type:
     *   - 0-5: Natural Person RUC
     *   - 6: Public Company RUC
     *   - 9: Private Company RUC
     *
     * @param string $numero Document number to validate
     *
     * @return bool True if valid, false otherwise
     */
    public function validar(string $numero = ''): bool
    {
        $this->setError('');
        $this->tipoDocumento = '';

        $numero = trim($numero);

        if (empty($numero)) {
            $this->setError('Value cannot be empty');

            return false;
        }

        if (!ctype_digit($numero)) {
            $this->setError('Value can only contain digits');

            return false;
        }

        $length = strlen($numero);

        // Cedula: 10 digits
        if ($length === 10) {
            $this->tipoDocumento = self::TIPO_CEDULA;

            return $this->validarCedula($numero);
        }

        // RUC: 13 digits
        if ($length === 13) {
            $tercerDigito = (int) $numero[2];

            // Natural Person RUC (third digit 0-5)
            if ($tercerDigito >= 0 && $tercerDigito <= 5) {
                $this->tipoDocumento = self::TIPO_RUC_NATURAL;

                return $this->validarRucPersonaNatural($numero);
            }

            // Public Company RUC (third digit 6)
            if ($tercerDigito === 6) {
                $this->tipoDocumento = self::TIPO_RUC_PUBLICA;

                return $this->validarRucSociedadPublica($numero);
            }

            // Private Company RUC (third digit 9)
            if ($tercerDigito === 9) {
                $this->tipoDocumento = self::TIPO_RUC_PRIVADA;

                return $this->validarRucSociedadPrivada($numero);
            }

            $this->setError('Invalid third digit for RUC. Must be 0-5 (natural), 6 (public), or 9 (private)');

            return false;
        }

        $this->setError('Invalid document length. Cedula must have 10 digits, RUC must have 13 digits');

        return false;
    }

    /**
     * Get the document type detected in the last validation.
     *
     * @return string One of the TIPO_* constants, or empty string if not detected
     */
    public function getTipoDocumento(): string
    {
        return $this->tipoDocumento;
    }

    /**
     * Validate Ecuadorian Cedula (National ID).
     *
     * For province code 30 (Ecuadorians abroad/foreign residents),
     * the third digit validation is skipped as different rules may apply.
     *
     * @param string $numero Cedula number (10 digits)
     *
     * @return bool True if valid, false otherwise
     */
    public function validarCedula(string $numero = ''): bool
    {
        $numero = (string) $numero;
        $this->setError('');

        try {
            $this->validarInicial($numero, 10);
            $codigoProvincia = substr($numero, 0, 2);
            $this->validarCodigoProvincia($codigoProvincia);

            // For province code 30 (foreign residents), skip third digit validation
            // as different rules may apply
            if ((int) $codigoProvincia !== 30) {
                $this->validarTercerDigito($numero[2], self::TIPO_CEDULA);
            }

            $this->algoritmoModulo10(substr($numero, 0, 9), $numero[9]);
        } catch (Exception $e) {
            $this->setError($e->getMessage());

            return false;
        }

        return true;
    }

    /**
     * Validate RUC for Natural Person.
     *
     * For province code 30 (Ecuadorians abroad/foreign residents),
     * the third digit validation is skipped as different rules may apply.
     *
     * @param string $numero RUC number (13 digits)
     *
     * @return bool True if valid, false otherwise
     */
    public function validarRucPersonaNatural(string $numero = ''): bool
    {
        $numero = (string) $numero;
        $this->setError('');

        try {
            $this->validarInicial($numero, 13);
            $codigoProvincia = substr($numero, 0, 2);
            $this->validarCodigoProvincia($codigoProvincia);

            // For province code 30 (foreign residents), skip third digit validation
            if ((int) $codigoProvincia !== 30) {
                $this->validarTercerDigito($numero[2], self::TIPO_RUC_NATURAL);
            }

            $this->validarCodigoEstablecimiento(substr($numero, 10, 3));
            $this->algoritmoModulo10(substr($numero, 0, 9), $numero[9]);
        } catch (Exception $e) {
            $this->setError($e->getMessage());

            return false;
        }

        return true;
    }

    /**
     * Validate RUC for Private Company.
     *
     * @param string $numero RUC number (13 digits)
     *
     * @return bool True if valid, false otherwise
     */
    public function validarRucSociedadPrivada(string $numero = ''): bool
    {
        $numero = (string) $numero;
        $this->setError('');

        try {
            $this->validarInicial($numero, 13);
            $this->validarCodigoProvincia(substr($numero, 0, 2));
            $this->validarTercerDigito($numero[2], self::TIPO_RUC_PRIVADA);
            $this->validarCodigoEstablecimiento(substr($numero, 10, 3));
            $this->algoritmoModulo11(substr($numero, 0, 9), $numero[9], self::TIPO_RUC_PRIVADA);
        } catch (Exception $e) {
            $this->setError($e->getMessage());

            return false;
        }

        return true;
    }

    /**
     * Validate RUC for Public Company.
     *
     * @param string $numero RUC number (13 digits)
     *
     * @return bool True if valid, false otherwise
     */
    public function validarRucSociedadPublica(string $numero = ''): bool
    {
        $numero = (string) $numero;
        $this->setError('');

        try {
            $this->validarInicial($numero, 13);
            $this->validarCodigoProvincia(substr($numero, 0, 2));
            $this->validarTercerDigito($numero[2], self::TIPO_RUC_PUBLICA);
            $this->validarCodigoEstablecimiento(substr($numero, 9, 4));
            $this->algoritmoModulo11(substr($numero, 0, 8), $numero[8], self::TIPO_RUC_PUBLICA);
        } catch (Exception $e) {
            $this->setError($e->getMessage());

            return false;
        }

        return true;
    }

    /**
     * Initial validations for Cedula and RUC.
     *
     * @param string $numero     Document number
     * @param int    $caracteres Required character count
     *
     * @throws InvalidArgumentException When validation fails
     *
     * @return bool True if valid
     */
    protected function validarInicial(string $numero, int $caracteres): bool
    {
        if (empty($numero)) {
            throw new InvalidArgumentException('Valor no puede estar vacio');
        }

        if (!ctype_digit($numero)) {
            throw new InvalidArgumentException('Valor ingresado solo puede tener dígitos');
        }

        if (strlen($numero) !== $caracteres) {
            throw new InvalidArgumentException("Valor ingresado debe tener {$caracteres} caracteres");
        }

        return true;
    }

    /**
     * Validate province code (first two digits of Cedula/RUC).
     *
     * Valid codes:
     * - 01-24: Ecuadorian provinces
     * - 30: Foreign residents
     *
     * @param string $numero First two digits
     *
     * @throws InvalidArgumentException When province code is invalid
     *
     * @return bool True if valid
     */
    protected function validarCodigoProvincia(string $numero): bool
    {
        $codigo = (int) $numero;

        // Valid province codes: 01-24 and 30 (foreign residents)
        $esProvinciaValida = ($codigo >= 1 && $codigo <= 24) || $codigo === 30;

        if (!$esProvinciaValida) {
            throw new InvalidArgumentException(
                'Codigo de Provincia (dos primeros dígitos) no deben ser mayor a 24 ni menores a 0'
            );
        }

        return true;
    }

    /**
     * Validate third digit based on document type.
     *
     * - Cedula and Natural Person RUC: 0-5
     * - Private Company RUC: 9
     * - Public Company RUC: 6
     *
     * @param string $numero Third digit
     * @param string $tipo   Document type
     *
     * @throws InvalidArgumentException When third digit is invalid
     *
     * @return bool True if valid
     */
    protected function validarTercerDigito(string $numero, string $tipo): bool
    {
        $digito = (int) $numero;

        switch ($tipo) {
            case self::TIPO_CEDULA:
            case self::TIPO_RUC_NATURAL:
                if ($digito < 0 || $digito > 5) {
                    throw new InvalidArgumentException(
                        'Tercer dígito debe ser mayor o igual a 0 y menor a 6 para cédulas y RUC de persona natural'
                    );
                }
                break;

            case self::TIPO_RUC_PRIVADA:
                if ($digito !== 9) {
                    throw new InvalidArgumentException(
                        'Tercer dígito debe ser igual a 9 para sociedades privadas'
                    );
                }
                break;

            case self::TIPO_RUC_PUBLICA:
                if ($digito !== 6) {
                    throw new InvalidArgumentException(
                        'Tercer dígito debe ser igual a 6 para sociedades públicas'
                    );
                }
                break;

            default:
                throw new InvalidArgumentException('Tipo de Identificacion no existe.');
        }

        return true;
    }

    /**
     * Validate establishment code.
     *
     * The establishment code cannot be 0 (must be 001 or higher).
     *
     * @param string $numero Establishment code digits
     *
     * @throws InvalidArgumentException When code is 0
     *
     * @return bool True if valid
     */
    protected function validarCodigoEstablecimiento(string $numero): bool
    {
        if ((int) $numero < 1) {
            throw new InvalidArgumentException('Código de establecimiento no puede ser 0');
        }

        return true;
    }

    /**
     * Modulo 10 algorithm for Cedula and Natural Person RUC validation.
     *
     * Coefficients: [2, 1, 2, 1, 2, 1, 2, 1, 2]
     *
     * Steps:
     * 1. Multiply each digit by its coefficient
     * 2. If result >= 10, sum the digits of the result
     * 3. Sum all results
     * 4. Calculate: result = (sum % 10 == 0) ? 0 : 10 - (sum % 10)
     * 5. Result must equal the verification digit
     *
     * @param string $digitosIniciales  First 9 digits
     * @param string $digitoVerificador 10th digit (verification digit)
     *
     * @throws InvalidArgumentException When verification fails
     *
     * @return bool True if valid
     */
    protected function algoritmoModulo10(string $digitosIniciales, string $digitoVerificador): bool
    {
        $coeficientes = [2, 1, 2, 1, 2, 1, 2, 1, 2];
        $digitoVerificador = (int) $digitoVerificador;
        $digitos = str_split($digitosIniciales);

        $total = 0;
        foreach ($digitos as $key => $valor) {
            $producto = (int) $valor * $coeficientes[$key];

            if ($producto >= 10) {
                $producto = array_sum(str_split((string) $producto));
            }

            $total += $producto;
        }

        $residuo = $total % 10;
        $resultado = ($residuo === 0) ? 0 : 10 - $residuo;

        if ($resultado !== $digitoVerificador) {
            throw new InvalidArgumentException('Dígitos iniciales no validan contra Dígito Idenficador');
        }

        return true;
    }

    /**
     * Modulo 11 algorithm for Private and Public Company RUC validation.
     *
     * Private Company coefficients: [4, 3, 2, 7, 6, 5, 4, 3, 2]
     * Public Company coefficients: [3, 2, 7, 6, 5, 4, 3, 2]
     *
     * Steps:
     * 1. Multiply each digit by its coefficient
     * 2. Sum all results
     * 3. Calculate: result = (sum % 11 == 0) ? 0 : 11 - (sum % 11)
     * 4. Result must equal the verification digit
     *
     * @param string $digitosIniciales  Initial digits (9 for private, 8 for public)
     * @param string $digitoVerificador Verification digit
     * @param string $tipo              Document type (ruc_privada or ruc_publica)
     *
     * @throws InvalidArgumentException When verification fails
     *
     * @return bool True if valid
     */
    protected function algoritmoModulo11(string $digitosIniciales, string $digitoVerificador, string $tipo): bool
    {
        $coeficientes = match ($tipo) {
            self::TIPO_RUC_PRIVADA => [4, 3, 2, 7, 6, 5, 4, 3, 2],
            self::TIPO_RUC_PUBLICA => [3, 2, 7, 6, 5, 4, 3, 2],
            default                => throw new InvalidArgumentException('Tipo de Identificacion no existe.'),
        };

        $digitoVerificador = (int) $digitoVerificador;
        $digitos = str_split($digitosIniciales);

        $total = 0;
        foreach ($digitos as $key => $valor) {
            $total += (int) $valor * $coeficientes[$key];
        }

        $residuo = $total % 11;
        $resultado = ($residuo === 0) ? 0 : 11 - $residuo;

        if ($resultado !== $digitoVerificador) {
            throw new InvalidArgumentException('Dígitos iniciales no validan contra Dígito Idenficador');
        }

        return true;
    }

    /**
     * Get the error message from the last validation.
     *
     * @return string Error message, or empty string if no error
     */
    public function getError(): string
    {
        return $this->error;
    }

    /**
     * Set the error message.
     *
     * @param string $newError Error message
     *
     * @return self
     */
    public function setError(string $newError): self
    {
        $this->error = $newError;

        return $this;
    }
}
