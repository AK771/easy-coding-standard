<?php

declare (strict_types=1);
namespace ECSPrefix20211117\Symplify\SymplifyKernel\Contract;

use ECSPrefix20211117\Psr\Container\ContainerInterface;
/**
 * @api
 */
interface LightKernelInterface
{
    /**
     * @param string[] $configFiles
     */
    public function createFromConfigs($configFiles) : \ECSPrefix20211117\Psr\Container\ContainerInterface;
    public function getContainer() : \ECSPrefix20211117\Psr\Container\ContainerInterface;
}
