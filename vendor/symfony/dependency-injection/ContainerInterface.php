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

use ECSPrefix20210508\Psr\Container\ContainerInterface as PsrContainerInterface;
use ECSPrefix20210508\Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use ECSPrefix20210508\Symfony\Component\DependencyInjection\Exception\ServiceCircularReferenceException;
use ECSPrefix20210508\Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;
/**
 * ContainerInterface is the interface implemented by service container classes.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
interface ContainerInterface extends \ECSPrefix20210508\Psr\Container\ContainerInterface
{
    const RUNTIME_EXCEPTION_ON_INVALID_REFERENCE = 0;
    const EXCEPTION_ON_INVALID_REFERENCE = 1;
    const NULL_ON_INVALID_REFERENCE = 2;
    const IGNORE_ON_INVALID_REFERENCE = 3;
    const IGNORE_ON_UNINITIALIZED_REFERENCE = 4;
    /**
     * Sets a service.
     * @param object|null $service
     * @param string $id
     */
    public function set($id, $service);
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
     *
     * @see Reference
     */
    public function get($id, int $invalidBehavior = self::EXCEPTION_ON_INVALID_REFERENCE);
    /**
     * Returns true if the given service is defined.
     *
     * @param string $id The service identifier
     *
     * @return bool true if the service is defined, false otherwise
     */
    public function has($id);
    /**
     * Check for whether or not a service has been initialized.
     *
     * @return bool true if the service has been initialized, false otherwise
     * @param string $id
     */
    public function initialized($id);
    /**
     * Gets a parameter.
     *
     * @param string $name The parameter name
     *
     * @return array|bool|float|int|string|null The parameter value
     *
     * @throws InvalidArgumentException if the parameter is not defined
     */
    public function getParameter($name);
    /**
     * Checks if a parameter exists.
     *
     * @param string $name The parameter name
     *
     * @return bool The presence of parameter in container
     */
    public function hasParameter($name);
    /**
     * Sets a parameter.
     *
     * @param string $name  The parameter name
     * @param mixed  $value The parameter value
     */
    public function setParameter($name, $value);
}