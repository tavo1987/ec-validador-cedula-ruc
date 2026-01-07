<?php

declare(strict_types=1);

namespace Tavo\Tests;

use PHPUnit\Framework\TestCase as BaseTestCase;
use Tavo\ValidadorEc;

class TestCase extends BaseTestCase
{
    protected ValidadorEc $validator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->validator = new ValidadorEc();
    }
}
