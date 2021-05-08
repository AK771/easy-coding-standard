<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20210508\Symfony\Component\HttpFoundation;

/**
 * HeaderBag is a container for HTTP headers.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class HeaderBag implements \IteratorAggregate, \Countable
{
    const UPPER = '_ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    const LOWER = '-abcdefghijklmnopqrstuvwxyz';
    protected $headers = [];
    protected $cacheControl = [];
    public function __construct(array $headers = [])
    {
        foreach ($headers as $key => $values) {
            $this->set($key, $values);
        }
    }
    /**
     * Returns the headers as a string.
     *
     * @return string The headers
     */
    public function __toString()
    {
        if (!($headers = $this->all())) {
            return '';
        }
        \ksort($headers);
        $max = \max(\array_map('strlen', \array_keys($headers))) + 1;
        $content = '';
        foreach ($headers as $name => $values) {
            $name = \ucwords($name, '-');
            foreach ($values as $value) {
                $content .= \sprintf("%-{$max}s %s\r\n", $name . ':', $value);
            }
        }
        return $content;
    }
    /**
     * Returns the headers.
     *
     * @param string $key The name of the headers to return or null to get them all
     *
     * @return array An array of headers
     */
    public function all($key = null)
    {
        if (null !== $key) {
            return isset($this->headers[\strtr($key, self::UPPER, self::LOWER)]) ? $this->headers[\strtr($key, self::UPPER, self::LOWER)] : [];
        }
        return $this->headers;
    }
    /**
     * Returns the parameter keys.
     *
     * @return array An array of parameter keys
     */
    public function keys()
    {
        return \array_keys($this->all());
    }
    /**
     * Replaces the current HTTP headers by a new set.
     */
    public function replace(array $headers = [])
    {
        $this->headers = [];
        $this->add($headers);
    }
    /**
     * Adds new headers the current HTTP headers set.
     */
    public function add(array $headers)
    {
        foreach ($headers as $key => $values) {
            $this->set($key, $values);
        }
    }
    /**
     * Returns a header value by name.
     *
     * @return string|null The first header value or default value
     * @param string $key
     */
    public function get($key, string $default = null)
    {
        if (\is_object($key)) {
            $key = (string) $key;
        }
        $headers = $this->all($key);
        if (!$headers) {
            return $default;
        }
        if (null === $headers[0]) {
            return null;
        }
        return (string) $headers[0];
    }
    /**
     * Sets a header by name.
     *
     * @param string|string[] $values  The value or an array of values
     * @param bool            $replace Whether to replace the actual value or not (true by default)
     * @param string $key
     */
    public function set($key, $values, bool $replace = \true)
    {
        if (\is_object($key)) {
            $key = (string) $key;
        }
        $key = \strtr($key, self::UPPER, self::LOWER);
        if (\is_array($values)) {
            $values = \array_values($values);
            if (\true === $replace || !isset($this->headers[$key])) {
                $this->headers[$key] = $values;
            } else {
                $this->headers[$key] = \array_merge($this->headers[$key], $values);
            }
        } else {
            if (\true === $replace || !isset($this->headers[$key])) {
                $this->headers[$key] = [$values];
            } else {
                $this->headers[$key][] = $values;
            }
        }
        if ('cache-control' === $key) {
            $this->cacheControl = $this->parseCacheControl(\implode(', ', $this->headers[$key]));
        }
    }
    /**
     * Returns true if the HTTP header is defined.
     *
     * @return bool true if the parameter exists, false otherwise
     * @param string $key
     */
    public function has($key)
    {
        if (\is_object($key)) {
            $key = (string) $key;
        }
        return \array_key_exists(\strtr($key, self::UPPER, self::LOWER), $this->all());
    }
    /**
     * Returns true if the given HTTP header contains the given value.
     *
     * @return bool true if the value is contained in the header, false otherwise
     * @param string $key
     */
    public function contains($key, string $value)
    {
        if (\is_object($key)) {
            $key = (string) $key;
        }
        return \in_array($value, $this->all($key));
    }
    /**
     * Removes a header.
     * @param string $key
     */
    public function remove($key)
    {
        if (\is_object($key)) {
            $key = (string) $key;
        }
        $key = \strtr($key, self::UPPER, self::LOWER);
        unset($this->headers[$key]);
        if ('cache-control' === $key) {
            $this->cacheControl = [];
        }
    }
    /**
     * Returns the HTTP header value converted to a date.
     *
     * @return \DateTimeInterface|null The parsed DateTime or the default value if the header does not exist
     *
     * @throws \RuntimeException When the HTTP header is not parseable
     * @param string $key
     */
    public function getDate($key, \DateTime $default = null)
    {
        if (\is_object($key)) {
            $key = (string) $key;
        }
        if (null === ($value = $this->get($key))) {
            return $default;
        }
        if (\false === ($date = \DateTime::createFromFormat(\DATE_RFC2822, $value))) {
            throw new \RuntimeException(\sprintf('The "%s" HTTP header is not parseable (%s).', $key, $value));
        }
        return $date;
    }
    /**
     * Adds a custom Cache-Control directive.
     *
     * @param mixed $value The Cache-Control directive value
     * @param string $key
     */
    public function addCacheControlDirective($key, $value = \true)
    {
        if (\is_object($key)) {
            $key = (string) $key;
        }
        $this->cacheControl[$key] = $value;
        $this->set('Cache-Control', $this->getCacheControlHeader());
    }
    /**
     * Returns true if the Cache-Control directive is defined.
     *
     * @return bool true if the directive exists, false otherwise
     * @param string $key
     */
    public function hasCacheControlDirective($key)
    {
        if (\is_object($key)) {
            $key = (string) $key;
        }
        return \array_key_exists($key, $this->cacheControl);
    }
    /**
     * Returns a Cache-Control directive value by name.
     *
     * @return mixed The directive value if defined, null otherwise
     * @param string $key
     */
    public function getCacheControlDirective($key)
    {
        if (\is_object($key)) {
            $key = (string) $key;
        }
        return \array_key_exists($key, $this->cacheControl) ? $this->cacheControl[$key] : null;
    }
    /**
     * Removes a Cache-Control directive.
     * @param string $key
     */
    public function removeCacheControlDirective($key)
    {
        if (\is_object($key)) {
            $key = (string) $key;
        }
        unset($this->cacheControl[$key]);
        $this->set('Cache-Control', $this->getCacheControlHeader());
    }
    /**
     * Returns an iterator for headers.
     *
     * @return \ArrayIterator An \ArrayIterator instance
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->headers);
    }
    /**
     * Returns the number of headers.
     *
     * @return int The number of headers
     */
    public function count()
    {
        return \count($this->headers);
    }
    protected function getCacheControlHeader()
    {
        \ksort($this->cacheControl);
        return \ECSPrefix20210508\Symfony\Component\HttpFoundation\HeaderUtils::toString($this->cacheControl, ',');
    }
    /**
     * Parses a Cache-Control HTTP header.
     *
     * @return array An array representing the attribute values
     * @param string $header
     */
    protected function parseCacheControl($header)
    {
        if (\is_object($header)) {
            $header = (string) $header;
        }
        $parts = \ECSPrefix20210508\Symfony\Component\HttpFoundation\HeaderUtils::split($header, ',=');
        return \ECSPrefix20210508\Symfony\Component\HttpFoundation\HeaderUtils::combine($parts);
    }
}