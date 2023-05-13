<?php

declare(strict_types=1);

namespace SpomkyLabs\CborBundle;

use SpomkyLabs\CborBundle\DependencyInjection\Compiler\OtherObjectCompilerPass;
use SpomkyLabs\CborBundle\DependencyInjection\Compiler\TagCompilerPass;
use SpomkyLabs\CborBundle\DependencyInjection\SpomkyLabsCborExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class SpomkyLabsCborBundle extends Bundle
{
    public function getContainerExtension(): SpomkyLabsCborExtension
    {
        return new SpomkyLabsCborExtension();
    }

    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $container->addCompilerPass(new TagCompilerPass());
        $container->addCompilerPass(new OtherObjectCompilerPass());
    }
}
