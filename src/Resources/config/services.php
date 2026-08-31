<?php

declare(strict_types=1);

use CBOR\Decoder;
use CBOR\OtherObject\OtherObjectManager;
use CBOR\Tag\TagManager;
use SpomkyLabs\CborBundle\CBORDecoder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\param;

return static function (ContainerConfigurator $container): void {
    $container = $container->services()
        ->defaults()
        ->private()
        ->autoconfigure()
        ->autowire()
    ;

    $container->set(Decoder::class)
        ->public()
        ->arg('$maxDepth', param('cbor.max_depth'))
    ;
    $container->set(CBORDecoder::class)->public();
    $container->set(OtherObjectManager::class);
    $container->set(TagManager::class);
};
