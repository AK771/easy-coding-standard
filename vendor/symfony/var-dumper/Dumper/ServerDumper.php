<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20210508\Symfony\Component\VarDumper\Dumper;

use ECSPrefix20210508\Symfony\Component\VarDumper\Cloner\Data;
use ECSPrefix20210508\Symfony\Component\VarDumper\Dumper\ContextProvider\ContextProviderInterface;
use ECSPrefix20210508\Symfony\Component\VarDumper\Server\Connection;
/**
 * ServerDumper forwards serialized Data clones to a server.
 *
 * @author Maxime Steinhausser <maxime.steinhausser@gmail.com>
 */
class ServerDumper implements \ECSPrefix20210508\Symfony\Component\VarDumper\Dumper\DataDumperInterface
{
    private $connection;
    private $wrappedDumper;
    /**
     * @param string                     $host             The server host
     * @param DataDumperInterface|null   $wrappedDumper    A wrapped instance used whenever we failed contacting the server
     * @param ContextProviderInterface[] $contextProviders Context providers indexed by context name
     */
    public function __construct($host, \ECSPrefix20210508\Symfony\Component\VarDumper\Dumper\DataDumperInterface $wrappedDumper = null, array $contextProviders = [])
    {
        if (\is_object($host)) {
            $host = (string) $host;
        }
        $this->connection = new \ECSPrefix20210508\Symfony\Component\VarDumper\Server\Connection($host, $contextProviders);
        $this->wrappedDumper = $wrappedDumper;
    }
    /**
     * @return mixed[]
     */
    public function getContextProviders()
    {
        return $this->connection->getContextProviders();
    }
    /**
     * {@inheritdoc}
     */
    public function dump(\ECSPrefix20210508\Symfony\Component\VarDumper\Cloner\Data $data)
    {
        if (!$this->connection->write($data) && $this->wrappedDumper) {
            $this->wrappedDumper->dump($data);
        }
    }
}