<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Php81\Rector\Array_\ArrayToFirstClassCallableRector;
use Rector\PHPUnit\CodeQuality\Rector\Class_\PreferPHPUnitThisCallRector;
use Rector\PHPUnit\Set\PHPUnitSetList;
use Rector\Renaming\Rector\Name\RenameClassRector;
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
// RenameClassRector is skipped on the test kernel: the Symfony sets are picked from the installed version, and
// on Symfony 8.1 they rewrite HttpKernel\Bundle\BundleInterface to DependencyInjection\Kernel\BundleInterface,
// a class that does not exist in the 6.4 and 7.x versions this branch also supports.
// ArrayToFirstClassCallableRector is skipped on the service configuration: ServiceConfigurator::factory() only
// takes a Closure since Symfony 7.1, and the [class, method] array is what the 6.4 version this branch supports
// understands.
$builder->withSkip([
    PreferPHPUnitThisCallRector::class,
    RenameClassRector::class => [__DIR__ . '/../tests/AppKernel.php'],
    ArrayToFirstClassCallableRector::class => [__DIR__ . '/../src/Resources/config/services.php'],
]);
$builder->withParallel();
$builder->withImportNames();

return $builder;
