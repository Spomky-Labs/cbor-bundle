<?php

declare(strict_types=1);

/*
 * The MIT License (MIT)
 *
 * Copyright (c) 2014-2018 Spomky-Labs
 *
 * This software may be modified and distributed under the terms
 * of the MIT license.  See the LICENSE file for details.
 */

namespace SpomkyLabs\CborBundle;

use SpomkyLabs\CborBundle\DependencyInjection\SpomkyLabsCborExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class SpomkyLabsCborBundle extends Bundle
{
    public function getContainerExtension()
    {
        return new SpomkyLabsCborExtension('cbor');
    }
}
