<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20210508\Symfony\Component\DependencyInjection\Loader;

/**
 * GlobFileLoader loads files from a glob pattern.
 *
 * @author Nicolas Grekas <p@tchwork.com>
 */
class GlobFileLoader extends \ECSPrefix20210508\Symfony\Component\DependencyInjection\Loader\FileLoader
{
    /**
     * {@inheritdoc}
     * @param string $type
     */
    public function load($resource, $type = null)
    {
        if (\is_object($type)) {
            $type = (string) $type;
        }
        foreach ($this->glob($resource, \false, $globResource) as $path => $info) {
            $this->import($path);
        }
        $this->container->addResource($globResource);
    }
    /**
     * {@inheritdoc}
     * @param string $type
     */
    public function supports($resource, $type = null)
    {
        return 'glob' === $type;
    }
}