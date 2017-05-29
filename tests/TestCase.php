<?php

namespace Tests;

use Tavo\ValidadorEc;
use PHPUnit\Framework\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
{
    /**
     * Validador
     *
     * Guarda Instancia de clase ValidarIdentificacion() disponible para todos los métodos
     *
     * @var string
     * @access protected
     */
    protected $validador;

    /**
     * Inicio objecto Validador()
     */
    protected function setUp()
    {
        $this->validador = new ValidadorEc();
    }
}
