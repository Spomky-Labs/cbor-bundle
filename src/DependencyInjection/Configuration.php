<?php

declare(strict_types=1);

namespace SpomkyLabs\CborBundle\DependencyInjection;

use CBOR\Decoder;
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
            ->end()
        ;

        return $treeBuilder;
    }
}
