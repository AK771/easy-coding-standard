<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20210508\Symfony\Component\DependencyInjection;

use ECSPrefix20210508\Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use ECSPrefix20210508\Symfony\Component\DependencyInjection\Argument\ServiceLocator as ArgumentServiceLocator;
use ECSPrefix20210508\Symfony\Component\DependencyInjection\Exception\EnvNotFoundException;
use ECSPrefix20210508\Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use ECSPrefix20210508\Symfony\Component\DependencyInjection\Exception\ParameterCircularReferenceException;
use ECSPrefix20210508\Symfony\Component\DependencyInjection\Exception\RuntimeException;
use ECSPrefix20210508\Symfony\Component\DependencyInjection\Exception\ServiceCircularReferenceException;
use ECSPrefix20210508\Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;
use ECSPrefix20210508\Symfony\Component\DependencyInjection\ParameterBag\EnvPlaceholderParameterBag;
use ECSPrefix20210508\Symfony\Component\DependencyInjection\ParameterBag\FrozenParameterBag;
use ECSPrefix20210508\Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use ECSPrefix20210508\Symfony\Contracts\Service\ResetInterface;
// Help opcache.preload discover always-needed symbols
\class_exists(\ECSPrefix20210508\Symfony\Component\DependencyInjection\Argument\RewindableGenerator::class);
\class_exists(\ECSPrefix20210508\Symfony\Component\DependencyInjection\Argument\ServiceLocator::class);
/**
 * Container is a dependency injection container.
 *
 * It gives access to object instances (services).
 * Services and parameters are simple key/pair stores.
 * The container can have four possible behaviors when a service
 * does not exist (or is not initialized for the last case):
 *
 *  * EXCEPTION_ON_INVALID_REFERENCE: Throws an exception (the default)
 *  * NULL_ON_INVALID_REFERENCE:      Returns null
 *  * IGNORE_ON_INVALID_REFERENCE:    Ignores the wrapping command asking for the reference
 *                                    (for instance, ignore a setter if the service does not exist)
 *  * IGNORE_ON_UNINITIALIZED_REFERENCE: Ignores/returns null for uninitialized services or invalid references
 *
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
class Container implements \ECSPrefix20210508\Symfony\Component\DependencyInjection\ContainerInterface, \ECSPrefix20210508\Symfony\Contracts\Service\ResetInterface
{
    protected $parameterBag;
    protected $services = [];
    protected $privates = [];
    protected $fileMap = [];
    protected $methodMap = [];
    protected $factories = [];
    protected $aliases = [];
    protected $loading = [];
    protected $resolving = [];
    protected $syntheticIds = [];
    private $envCache = [];
    private $compiled = \false;
    private $getEnv;
    public function __construct(\ECSPrefix20210508\Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface $parameterBag = null)
    {
        $this->parameterBag = isset($parameterBag) ? $parameterBag : new \ECSPrefix20210508\Symfony\Component\DependencyInjection\ParameterBag\EnvPlaceholderParameterBag();
    }
    /**
     * Compiles the container.
     *
     * This method does two things:
     *
     *  * Parameter values are resolved;
     *  * The parameter bag is frozen.
     */
    public function compile()
    {
        $this->parameterBag->resolve();
        $this->parameterBag = new \ECSPrefix20210508\Symfony\Component\DependencyInjection\ParameterBag\FrozenParameterBag($this->parameterBag->all());
        $this->compiled = \true;
    }
    /**
     * Returns true if the container is compiled.
     *
     * @return bool
     */
    public function isCompiled()
    {
        return $this->compiled;
    }
    /**
     * Gets the service container parameter bag.
     *
     * @return ParameterBagInterface A ParameterBagInterface instance
     */
    public function getParameterBag()
    {
        return $this->parameterBag;
    }
    /**
     * Gets a parameter.
     *
     * @param string $name The parameter name
     *
     * @return array|bool|float|int|string|null The parameter value
     *
     * @throws InvalidArgumentException if the parameter is not defined
     */
    public function getParameter($name)
    {
        if (\is_object($name)) {
            $name = (string) $name;
        }
        return $this->parameterBag->get($name);
    }
    /**
     * Checks if a parameter exists.
     *
     * @param string $name The parameter name
     *
     * @return bool The presence of parameter in container
     */
    public function hasParameter($name)
    {
        if (\is_object($name)) {
            $name = (string) $name;
        }
        return $this->parameterBag->has($name);
    }
    /**
     * Sets a parameter.
     *
     * @param string $name  The parameter name
     * @param mixed  $value The parameter value
     */
    public function setParameter($name, $value)
    {
        if (\is_object($name)) {
            $name = (string) $name;
        }
        $this->parameterBag->set($name, $value);
    }
    /**
     * Sets a service.
     *
     * Setting a synthetic service to null resets it: has() returns false and get()
     * behaves in the same way as if the service was never created.
     * @param object|null $service
     * @param string $id
     */
    public function set($id, $service)
    {
        if (\is_object($id)) {
            $id = (string) $id;
        }
        // Runs the internal initializer; used by the dumped container to include always-needed files
        if (isset($this->privates['service_container']) && $this->privates['service_container'] instanceof \Closure) {
            $initialize = $this->privates['service_container'];
            unset($this->privates['service_container']);
            $initialize();
        }
        if ('service_container' === $id) {
            throw new \ECSPrefix20210508\Symfony\Component\DependencyInjection\Exception\InvalidArgumentException('You cannot set service "service_container".');
        }
        if (!(isset($this->fileMap[$id]) || isset($this->methodMap[$id]))) {
            if (isset($this->syntheticIds[$id]) || !isset($this->getRemovedIds()[$id])) {
                // no-op
            } elseif (null === $service) {
                throw new \ECSPrefix20210508\Symfony\Component\DependencyInjection\Exception\InvalidArgumentException(\sprintf('The "%s" service is private, you cannot unset it.', $id));
            } else {
                throw new \ECSPrefix20210508\Symfony\Component\DependencyInjection\Exception\InvalidArgumentException(\sprintf('The "%s" service is private, you cannot replace it.', $id));
            }
        } elseif (isset($this->services[$id])) {
            throw new \ECSPrefix20210508\Symfony\Component\DependencyInjection\Exception\InvalidArgumentException(\sprintf('The "%s" service is already initialized, you cannot replace it.', $id));
        }
        if (isset($this->aliases[$id])) {
            unset($this->aliases[$id]);
        }
        if (null === $service) {
            unset($this->services[$id]);
            return;
        }
        $this->services[$id] = $service;
    }
    /**
     * Returns true if the given service is defined.
     *
     * @param string $id The service identifier
     *
     * @return bool true if the service is defined, false otherwise
     */
    public function has($id)
    {
        if (\is_object($id)) {
            $id = (string) $id;
        }
        if (isset($this->aliases[$id])) {
            $id = $this->aliases[$id];
        }
        if (isset($this->services[$id])) {
            return \true;
        }
        if ('service_container' === $id) {
            return \true;
        }
        return isset($this->fileMap[$id]) || isset($this->methodMap[$id]);
    }
    /**
     * Gets a service.
     *
     * @param string $id              The service identifier
     * @param int    $invalidBehavior The behavior when the service does not exist
     *
     * @return object|null The associated service
     *
     * @throws ServiceCircularReferenceException When a circular reference is detected
     * @throws ServiceNotFoundException          When the service is not defined
     * @throws \Exception                        if an exception has been thrown when the service has been resolved
     *
     * @see Reference
     */
    public function get($id, int $invalidBehavior = 1)
    {
        if (\is_object($id)) {
            $id = (string) $id;
        }
        return isset($this->services[$id]) ? $this->services[$id] : (isset($this->services[$id = isset($this->aliases[$id]) ? $this->aliases[$id] : $id]) ? $this->services[$id = isset($this->aliases[$id]) ? $this->aliases[$id] : $id] : ('service_container' === $id ? $this : (isset($this->factories[$id]) ? $this->factories[$id] : [$this, 'make'])($id, $invalidBehavior)));
    }
    /**
     * Creates a service.
     *
     * As a separate method to allow "get()" to use the really fast `??` operator.
     * @param string $id
     */
    private function make($id, int $invalidBehavior)
    {
        if (\is_object($id)) {
            $id = (string) $id;
        }
        if (isset($this->loading[$id])) {
            throw new \ECSPrefix20210508\Symfony\Component\DependencyInjection\Exception\ServiceCircularReferenceException($id, \array_merge(\array_keys($this->loading), [$id]));
        }
        $this->loading[$id] = \true;
        try {
            if (isset($this->fileMap[$id])) {
                return 4 === $invalidBehavior ? null : $this->load($this->fileMap[$id]);
            } elseif (isset($this->methodMap[$id])) {
                return 4 === $invalidBehavior ? null : $this->{$this->methodMap[$id]}();
            }
        } catch (\Exception $e) {
            unset($this->services[$id]);
            throw $e;
        } finally {
            unset($this->loading[$id]);
        }
        if (1 === $invalidBehavior) {
            if (!$id) {
                throw new \ECSPrefix20210508\Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException($id);
            }
            if (isset($this->syntheticIds[$id])) {
                throw new \ECSPrefix20210508\Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException($id, null, null, [], \sprintf('The "%s" service is synthetic, it needs to be set at boot time before it can be used.', $id));
            }
            if (isset($this->getRemovedIds()[$id])) {
                throw new \ECSPrefix20210508\Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException($id, null, null, [], \sprintf('The "%s" service or alias has been removed or inlined when the container was compiled. You should either make it public, or stop using the container directly and use dependency injection instead.', $id));
            }
            $alternatives = [];
            foreach ($this->getServiceIds() as $knownId) {
                if ('' === $knownId || '.' === $knownId[0]) {
                    continue;
                }
                $lev = \levenshtein($id, $knownId);
                if ($lev <= \strlen($id) / 3 || \false !== \strpos($knownId, $id)) {
                    $alternatives[] = $knownId;
                }
            }
            throw new \ECSPrefix20210508\Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException($id, null, null, $alternatives);
        }
        return null;
    }
    /**
     * Returns true if the given service has actually been initialized.
     *
     * @param string $id The service identifier
     *
     * @return bool true if service has already been initialized, false otherwise
     */
    public function initialized($id)
    {
        if (\is_object($id)) {
            $id = (string) $id;
        }
        if (isset($this->aliases[$id])) {
            $id = $this->aliases[$id];
        }
        if ('service_container' === $id) {
            return \false;
        }
        return isset($this->services[$id]);
    }
    /**
     * {@inheritdoc}
     */
    public function reset()
    {
        $services = $this->services + $this->privates;
        $this->services = $this->factories = $this->privates = [];
        foreach ($services as $service) {
            try {
                if ($service instanceof \ECSPrefix20210508\Symfony\Contracts\Service\ResetInterface) {
                    $service->reset();
                }
            } catch (\Throwable $e) {
                continue;
            }
        }
    }
    /**
     * Gets all service ids.
     *
     * @return string[] An array of all defined service ids
     */
    public function getServiceIds()
    {
        return \array_map('strval', \array_unique(\array_merge(['service_container'], \array_keys($this->fileMap), \array_keys($this->methodMap), \array_keys($this->aliases), \array_keys($this->services))));
    }
    /**
     * Gets service ids that existed at compile time.
     *
     * @return array
     */
    public function getRemovedIds()
    {
        return [];
    }
    /**
     * Camelizes a string.
     *
     * @param string $id A string to camelize
     *
     * @return string The camelized string
     */
    public static function camelize($id)
    {
        if (\is_object($id)) {
            $id = (string) $id;
        }
        return \strtr(\ucwords(\strtr($id, ['_' => ' ', '.' => '_ ', '\\' => '_ '])), [' ' => '']);
    }
    /**
     * A string to underscore.
     *
     * @param string $id The string to underscore
     *
     * @return string The underscored string
     */
    public static function underscore($id)
    {
        if (\is_object($id)) {
            $id = (string) $id;
        }
        return \strtolower(\preg_replace(['/([A-Z]+)([A-Z][a-z])/', '/([a-z\\d])([A-Z])/'], ['\\1_\\2', '\\1_\\2'], \str_replace('_', '.', $id)));
    }
    /**
     * Creates a service by requiring its factory file.
     */
    protected function load($file)
    {
        return require $file;
    }
    /**
     * Fetches a variable from the environment.
     *
     * @param string $name The name of the environment variable
     *
     * @return mixed The value to use for the provided environment variable name
     *
     * @throws EnvNotFoundException When the environment variable is not found and has no default value
     */
    protected function getEnv($name)
    {
        if (\is_object($name)) {
            $name = (string) $name;
        }
        if (isset($this->resolving[$envName = "env({$name})"])) {
            throw new \ECSPrefix20210508\Symfony\Component\DependencyInjection\Exception\ParameterCircularReferenceException(\array_keys($this->resolving));
        }
        if (isset($this->envCache[$name]) || \array_key_exists($name, $this->envCache)) {
            return $this->envCache[$name];
        }
        if (!$this->has($id = 'container.env_var_processors_locator')) {
            $this->set($id, new \ECSPrefix20210508\Symfony\Component\DependencyInjection\ServiceLocator([]));
        }
        if (!$this->getEnv) {
            $this->getEnv = new \ReflectionMethod($this, __FUNCTION__);
            $this->getEnv->setAccessible(\true);
            $this->getEnv = $this->getEnv->getClosure($this);
        }
        $processors = $this->get($id);
        if (\false !== ($i = \strpos($name, ':'))) {
            $prefix = \substr($name, 0, $i);
            $localName = \substr($name, 1 + $i);
        } else {
            $prefix = 'string';
            $localName = $name;
        }
        $processor = $processors->has($prefix) ? $processors->get($prefix) : new \ECSPrefix20210508\Symfony\Component\DependencyInjection\EnvVarProcessor($this);
        $this->resolving[$envName] = \true;
        try {
            return $this->envCache[$name] = $processor->getEnv($prefix, $localName, $this->getEnv);
        } finally {
            unset($this->resolving[$envName]);
        }
    }
    /**
     * @param string|false $registry
     * @param string|bool  $load
     *
     * @return mixed
     *
     * @internal
     * @param string|null $method
     * @param string $id
     */
    protected final function getService($registry, $id, $method, $load)
    {
        if (\is_object($id)) {
            $id = (string) $id;
        }
        if ('service_container' === $id) {
            return $this;
        }
        if (\is_string($load)) {
            throw new \ECSPrefix20210508\Symfony\Component\DependencyInjection\Exception\RuntimeException($load);
        }
        if (null === $method) {
            return \false !== $registry ? isset($this->{$registry}[$id]) ? $this->{$registry}[$id] : null : null;
        }
        if (\false !== $registry) {
            return isset($this->{$registry}[$id]) ? $this->{$registry}[$id] : ($this->{$registry}[$id] = $load ? $this->load($method) : $this->{$method}());
        }
        if (!$load) {
            return $this->{$method}();
        }
        return ($factory = isset($this->factories[$id]) ? $this->factories[$id] : (isset($this->factories['service_container'][$id]) ? $this->factories['service_container'][$id] : null)) ? $factory() : $this->load($method);
    }
    private function __clone()
    {
    }
}