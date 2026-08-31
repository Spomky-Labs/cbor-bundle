<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\PHPUnit\CodeQuality\Rector\Class_\PreferPHPUnitThisCallRector;
use Rector\PHPUnit\Set\PHPUnitSetList;
use Rector\Set\ValueObject\LevelSetList;
use Rector\Set\ValueObject\SetList;
use Rector\Symfony\Set\SymfonySetList;
use Rector\ValueObject\PhpVersion;

$builder = RectorConfig::configure();
if (file_exists('/tools/.composer/vendor-bin/phpunit/vendor/autoload.php')) {
    $builder->withAutoloadPaths(['/tools/.composer/vendor-bin/phpunit/vendor/autoload.php']);
}
$builder->withSets([
    SetList::DEAD_CODE,
    LevelSetList::UP_TO_PHP_83,
    PHPUnitSetList::PHPUNIT_CODE_QUALITY,
    PHPUnitSetList::ANNOTATIONS_TO_ATTRIBUTES,
    SymfonySetList::SYMFONY_CODE_QUALITY,
]);
$builder->withComposerBased(phpunit: true, symfony: true);
$builder->withPhpVersion(PhpVersion::PHP_83);
$builder->withPaths([
    __DIR__ . '/../src',
    __DIR__ . '/../tests',
    __DIR__ . '/../castor.php',
    __DIR__ . '/ecs.php',
    __DIR__ . '/rector.php',
]);
$builder->withSkip([PreferPHPUnitThisCallRector::class]);
$builder->withParallel();
$builder->withImportNames();

return $builder;
