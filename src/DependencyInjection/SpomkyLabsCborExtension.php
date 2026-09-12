<?php

declare(strict_types=1);

namespace SpomkyLabs\CborBundle\DependencyInjection;

use CBOR\OtherObject\OtherObjectInterface;
use CBOR\Tag\TagInterface;
use Override;
use SpomkyLabs\CborBundle\DependencyInjection\Compiler\OtherObjectCompilerPass;
use SpomkyLabs\CborBundle\DependencyInjection\Compiler\TagCompilerPass;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;

final class SpomkyLabsCborExtension extends Extension
{
    private const string ALIAS = 'cbor';

    #[Override]
    public function getAlias(): string
    {
        return self::ALIAS;
    }

    public function load(array $configs, ContainerBuilder $container): void
    {
        /** @var array{max_depth: int} $config */
        $config = $this->processConfiguration(new Configuration(self::ALIAS), $configs);
        $container->setParameter(self::ALIAS . '.max_depth', $config['max_depth']);

        $container->registerForAutoconfiguration(TagInterface::class)->addTag(TagCompilerPass::TAG);
        $container->registerForAutoconfiguration(OtherObjectInterface::class)->addTag(OtherObjectCompilerPass::TAG);

        $loader = new PhpFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.php');
    }

    #[Override]
    public function getConfiguration(array $config, ContainerBuilder $container): ConfigurationInterface
    {
        return new Configuration(self::ALIAS);
    }
}
