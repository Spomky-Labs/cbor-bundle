<?php

declare(strict_types=1);

namespace SpomkyLabs\CborBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function __construct(
        private string $alias
    ) {
    }

    public function getConfigTreeBuilder(): TreeBuilder
    {
        return new TreeBuilder($this->alias);
    }
}
