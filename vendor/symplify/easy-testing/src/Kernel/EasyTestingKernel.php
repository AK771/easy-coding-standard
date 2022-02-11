<?php

declare (strict_types=1);
namespace ECSPrefix20220211\Symplify\EasyTesting\Kernel;

use ECSPrefix20220211\Psr\Container\ContainerInterface;
use ECSPrefix20220211\Symplify\EasyTesting\ValueObject\EasyTestingConfig;
use ECSPrefix20220211\Symplify\SymplifyKernel\HttpKernel\AbstractSymplifyKernel;
final class EasyTestingKernel extends \ECSPrefix20220211\Symplify\SymplifyKernel\HttpKernel\AbstractSymplifyKernel
{
    /**
     * @param string[] $configFiles
     */
    public function createFromConfigs(array $configFiles) : \ECSPrefix20220211\Psr\Container\ContainerInterface
    {
        $configFiles[] = \ECSPrefix20220211\Symplify\EasyTesting\ValueObject\EasyTestingConfig::FILE_PATH;
        return $this->create($configFiles);
    }
}
