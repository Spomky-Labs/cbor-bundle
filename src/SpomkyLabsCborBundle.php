<?php

declare(strict_types=1);

namespace SpomkyLabs\CborBundle;

use SpomkyLabs\CborBundle\DependencyInjection\SpomkyLabsCborExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class SpomkyLabsCborBundle extends Bundle
{
    public function getContainerExtension(): SpomkyLabsCborExtension
    {
        return new SpomkyLabsCborExtension('cbor');
    }
}
