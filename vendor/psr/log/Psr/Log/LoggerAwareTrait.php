<?php

namespace ECSPrefix20210508\Psr\Log;

/**
 * Basic Implementation of LoggerAwareInterface.
 */
trait LoggerAwareTrait
{
    /**
     * The logger instance.
     *
     * @var LoggerInterface|null
     */
    protected $logger;
    /**
     * Sets a logger.
     *
     * @param LoggerInterface $logger
     */
    public function setLogger(\ECSPrefix20210508\Psr\Log\LoggerInterface $logger)
    {
        $this->logger = $logger;
    }
}