<?php

declare(strict_types=1);

namespace SpomkyLabs\CborBundle\DependencyInjection\Compiler;

use CBOR\OtherObject\OtherObjectManagerInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class OtherObjectCompilerPass implements CompilerPassInterface
{
    public const TAG = 'cbor.other_object';

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container): void
    {
        if (! $container->hasDefinition(OtherObjectManagerInterface::class)) {
            return;
        }

        $definition = $container->getDefinition(OtherObjectManagerInterface::class);

        $taggedAlgorithmServices = $container->findTaggedServiceIds(self::TAG);
        foreach ($taggedAlgorithmServices as $id => $tags) {
            $definition->addMethodCall('add', [new Reference($id)]);
        }
    }
}
