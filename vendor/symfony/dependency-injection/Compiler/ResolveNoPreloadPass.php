<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20210508\Symfony\Component\DependencyInjection\Compiler;

use ECSPrefix20210508\Symfony\Component\DependencyInjection\ContainerBuilder;
use ECSPrefix20210508\Symfony\Component\DependencyInjection\Definition;
use ECSPrefix20210508\Symfony\Component\DependencyInjection\Reference;
/**
 * Propagate the "container.no_preload" tag.
 *
 * @author Nicolas Grekas <p@tchwork.com>
 */
class ResolveNoPreloadPass extends \ECSPrefix20210508\Symfony\Component\DependencyInjection\Compiler\AbstractRecursivePass
{
    const DO_PRELOAD_TAG = '.container.do_preload';
    private $tagName;
    private $resolvedIds = [];
    /**
     * @param string $tagName
     */
    public function __construct($tagName = 'container.no_preload')
    {
        if (\is_object($tagName)) {
            $tagName = (string) $tagName;
        }
        $this->tagName = $tagName;
    }
    /**
     * {@inheritdoc}
     */
    public function process(\ECSPrefix20210508\Symfony\Component\DependencyInjection\ContainerBuilder $container)
    {
        $this->container = $container;
        try {
            foreach ($container->getDefinitions() as $id => $definition) {
                if ($definition->isPublic() && !$definition->isPrivate() && !isset($this->resolvedIds[$id])) {
                    $this->resolvedIds[$id] = \true;
                    $this->processValue($definition, \true);
                }
            }
            foreach ($container->getAliases() as $alias) {
                if ($alias->isPublic() && !$alias->isPrivate() && !isset($this->resolvedIds[$id = (string) $alias]) && $container->hasDefinition($id)) {
                    $this->resolvedIds[$id] = \true;
                    $this->processValue($container->getDefinition($id), \true);
                }
            }
        } finally {
            $this->resolvedIds = [];
            $this->container = null;
        }
        foreach ($container->getDefinitions() as $definition) {
            if ($definition->hasTag(self::DO_PRELOAD_TAG)) {
                $definition->clearTag(self::DO_PRELOAD_TAG);
            } elseif (!$definition->isDeprecated() && !$definition->hasErrors()) {
                $definition->addTag($this->tagName);
            }
        }
    }
    /**
     * {@inheritdoc}
     * @param bool $isRoot
     */
    protected function processValue($value, $isRoot = \false)
    {
        if ($value instanceof \ECSPrefix20210508\Symfony\Component\DependencyInjection\Reference && \ECSPrefix20210508\Symfony\Component\DependencyInjection\ContainerBuilder::IGNORE_ON_UNINITIALIZED_REFERENCE !== $value->getInvalidBehavior() && $this->container->hasDefinition($id = (string) $value)) {
            $definition = $this->container->getDefinition($id);
            if (!isset($this->resolvedIds[$id]) && (!$definition->isPublic() || $definition->isPrivate())) {
                $this->resolvedIds[$id] = \true;
                $this->processValue($definition, \true);
            }
            return $value;
        }
        if (!$value instanceof \ECSPrefix20210508\Symfony\Component\DependencyInjection\Definition) {
            return parent::processValue($value, $isRoot);
        }
        if ($value->hasTag($this->tagName) || $value->isDeprecated() || $value->hasErrors()) {
            return $value;
        }
        if ($isRoot) {
            $value->addTag(self::DO_PRELOAD_TAG);
        }
        return parent::processValue($value, $isRoot);
    }
}