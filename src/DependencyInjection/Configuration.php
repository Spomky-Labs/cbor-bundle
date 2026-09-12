<?php

declare(strict_types=1);

namespace SpomkyLabs\CborBundle\DependencyInjection;

use CBOR\Decoder;
use CBOR\OtherObject\OtherObjectInterface;
use CBOR\Tag\TagInterface;
use function is_a;
use function is_string;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final readonly class Configuration implements ConfigurationInterface
{
    public function __construct(
        private string $alias
    ) {
    }

    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder($this->alias);
        $treeBuilder->getRootNode()
            ->children()
            ->integerNode('max_depth')
            ->info(
                'Maximum nesting depth accepted when decoding. Data nested deeper than this limit is rejected. A low value is recommended when the data comes from an untrusted source.'
            )
            ->defaultValue(Decoder::DEFAULT_MAX_DEPTH)
            ->min(1)
            ->end()
            ->arrayNode('tags')
            ->info(
                'Tag classes to register in addition to the ones the library implements. Each class says which tag number it handles; a class given here replaces the built-in one for the same number.'
            )
            ->scalarPrototype()
            ->validate()
            ->ifTrue(static fn (mixed $class): bool => ! is_string($class) || ! is_a($class, TagInterface::class, true))
            ->thenInvalid('The class %s does not implement ' . TagInterface::class . '.')
            ->end()
            ->end()
            ->end()
            ->arrayNode('other_objects')
            ->info(
                'Classes of major type 7 items (simple values, floats) to register in addition to the ones the library implements. Each class says which additional information values it handles; a class given here replaces the built-in one for the same values.'
            )
            ->scalarPrototype()
            ->validate()
            ->ifTrue(
                static fn (mixed $class): bool => ! is_string($class) || ! is_a($class, OtherObjectInterface::class, true)
            )
            ->thenInvalid('The class %s does not implement ' . OtherObjectInterface::class . '.')
            ->end()
            ->end()
            ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
