<?php

namespace Acme\AcmeBundle\Tests;

use Acme\AcmeBundle\AcmeBundle;
use PHPUnit\Framework\TestCase;

class AcmeBundleTest extends TestCase
{
    public function testGetPath(): void
    {
        $this->assertSame(\dirname(__DIR__), (new AcmeBundle())->getPath());
    }
}
