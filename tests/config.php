<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return static function (ContainerConfigurator $container) {
    $container->extension('framework', [
        'test' => true,
        'secret' => 'test',
        'http_method_override' => true,
        'session' => [
            'storage_factory_id' => 'session.storage.factory.mock_file',
        ],
    ]);
};
