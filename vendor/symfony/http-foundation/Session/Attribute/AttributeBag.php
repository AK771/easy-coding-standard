<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20210508\Symfony\Component\HttpFoundation\Session\Attribute;

/**
 * This class relates to session attribute storage.
 */
class AttributeBag implements \ECSPrefix20210508\Symfony\Component\HttpFoundation\Session\Attribute\AttributeBagInterface, \IteratorAggregate, \Countable
{
    private $name = 'attributes';
    private $storageKey;
    protected $attributes = [];
    /**
     * @param string $storageKey The key used to store attributes in the session
     */
    public function __construct($storageKey = '_sf2_attributes')
    {
        if (\is_object($storageKey)) {
            $storageKey = (string) $storageKey;
        }
        $this->storageKey = $storageKey;
    }
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }
    /**
     * @param string $name
     */
    public function setName($name)
    {
        if (\is_object($name)) {
            $name = (string) $name;
        }
        $this->name = $name;
    }
    /**
     * {@inheritdoc}
     */
    public function initialize(array &$attributes)
    {
        $this->attributes =& $attributes;
    }
    /**
     * {@inheritdoc}
     */
    public function getStorageKey()
    {
        return $this->storageKey;
    }
    /**
     * {@inheritdoc}
     * @param string $name
     */
    public function has($name)
    {
        if (\is_object($name)) {
            $name = (string) $name;
        }
        return \array_key_exists($name, $this->attributes);
    }
    /**
     * {@inheritdoc}
     * @param string $name
     */
    public function get($name, $default = null)
    {
        if (\is_object($name)) {
            $name = (string) $name;
        }
        return \array_key_exists($name, $this->attributes) ? $this->attributes[$name] : $default;
    }
    /**
     * {@inheritdoc}
     * @param string $name
     */
    public function set($name, $value)
    {
        if (\is_object($name)) {
            $name = (string) $name;
        }
        $this->attributes[$name] = $value;
    }
    /**
     * {@inheritdoc}
     */
    public function all()
    {
        return $this->attributes;
    }
    /**
     * {@inheritdoc}
     */
    public function replace(array $attributes)
    {
        $this->attributes = [];
        foreach ($attributes as $key => $value) {
            $this->set($key, $value);
        }
    }
    /**
     * {@inheritdoc}
     * @param string $name
     */
    public function remove($name)
    {
        if (\is_object($name)) {
            $name = (string) $name;
        }
        $retval = null;
        if (\array_key_exists($name, $this->attributes)) {
            $retval = $this->attributes[$name];
            unset($this->attributes[$name]);
        }
        return $retval;
    }
    /**
     * {@inheritdoc}
     */
    public function clear()
    {
        $return = $this->attributes;
        $this->attributes = [];
        return $return;
    }
    /**
     * Returns an iterator for attributes.
     *
     * @return \ArrayIterator An \ArrayIterator instance
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->attributes);
    }
    /**
     * Returns the number of attributes.
     *
     * @return int The number of attributes
     */
    public function count()
    {
        return \count($this->attributes);
    }
}