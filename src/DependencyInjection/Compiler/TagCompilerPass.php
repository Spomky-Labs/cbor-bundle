<?php

declare(strict_types=1);

namespace SpomkyLabs\CborBundle\DependencyInjection\Compiler;

use CBOR\Tag\TagInterface;
use CBOR\Tag\TagManager;
use function is_a;
use function is_string;
use function sprintf;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;

/**
 * Files the class of every service tagged "cbor.tag" in the tag manager.
 *
 * A tag class is not a service: the decoder instantiates it for each tagged item it reads, with the data of that
 * item. The service definition only names the class, which is why it is never referenced here -- only its class
 * is taken, so the container never tries to build it.
 */
final readonly class TagCompilerPass implements CompilerPassInterface
{
    public const string TAG = 'cbor.tag';

    public function process(ContainerBuilder $container): void
    {
        if (! $container->hasDefinition(TagManager::class)) {
            return;
        }

        $definition = $container->getDefinition(TagManager::class);

        foreach ($container->findTaggedServiceIds(self::TAG) as $id => $tags) {
            $class = $container->getParameterBag()
                ->resolveValue($container->getDefinition($id)->getClass());
            if (! is_string($class) || ! is_a($class, TagInterface::class, true)) {
                throw new InvalidArgumentException(sprintf(
                    'The service "%s" is tagged "%s" but its class does not implement "%s".',
                    $id,
                    self::TAG,
                    TagInterface::class
                ));
            }
            $definition->addMethodCall('register', [$class::getTagId(), $class]);
        }
    }
}
