<?php

declare (strict_types=1);
namespace ECSPrefix20210727\Symplify\EasyTesting\HttpKernel;

use ECSPrefix20210727\Symfony\Component\Config\Loader\LoaderInterface;
use ECSPrefix20210727\Symplify\SymplifyKernel\HttpKernel\AbstractSymplifyKernel;
final class EasyTestingKernel extends \ECSPrefix20210727\Symplify\SymplifyKernel\HttpKernel\AbstractSymplifyKernel
{
    /**
     * @param \Symfony\Component\Config\Loader\LoaderInterface $loader
     * @return void
     */
    public function registerContainerConfiguration($loader)
    {
        $loader->load(__DIR__ . '/../../config/config.php');
    }
}
