<?php

declare(strict_types=1);

namespace Tavo\Tests;

use PHPUnit\Framework\TestCase as BaseTestCase;
use Tavo\ValidadorEc;

class TestCase extends BaseTestCase
{
    /**
     * Validador instance available for all test methods.
     */
    protected ValidadorEc $validador;

    /**
     * Set up the ValidadorEc instance before each test.
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->validador = new ValidadorEc();
    }
}
