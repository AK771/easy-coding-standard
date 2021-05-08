<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20210508\Symfony\Component\DependencyInjection\Loader\Configurator;

use ECSPrefix20210508\Symfony\Component\DependencyInjection\Alias;
use ECSPrefix20210508\Symfony\Component\DependencyInjection\ChildDefinition;
use ECSPrefix20210508\Symfony\Component\DependencyInjection\ContainerBuilder;
use ECSPrefix20210508\Symfony\Component\DependencyInjection\Definition;
use ECSPrefix20210508\Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use ECSPrefix20210508\Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;
use ECSPrefix20210508\Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
/**
 * @author Nicolas Grekas <p@tchwork.com>
 */
class ServicesConfigurator extends \ECSPrefix20210508\Symfony\Component\DependencyInjection\Loader\Configurator\AbstractConfigurator
{
    const FACTORY = 'services';
    private $defaults;
    private $container;
    private $loader;
    private $instanceof;
    private $path;
    private $anonymousHash;
    private $anonymousCount;
    /**
     * @param string $path
     * @param int $anonymousCount
     */
    public function __construct(\ECSPrefix20210508\Symfony\Component\DependencyInjection\ContainerBuilder $container, \ECSPrefix20210508\Symfony\Component\DependencyInjection\Loader\PhpFileLoader $loader, array &$instanceof, $path = null, &$anonymousCount = 0)
    {
        $this->defaults = new \ECSPrefix20210508\Symfony\Component\DependencyInjection\Definition();
        $this->container = $container;
        $this->loader = $loader;
        $this->instanceof =& $instanceof;
        $this->path = $path;
        $this->anonymousHash = \ECSPrefix20210508\Symfony\Component\DependencyInjection\ContainerBuilder::hash($path ?: \mt_rand());
        $this->anonymousCount =& $anonymousCount;
        $instanceof = [];
    }
    /**
     * Defines a set of defaults for following service definitions.
     * @return \Symfony\Component\DependencyInjection\Loader\Configurator\DefaultsConfigurator
     */
    public final function defaults()
    {
        return new \ECSPrefix20210508\Symfony\Component\DependencyInjection\Loader\Configurator\DefaultsConfigurator($this, $this->defaults = new \ECSPrefix20210508\Symfony\Component\DependencyInjection\Definition(), $this->path);
    }
    /**
     * Defines an instanceof-conditional to be applied to following service definitions.
     * @param string $fqcn
     */
    public final function instanceof($fqcn) : \ECSPrefix20210508\Symfony\Component\DependencyInjection\Loader\Configurator\InstanceofConfigurator
    {
        if (\is_object($fqcn)) {
            $fqcn = (string) $fqcn;
        }
        $this->instanceof[$fqcn] = $definition = new \ECSPrefix20210508\Symfony\Component\DependencyInjection\ChildDefinition('');
        return new \ECSPrefix20210508\Symfony\Component\DependencyInjection\Loader\Configurator\InstanceofConfigurator($this, $definition, $fqcn, $this->path);
    }
    /**
     * Registers a service.
     *
     * @param string|null $id    The service id, or null to create an anonymous service
     * @param string $class The class of the service, or null when $id is also the class name
     * @return \Symfony\Component\DependencyInjection\Loader\Configurator\ServiceConfigurator
     */
    public final function set($id, $class = null)
    {
        $defaults = $this->defaults;
        $definition = new \ECSPrefix20210508\Symfony\Component\DependencyInjection\Definition();
        if (null === $id) {
            if (!$class) {
                throw new \LogicException('Anonymous services must have a class name.');
            }
            $id = \sprintf('.%d_%s', ++$this->anonymousCount, \preg_replace('/^.*\\\\/', '', $class) . '~' . $this->anonymousHash);
        } elseif (!$defaults->isPublic() || !$defaults->isPrivate()) {
            $definition->setPublic($defaults->isPublic() && !$defaults->isPrivate());
        }
        $definition->setAutowired($defaults->isAutowired());
        $definition->setAutoconfigured($defaults->isAutoconfigured());
        // deep clone, to avoid multiple process of the same instance in the passes
        $definition->setBindings(\unserialize(\serialize($defaults->getBindings())));
        $definition->setChanges([]);
        $configurator = new \ECSPrefix20210508\Symfony\Component\DependencyInjection\Loader\Configurator\ServiceConfigurator($this->container, $this->instanceof, \true, $this, $definition, $id, $defaults->getTags(), $this->path);
        return null !== $class ? $configurator->class($class) : $configurator;
    }
    /**
     * Creates an alias.
     * @param string $id
     */
    public final function alias($id, string $referencedId) : \ECSPrefix20210508\Symfony\Component\DependencyInjection\Loader\Configurator\AliasConfigurator
    {
        if (\is_object($id)) {
            $id = (string) $id;
        }
        $ref = static::processValue($referencedId, \true);
        $alias = new \ECSPrefix20210508\Symfony\Component\DependencyInjection\Alias((string) $ref);
        if (!$this->defaults->isPublic() || !$this->defaults->isPrivate()) {
            $alias->setPublic($this->defaults->isPublic());
        }
        $this->container->setAlias($id, $alias);
        return new \ECSPrefix20210508\Symfony\Component\DependencyInjection\Loader\Configurator\AliasConfigurator($this, $alias);
    }
    /**
     * Registers a PSR-4 namespace using a glob pattern.
     * @param string $namespace
     */
    public final function load($namespace, string $resource) : \ECSPrefix20210508\Symfony\Component\DependencyInjection\Loader\Configurator\PrototypeConfigurator
    {
        if (\is_object($namespace)) {
            $namespace = (string) $namespace;
        }
        return new \ECSPrefix20210508\Symfony\Component\DependencyInjection\Loader\Configurator\PrototypeConfigurator($this, $this->loader, $this->defaults, $namespace, $resource, \true);
    }
    /**
     * Gets an already defined service definition.
     *
     * @throws ServiceNotFoundException if the service definition does not exist
     * @param string $id
     */
    public final function get($id) : \ECSPrefix20210508\Symfony\Component\DependencyInjection\Loader\Configurator\ServiceConfigurator
    {
        if (\is_object($id)) {
            $id = (string) $id;
        }
        $definition = $this->container->getDefinition($id);
        return new \ECSPrefix20210508\Symfony\Component\DependencyInjection\Loader\Configurator\ServiceConfigurator($this->container, $definition->getInstanceofConditionals(), \true, $this, $definition, $id, []);
    }
    /**
     * Registers a stack of decorator services.
     *
     * @param InlineServiceConfigurator[]|ReferenceConfigurator[] $services
     * @param string $id
     */
    public final function stack($id, array $services) : \ECSPrefix20210508\Symfony\Component\DependencyInjection\Loader\Configurator\AliasConfigurator
    {
        if (\is_object($id)) {
            $id = (string) $id;
        }
        foreach ($services as $i => $service) {
            if ($service instanceof \ECSPrefix20210508\Symfony\Component\DependencyInjection\Loader\Configurator\InlineServiceConfigurator) {
                $definition = $service->definition->setInstanceofConditionals($this->instanceof);
                $changes = $definition->getChanges();
                $definition->setAutowired((isset($changes['autowired']) ? $definition : $this->defaults)->isAutowired());
                $definition->setAutoconfigured((isset($changes['autoconfigured']) ? $definition : $this->defaults)->isAutoconfigured());
                $definition->setBindings(\array_merge($this->defaults->getBindings(), $definition->getBindings()));
                $definition->setChanges($changes);
                $services[$i] = $definition;
            } elseif (!$service instanceof \ECSPrefix20210508\Symfony\Component\DependencyInjection\Loader\Configurator\ReferenceConfigurator) {
                throw new \ECSPrefix20210508\Symfony\Component\DependencyInjection\Exception\InvalidArgumentException(\sprintf('"%s()" expects a list of definitions as returned by "%s()" or "%s()", "%s" given at index "%s" for service "%s".', __METHOD__, \ECSPrefix20210508\Symfony\Component\DependencyInjection\Loader\Configurator\InlineServiceConfigurator::FACTORY, \ECSPrefix20210508\Symfony\Component\DependencyInjection\Loader\Configurator\ReferenceConfigurator::FACTORY, $service instanceof \ECSPrefix20210508\Symfony\Component\DependencyInjection\Loader\Configurator\AbstractConfigurator ? $service::FACTORY . '()' : \get_debug_type($service), $i, $id));
            }
        }
        $alias = $this->alias($id, '');
        $alias->definition = $this->set($id)->parent('')->args($services)->tag('container.stack')->definition;
        return $alias;
    }
    /**
     * Registers a service.
     * @param string $id
     */
    public final function __invoke($id, string $class = null) : \ECSPrefix20210508\Symfony\Component\DependencyInjection\Loader\Configurator\ServiceConfigurator
    {
        if (\is_object($id)) {
            $id = (string) $id;
        }
        return $this->set($id, $class);
    }
    public function __destruct()
    {
        $this->loader->registerAliasesForSinglyImplementedInterfaces();
    }
}