<?php

declare(strict_types=1);

namespace Tavo;

use InvalidArgumentException;

/**
 * Ecuador ID and RUC Validator.
 *
 * Validates Ecuadorian identification documents:
 * - Cedula (National ID - 10 digits)
 * - RUC for Natural Persons (13 digits, third digit 0-5)
 * - RUC for Private Companies (13 digits, third digit 9)
 * - RUC for Public Companies (13 digits, third digit 6)
 *
 * @author Edwin Ramirez
 * @author Bryan Suarez
 */
final class ValidadorEc
{
    // Document type constants
    public const TYPE_CEDULA = 'cedula';
    public const TYPE_RUC_NATURAL = 'ruc_natural';
    public const TYPE_RUC_PRIVATE = 'ruc_private';
    public const TYPE_RUC_PUBLIC = 'ruc_public';

    // Province code for foreign residents
    private const FOREIGN_RESIDENT_CODE = 30;

    // Validation algorithms coefficients
    private const MODULO_10_COEFFICIENTS = [2, 1, 2, 1, 2, 1, 2, 1, 2];
    private const MODULO_11_PRIVATE_COEFFICIENTS = [4, 3, 2, 7, 6, 5, 4, 3, 2];
    private const MODULO_11_PUBLIC_COEFFICIENTS = [3, 2, 7, 6, 5, 4, 3, 2];

    private string $error = '';
    private string $documentType = '';

    /**
     * Auto-detect and validate any Ecuadorian identification document.
     *
     * Determines document type based on:
     * - 10 digits: Cedula
     * - 13 digits with third digit 0-5: Natural Person RUC
     * - 13 digits with third digit 6: Public Company RUC
     * - 13 digits with third digit 9: Private Company RUC
     */
    public function validate(string $number = ''): bool
    {
        $this->error = '';
        $this->documentType = '';
        $number = trim($number);

        if (!$this->isValidFormat($number)) {
            return false;
        }

        $length = strlen($number);

        if ($length === 10) {
            $this->documentType = self::TYPE_CEDULA;

            return $this->performCedulaValidation($number);
        }

        if ($length === 13) {
            return $this->detectAndValidateRuc($number);
        }

        $this->error = 'Invalid document length. Cedula must have 10 digits, RUC must have 13 digits';

        return false;
    }

    /**
     * Validate Ecuadorian Cedula (National ID).
     */
    public function validateCedula(string $number = ''): bool
    {
        $this->error = '';
        $this->documentType = '';

        return $this->performCedulaValidation($number);
    }

    /**
     * Validate RUC for Natural Person.
     */
    public function validateNaturalPersonRuc(string $number = ''): bool
    {
        $this->error = '';
        $this->documentType = '';

        return $this->performNaturalPersonRucValidation($number);
    }

    /**
     * Validate RUC for Private Company.
     */
    public function validatePrivateCompanyRuc(string $number = ''): bool
    {
        $this->error = '';
        $this->documentType = '';

        return $this->performPrivateCompanyRucValidation($number);
    }

    /**
     * Validate RUC for Public Company.
     */
    public function validatePublicCompanyRuc(string $number = ''): bool
    {
        $this->error = '';
        $this->documentType = '';

        return $this->performPublicCompanyRucValidation($number);
    }

    /**
     * Get the document type detected in the last validation.
     */
    public function getDocumentType(): string
    {
        return $this->documentType;
    }

    /**
     * Get the error message from the last validation.
     */
    public function getError(): string
    {
        return $this->error;
    }

    // ==================== Legacy Methods (Backwards Compatibility) ====================

    /** @deprecated Use validate() instead */
    public function validar(string $number = ''): bool
    {
        return $this->validate($number);
    }

    /** @deprecated Use validateCedula() instead */
    public function validarCedula(string $number = ''): bool
    {
        return $this->validateCedula($number);
    }

    /** @deprecated Use validateNaturalPersonRuc() instead */
    public function validarRucPersonaNatural(string $number = ''): bool
    {
        return $this->validateNaturalPersonRuc($number);
    }

    /** @deprecated Use validatePrivateCompanyRuc() instead */
    public function validarRucSociedadPrivada(string $number = ''): bool
    {
        return $this->validatePrivateCompanyRuc($number);
    }

    /** @deprecated Use validatePublicCompanyRuc() instead */
    public function validarRucSociedadPublica(string $number = ''): bool
    {
        return $this->validatePublicCompanyRuc($number);
    }

    /** @deprecated Use getDocumentType() instead */
    public function getTipoDocumento(): string
    {
        return $this->getDocumentType();
    }

    // ==================== Internal Validation Methods ====================

    private function performCedulaValidation(string $number): bool
    {
        try {
            $this->assertValidInitialFormat($number, 10);
            $provinceCode = (int) substr($number, 0, 2);
            $this->assertValidProvinceCode($provinceCode);

            if ($provinceCode !== self::FOREIGN_RESIDENT_CODE) {
                $this->assertValidThirdDigit((int) $number[2], self::TYPE_CEDULA);
            }

            $this->assertValidModulo10(substr($number, 0, 9), (int) $number[9]);
        } catch (InvalidArgumentException $e) {
            $this->error = $e->getMessage();

            return false;
        }

        return true;
    }

    private function performNaturalPersonRucValidation(string $number): bool
    {
        try {
            $this->assertValidInitialFormat($number, 13);
            $provinceCode = (int) substr($number, 0, 2);
            $this->assertValidProvinceCode($provinceCode);

            if ($provinceCode !== self::FOREIGN_RESIDENT_CODE) {
                $this->assertValidThirdDigit((int) $number[2], self::TYPE_RUC_NATURAL);
            }

            $this->assertValidEstablishmentCode((int) substr($number, 10, 3));
            $this->assertValidModulo10(substr($number, 0, 9), (int) $number[9]);
        } catch (InvalidArgumentException $e) {
            $this->error = $e->getMessage();

            return false;
        }

        return true;
    }

    private function performPrivateCompanyRucValidation(string $number): bool
    {
        try {
            $this->assertValidInitialFormat($number, 13);
            $this->assertValidProvinceCode((int) substr($number, 0, 2));
            $this->assertValidThirdDigit((int) $number[2], self::TYPE_RUC_PRIVATE);
            $this->assertValidEstablishmentCode((int) substr($number, 10, 3));
            $this->assertValidModulo11(
                substr($number, 0, 9),
                (int) $number[9],
                self::MODULO_11_PRIVATE_COEFFICIENTS
            );
        } catch (InvalidArgumentException $e) {
            $this->error = $e->getMessage();

            return false;
        }

        return true;
    }

    private function performPublicCompanyRucValidation(string $number): bool
    {
        try {
            $this->assertValidInitialFormat($number, 13);
            $this->assertValidProvinceCode((int) substr($number, 0, 2));
            $this->assertValidThirdDigit((int) $number[2], self::TYPE_RUC_PUBLIC);
            $this->assertValidEstablishmentCode((int) substr($number, 9, 4));
            $this->assertValidModulo11(
                substr($number, 0, 8),
                (int) $number[8],
                self::MODULO_11_PUBLIC_COEFFICIENTS
            );
        } catch (InvalidArgumentException $e) {
            $this->error = $e->getMessage();

            return false;
        }

        return true;
    }

    // ==================== Helper Methods ====================

    private function isValidFormat(string $number): bool
    {
        if (empty($number)) {
            $this->error = 'Value cannot be empty';

            return false;
        }

        if (!ctype_digit($number)) {
            $this->error = 'Value can only contain digits';

            return false;
        }

        return true;
    }

    private function detectAndValidateRuc(string $number): bool
    {
        $thirdDigit = (int) $number[2];

        if ($thirdDigit >= 0 && $thirdDigit <= 5) {
            $this->documentType = self::TYPE_RUC_NATURAL;

            return $this->performNaturalPersonRucValidation($number);
        }

        if ($thirdDigit === 6) {
            $this->documentType = self::TYPE_RUC_PUBLIC;

            return $this->performPublicCompanyRucValidation($number);
        }

        if ($thirdDigit === 9) {
            $this->documentType = self::TYPE_RUC_PRIVATE;

            return $this->performPrivateCompanyRucValidation($number);
        }

        $this->error = 'Invalid third digit for RUC. Must be 0-5 (natural), 6 (public), or 9 (private)';

        return false;
    }

    private function assertValidInitialFormat(string $number, int $requiredLength): void
    {
        if (empty($number)) {
            throw new InvalidArgumentException('Value cannot be empty');
        }

        if (!ctype_digit($number)) {
            throw new InvalidArgumentException('Value can only contain digits');
        }

        if (strlen($number) !== $requiredLength) {
            throw new InvalidArgumentException("Value must have {$requiredLength} characters");
        }
    }

    private function assertValidProvinceCode(int $code): void
    {
        $isValid = ($code >= 1 && $code <= 24) || $code === self::FOREIGN_RESIDENT_CODE;

        if (!$isValid) {
            throw new InvalidArgumentException(
                'Province code (first two digits) must be between 01-24 or 30'
            );
        }
    }

    private function assertValidThirdDigit(int $digit, string $type): void
    {
        $isValid = match ($type) {
            self::TYPE_CEDULA, self::TYPE_RUC_NATURAL => $digit >= 0 && $digit <= 5,
            self::TYPE_RUC_PRIVATE => $digit === 9,
            self::TYPE_RUC_PUBLIC  => $digit === 6,
            default                => throw new InvalidArgumentException('Invalid identification type'),
        };

        if (!$isValid) {
            $message = match ($type) {
                self::TYPE_CEDULA, self::TYPE_RUC_NATURAL => 'Third digit must be between 0 and 5 for cedula and natural person RUC',
                self::TYPE_RUC_PRIVATE => 'Third digit must be 9 for private companies',
                self::TYPE_RUC_PUBLIC  => 'Third digit must be 6 for public companies',
                default                => 'Invalid identification type',
            };

            throw new InvalidArgumentException($message);
        }
    }

    private function assertValidEstablishmentCode(int $code): void
    {
        if ($code < 1) {
            throw new InvalidArgumentException('Establishment code cannot be 0');
        }
    }

    /**
     * Modulo 10 algorithm for Cedula and Natural Person RUC.
     */
    private function assertValidModulo10(string $initialDigits, int $checkDigit): void
    {
        $sum = 0;
        $digits = str_split($initialDigits);

        foreach ($digits as $index => $value) {
            $product = (int) $value * self::MODULO_10_COEFFICIENTS[$index];
            $sum += ($product >= 10) ? array_sum(str_split((string) $product)) : $product;
        }

        $remainder = $sum % 10;
        $expected = ($remainder === 0) ? 0 : 10 - $remainder;

        if ($expected !== $checkDigit) {
            throw new InvalidArgumentException('Check digit validation failed');
        }
    }

    /**
     * Modulo 11 algorithm for Private and Public Company RUC.
     */
    private function assertValidModulo11(string $initialDigits, int $checkDigit, array $coefficients): void
    {
        $sum = 0;
        $digits = str_split($initialDigits);

        foreach ($digits as $index => $value) {
            $sum += (int) $value * $coefficients[$index];
        }

        $remainder = $sum % 11;
        $expected = ($remainder === 0) ? 0 : 11 - $remainder;

        if ($expected !== $checkDigit) {
            throw new InvalidArgumentException('Check digit validation failed');
        }
    }
}
