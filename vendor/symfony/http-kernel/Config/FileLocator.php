<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20210508\Symfony\Component\HttpKernel\Config;

use ECSPrefix20210508\Symfony\Component\Config\FileLocator as BaseFileLocator;
use ECSPrefix20210508\Symfony\Component\HttpKernel\KernelInterface;
/**
 * FileLocator uses the KernelInterface to locate resources in bundles.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class FileLocator extends \ECSPrefix20210508\Symfony\Component\Config\FileLocator
{
    private $kernel;
    public function __construct(\ECSPrefix20210508\Symfony\Component\HttpKernel\KernelInterface $kernel)
    {
        $this->kernel = $kernel;
        parent::__construct();
    }
    /**
     * {@inheritdoc}
     * @param string $file
     */
    public function locate($file, string $currentPath = null, bool $first = \true)
    {
        if (\is_object($file)) {
            $file = (string) $file;
        }
        if (isset($file[0]) && '@' === $file[0]) {
            $resource = $this->kernel->locateResource($file);
            return $first ? $resource : [$resource];
        }
        return parent::locate($file, $currentPath, $first);
    }
}