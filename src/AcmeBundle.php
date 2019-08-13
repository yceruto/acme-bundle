<?php

namespace Acme\AcmeBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\HttpKernel\Kernel;

class AcmeBundle extends Bundle
{
    public function getPath()
    {
        return Kernel::VERSION_ID >= 40400 ? \dirname(__DIR__) : __DIR__;
    }
}
