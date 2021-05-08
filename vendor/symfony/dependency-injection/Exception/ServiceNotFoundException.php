<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20210508\Symfony\Component\DependencyInjection\Exception;

use ECSPrefix20210508\Psr\Container\NotFoundExceptionInterface;
/**
 * This exception is thrown when a non-existent service is requested.
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
class ServiceNotFoundException extends \ECSPrefix20210508\Symfony\Component\DependencyInjection\Exception\InvalidArgumentException implements \ECSPrefix20210508\Psr\Container\NotFoundExceptionInterface
{
    private $id;
    private $sourceId;
    private $alternatives;
    /**
     * @param string $id
     */
    public function __construct($id, string $sourceId = null, \Throwable $previous = null, array $alternatives = [], string $msg = null)
    {
        if (\is_object($id)) {
            $id = (string) $id;
        }
        if (null !== $msg) {
            // no-op
        } elseif (null === $sourceId) {
            $msg = \sprintf('You have requested a non-existent service "%s".', $id);
        } else {
            $msg = \sprintf('The service "%s" has a dependency on a non-existent service "%s".', $sourceId, $id);
        }
        if ($alternatives) {
            if (1 == \count($alternatives)) {
                $msg .= ' Did you mean this: "';
            } else {
                $msg .= ' Did you mean one of these: "';
            }
            $msg .= \implode('", "', $alternatives) . '"?';
        }
        parent::__construct($msg, 0, $previous);
        $this->id = $id;
        $this->sourceId = $sourceId;
        $this->alternatives = $alternatives;
    }
    public function getId()
    {
        return $this->id;
    }
    public function getSourceId()
    {
        return $this->sourceId;
    }
    public function getAlternatives()
    {
        return $this->alternatives;
    }
}