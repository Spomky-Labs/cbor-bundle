<?php

declare(strict_types=1);

namespace SpomkyLabs\CborBundle\DependencyInjection\Compiler;

use CBOR\OtherObject\OtherObjectInterface;
use CBOR\OtherObject\OtherObjectManager;
use function is_a;
use function is_string;
use function sprintf;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;

/**
 * Files the class of every service tagged "cbor.other_object" in the other object manager.
 *
 * As with tags, the class is not a service: the decoder instantiates it for each item it reads. Only the class of
 * the definition is taken, so the container never tries to build it.
 */
final readonly class OtherObjectCompilerPass implements CompilerPassInterface
{
    public const string TAG = 'cbor.other_object';

    public function process(ContainerBuilder $container): void
    {
        if (! $container->hasDefinition(OtherObjectManager::class)) {
            return;
        }

        $definition = $container->getDefinition(OtherObjectManager::class);

        foreach ($container->findTaggedServiceIds(self::TAG) as $id => $tags) {
            $class = $container->getParameterBag()
                ->resolveValue($container->getDefinition($id)->getClass());
            if (! is_string($class) || ! is_a($class, OtherObjectInterface::class, true)) {
                throw new InvalidArgumentException(sprintf(
                    'The service "%s" is tagged "%s" but its class does not implement "%s".',
                    $id,
                    self::TAG,
                    OtherObjectInterface::class
                ));
            }
            $definition->addMethodCall('add', [$class]);
        }
    }
}
