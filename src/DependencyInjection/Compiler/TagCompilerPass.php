<?php

declare(strict_types=1);

namespace SpomkyLabs\CborBundle\DependencyInjection\Compiler;

use CBOR\Tag\TagManagerInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class TagCompilerPass implements CompilerPassInterface
{
    public const TAG = 'cbor.tag';

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container): void
    {
        if (! $container->hasDefinition(TagManagerInterface::class)) {
            return;
        }

        $definition = $container->getDefinition(TagManagerInterface::class);

        $taggedAlgorithmServices = $container->findTaggedServiceIds(self::TAG);
        foreach ($taggedAlgorithmServices as $id => $tags) {
            $definition->addMethodCall('add', [new Reference($id)]);
        }
    }
}
