<?php

namespace ECSPrefix20210508\Nette;

/**
 * Static class.
 */
trait StaticClass
{
    /** @throws \Error */
    public final function __construct()
    {
        throw new \Error('Class ' . static::class . ' is static and cannot be instantiated.');
    }
    /**
     * Call to undefined static method.
     * @return void
     * @throws MemberAccessException
     * @param string $name
     */
    public static function __callStatic($name, array $args)
    {
        if (\is_object($name)) {
            $name = (string) $name;
        }
        \ECSPrefix20210508\Nette\Utils\ObjectHelpers::strictStaticCall(static::class, $name);
    }
}