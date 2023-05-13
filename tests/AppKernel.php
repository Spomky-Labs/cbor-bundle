<?php

declare(strict_types=1);

namespace SpomkyLabs\CborBundle\Tests;

use SpomkyLabs\CborBundle\SpomkyLabsCborBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\HttpKernel\Kernel;

final class AppKernel extends Kernel
{
    public function __construct(string $environment)
    {
        parent::__construct($environment, false);
    }

    /**
     * @return BundleInterface[]
     */
    public function registerBundles(): array
    {
        return [new FrameworkBundle(), new SpomkyLabsCborBundle()];
    }

    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        $loader->load(__DIR__ . '/config.php');
    }
}
