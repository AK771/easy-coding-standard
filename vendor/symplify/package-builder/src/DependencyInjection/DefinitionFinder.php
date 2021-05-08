<?php

namespace Symplify\PackageBuilder\DependencyInjection;

use ECSPrefix20210508\Symfony\Component\DependencyInjection\ContainerBuilder;
use ECSPrefix20210508\Symfony\Component\DependencyInjection\Definition;
use Symplify\PackageBuilder\Exception\DependencyInjection\DefinitionForTypeNotFoundException;
use Throwable;
/**
 * @see \Symplify\PackageBuilder\Tests\DependencyInjection\DefinitionFinderTest
 */
final class DefinitionFinder
{
    /**
     * @return Definition[]
     * @param string $type
     */
    public function findAllByType(\ECSPrefix20210508\Symfony\Component\DependencyInjection\ContainerBuilder $containerBuilder, $type) : array
    {
        if (\is_object($type)) {
            $type = (string) $type;
        }
        $definitions = [];
        $containerBuilderDefinitions = $containerBuilder->getDefinitions();
        foreach ($containerBuilderDefinitions as $name => $definition) {
            $class = $definition->getClass() ?: $name;
            if (!$this->doesClassExists($class)) {
                continue;
            }
            if (\is_a($class, $type, \true)) {
                $definitions[$name] = $definition;
            }
        }
        return $definitions;
    }
    /**
     * @param string $type
     */
    public function getByType(\ECSPrefix20210508\Symfony\Component\DependencyInjection\ContainerBuilder $containerBuilder, $type) : \ECSPrefix20210508\Symfony\Component\DependencyInjection\Definition
    {
        if (\is_object($type)) {
            $type = (string) $type;
        }
        $definition = $this->getByTypeIfExists($containerBuilder, $type);
        if ($definition !== null) {
            return $definition;
        }
        throw new \Symplify\PackageBuilder\Exception\DependencyInjection\DefinitionForTypeNotFoundException(\sprintf('Definition for type "%s" was not found.', $type));
    }
    /**
     * @return \Symfony\Component\DependencyInjection\Definition|null
     * @param string $type
     */
    private function getByTypeIfExists(\ECSPrefix20210508\Symfony\Component\DependencyInjection\ContainerBuilder $containerBuilder, $type)
    {
        if (\is_object($type)) {
            $type = (string) $type;
        }
        $containerBuilderDefinitions = $containerBuilder->getDefinitions();
        foreach ($containerBuilderDefinitions as $name => $definition) {
            $class = $definition->getClass() ?: $name;
            if (!$this->doesClassExists($class)) {
                continue;
            }
            if (\is_a($class, $type, \true)) {
                return $definition;
            }
        }
        return null;
    }
    /**
     * @param string $class
     */
    private function doesClassExists($class) : bool
    {
        if (\is_object($class)) {
            $class = (string) $class;
        }
        try {
            return \class_exists($class);
        } catch (\Throwable $throwable) {
            return \false;
        }
    }
}