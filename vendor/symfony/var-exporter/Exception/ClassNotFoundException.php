<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20210508\Symfony\Component\VarExporter\Exception;

class ClassNotFoundException extends \Exception implements \ECSPrefix20210508\Symfony\Component\VarExporter\Exception\ExceptionInterface
{
    /**
     * @param string $class
     */
    public function __construct($class, \Throwable $previous = null)
    {
        if (\is_object($class)) {
            $class = (string) $class;
        }
        parent::__construct(\sprintf('Class "%s" not found.', $class), 0, $previous);
    }
}