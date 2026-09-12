<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use SpomkyLabs\CborBundle\DependencyInjection\Compiler\OtherObjectCompilerPass;
use SpomkyLabs\CborBundle\DependencyInjection\Compiler\TagCompilerPass;
use SpomkyLabs\CborBundle\Tests\Fixtures\Cbor\AutoconfiguredSimpleObject;
use SpomkyLabs\CborBundle\Tests\Fixtures\Cbor\AutoconfiguredTag;
use SpomkyLabs\CborBundle\Tests\Fixtures\Cbor\ConfiguredSimpleObject;
use SpomkyLabs\CborBundle\Tests\Fixtures\Cbor\ConfiguredTag;
use SpomkyLabs\CborBundle\Tests\Fixtures\Cbor\TaggedServiceSimpleObject;
use SpomkyLabs\CborBundle\Tests\Fixtures\Cbor\TaggedServiceTag;

return static function (ContainerConfigurator $container) {
    $container->extension('cbor', [
        'max_depth' => 64,
        'tags' => [ConfiguredTag::class],
        'other_objects' => [ConfiguredSimpleObject::class],
    ]);
    $container->extension('framework', [
        'test' => true,
        'secret' => 'test',
        'http_method_override' => true,
        'session' => [
            'storage_factory_id' => 'session.storage.factory.mock_file',
        ],
        'serializer' => [
            'enabled' => true,
        ],
    ]);

    // The other two ways of registering a class: an explicit service tag, and the autoconfiguration the bundle
    // sets up for the interfaces. Neither service can be built (their constructors take the decoded data), which
    // is the point: the bundle only takes their class.
    $services = $container->services();
    $services->set(TaggedServiceTag::class)->tag(TagCompilerPass::TAG);
    $services->set(AutoconfiguredTag::class)->autoconfigure();
    $services->set(TaggedServiceSimpleObject::class)->tag(OtherObjectCompilerPass::TAG);
    $services->set(AutoconfiguredSimpleObject::class)->autoconfigure();
};
