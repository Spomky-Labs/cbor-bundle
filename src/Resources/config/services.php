<?php

declare(strict_types=1);

use CBOR\Decoder;
use CBOR\Encoder;
use CBOR\OtherObject\OtherObjectManager;
use CBOR\OtherObject\OtherObjectManagerInterface;
use CBOR\Tag\TagManager;
use CBOR\Tag\TagManagerInterface;
use SpomkyLabs\CborBundle\CBORDecoder;
use SpomkyLabs\CborBundle\CBOREncoder;
use SpomkyLabs\CborBundle\OtherObjectManagerFactory;
use SpomkyLabs\CborBundle\TagManagerFactory;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\param;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $container): void {
    $container = $container->services()
        ->defaults()
        ->private()
        ->autoconfigure()
        ->autowire()
    ;

    // The managers start with the whole registry the library implements; the application's own tags and
    // other objects are added to them by the extension (configuration) and the compiler passes (tagged services).
    $container->set(TagManager::class)
        ->factory(TagManagerFactory::create(...))
    ;
    $container->alias(TagManagerInterface::class, TagManager::class);
    $container->set(OtherObjectManager::class)
        ->factory(OtherObjectManagerFactory::create(...))
    ;
    $container->alias(OtherObjectManagerInterface::class, OtherObjectManager::class);

    $container->set(Decoder::class)
        ->public()
        ->args([
            service(TagManagerInterface::class),
            service(OtherObjectManagerInterface::class),
            param('cbor.max_depth'),
        ])
    ;
    $container->set(Encoder::class)->public();
    $container->set(CBORDecoder::class)->public();
    $container->set(CBOREncoder::class);

    // Service aliases for easier access
    $container->alias('cbor.decoder', CBORDecoder::class)->public();
    $container->alias('cbor.encoder', CBOREncoder::class)->public();
};
