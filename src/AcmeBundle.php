<?php

namespace Acme\AcmeBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class AcmeBundle extends Bundle
{
    public function getPath()
    {
        return \dirname(__DIR__);
    }
}
