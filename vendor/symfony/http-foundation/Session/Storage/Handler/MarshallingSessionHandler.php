<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20210508\Symfony\Component\HttpFoundation\Session\Storage\Handler;

use ECSPrefix20210508\Symfony\Component\Cache\Marshaller\MarshallerInterface;
/**
 * @author Ahmed TAILOULOUTE <ahmed.tailouloute@gmail.com>
 */
class MarshallingSessionHandler implements \SessionHandlerInterface, \SessionUpdateTimestampHandlerInterface
{
    private $handler;
    private $marshaller;
    public function __construct(\ECSPrefix20210508\Symfony\Component\HttpFoundation\Session\Storage\Handler\AbstractSessionHandler $handler, \ECSPrefix20210508\Symfony\Component\Cache\Marshaller\MarshallerInterface $marshaller)
    {
        $this->handler = $handler;
        $this->marshaller = $marshaller;
    }
    /**
     * @return bool
     */
    public function open($savePath, $name)
    {
        if (\is_object($savePath)) {
            $savePath = (string) $savePath;
        }
        return $this->handler->open($savePath, $name);
    }
    /**
     * @return bool
     */
    public function close()
    {
        return $this->handler->close();
    }
    /**
     * @return bool
     */
    public function destroy($sessionId)
    {
        if (\is_object($sessionId)) {
            $sessionId = (string) $sessionId;
        }
        return $this->handler->destroy($sessionId);
    }
    /**
     * @return bool
     */
    public function gc($maxlifetime)
    {
        return $this->handler->gc($maxlifetime);
    }
    /**
     * @return string
     */
    public function read($sessionId)
    {
        if (\is_object($sessionId)) {
            $sessionId = (string) $sessionId;
        }
        return $this->marshaller->unmarshall($this->handler->read($sessionId));
    }
    /**
     * @return bool
     */
    public function write($sessionId, $data)
    {
        if (\is_object($sessionId)) {
            $sessionId = (string) $sessionId;
        }
        $failed = [];
        $marshalledData = $this->marshaller->marshall(['data' => $data], $failed);
        if (isset($failed['data'])) {
            return \false;
        }
        return $this->handler->write($sessionId, $marshalledData['data']);
    }
    /**
     * @return bool
     */
    public function validateId($sessionId)
    {
        if (\is_object($sessionId)) {
            $sessionId = (string) $sessionId;
        }
        return $this->handler->validateId($sessionId);
    }
    /**
     * @return bool
     */
    public function updateTimestamp($sessionId, $data)
    {
        if (\is_object($sessionId)) {
            $sessionId = (string) $sessionId;
        }
        return $this->handler->updateTimestamp($sessionId, $data);
    }
}