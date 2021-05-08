<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20210508\Symfony\Component\String;

use ECSPrefix20210508\Symfony\Component\String\Exception\ExceptionInterface;
use ECSPrefix20210508\Symfony\Component\String\Exception\InvalidArgumentException;
/**
 * Represents a string of Unicode code points encoded as UTF-8.
 *
 * @author Nicolas Grekas <p@tchwork.com>
 * @author Hugo Hamon <hugohamon@neuf.fr>
 *
 * @throws ExceptionInterface
 */
class CodePointString extends \ECSPrefix20210508\Symfony\Component\String\AbstractUnicodeString
{
    /**
     * @param string $string
     */
    public function __construct($string = '')
    {
        if (\is_object($string)) {
            $string = (string) $string;
        }
        if ('' !== $string && !\preg_match('//u', $string)) {
            throw new \ECSPrefix20210508\Symfony\Component\String\Exception\InvalidArgumentException('Invalid UTF-8 string.');
        }
        $this->string = $string;
    }
    /**
     * @param string ...$suffix
     * @return \Symfony\Component\String\AbstractString
     */
    public function append(...$suffix)
    {
        $str = clone $this;
        $str->string .= 1 >= \count($suffix) ? isset($suffix[0]) ? $suffix[0] : '' : \implode('', $suffix);
        if (!\preg_match('//u', $str->string)) {
            throw new \ECSPrefix20210508\Symfony\Component\String\Exception\InvalidArgumentException('Invalid UTF-8 string.');
        }
        return $str;
    }
    /**
     * @param int $length
     * @return mixed[]
     */
    public function chunk($length = 1)
    {
        if (1 > $length) {
            throw new \ECSPrefix20210508\Symfony\Component\String\Exception\InvalidArgumentException('The chunk length must be greater than zero.');
        }
        if ('' === $this->string) {
            return [];
        }
        $rx = '/(';
        while (65535 < $length) {
            $rx .= '.{65535}';
            $length -= 65535;
        }
        $rx .= '.{' . $length . '})/us';
        $str = clone $this;
        $chunks = [];
        foreach (\preg_split($rx, $this->string, -1, \PREG_SPLIT_DELIM_CAPTURE | \PREG_SPLIT_NO_EMPTY) as $chunk) {
            $str->string = $chunk;
            $chunks[] = clone $str;
        }
        return $chunks;
    }
    /**
     * @param int $offset
     * @return mixed[]
     */
    public function codePointsAt($offset)
    {
        $str = $offset ? $this->slice($offset, 1) : $this;
        return '' === $str->string ? [] : [\mb_ord($str->string, 'UTF-8')];
    }
    /**
     * @return bool
     */
    public function endsWith($suffix)
    {
        if ($suffix instanceof \ECSPrefix20210508\Symfony\Component\String\AbstractString) {
            $suffix = $suffix->string;
        } elseif (\is_array($suffix) || $suffix instanceof \Traversable) {
            return parent::endsWith($suffix);
        } else {
            $suffix = (string) $suffix;
        }
        if ('' === $suffix || !\preg_match('//u', $suffix)) {
            return \false;
        }
        if ($this->ignoreCase) {
            return \preg_match('{' . \preg_quote($suffix) . '$}iuD', $this->string);
        }
        return \strlen($this->string) >= \strlen($suffix) && 0 === \substr_compare($this->string, $suffix, -\strlen($suffix));
    }
    /**
     * @return bool
     */
    public function equalsTo($string)
    {
        if ($string instanceof \ECSPrefix20210508\Symfony\Component\String\AbstractString) {
            $string = $string->string;
        } elseif (\is_array($string) || $string instanceof \Traversable) {
            return parent::equalsTo($string);
        } else {
            $string = (string) $string;
        }
        if ('' !== $string && $this->ignoreCase) {
            return \strlen($string) === \strlen($this->string) && 0 === \mb_stripos($this->string, $string, 0, 'UTF-8');
        }
        return $string === $this->string;
    }
    /**
     * @return int|null
     * @param int $offset
     */
    public function indexOf($needle, $offset = 0)
    {
        if ($needle instanceof \ECSPrefix20210508\Symfony\Component\String\AbstractString) {
            $needle = $needle->string;
        } elseif (\is_array($needle) || $needle instanceof \Traversable) {
            return parent::indexOf($needle, $offset);
        } else {
            $needle = (string) $needle;
        }
        if ('' === $needle) {
            return null;
        }
        $i = $this->ignoreCase ? \mb_stripos($this->string, $needle, $offset, 'UTF-8') : \mb_strpos($this->string, $needle, $offset, 'UTF-8');
        return \false === $i ? null : $i;
    }
    /**
     * @return int|null
     * @param int $offset
     */
    public function indexOfLast($needle, $offset = 0)
    {
        if ($needle instanceof \ECSPrefix20210508\Symfony\Component\String\AbstractString) {
            $needle = $needle->string;
        } elseif (\is_array($needle) || $needle instanceof \Traversable) {
            return parent::indexOfLast($needle, $offset);
        } else {
            $needle = (string) $needle;
        }
        if ('' === $needle) {
            return null;
        }
        $i = $this->ignoreCase ? \mb_strripos($this->string, $needle, $offset, 'UTF-8') : \mb_strrpos($this->string, $needle, $offset, 'UTF-8');
        return \false === $i ? null : $i;
    }
    /**
     * @return int
     */
    public function length()
    {
        return \mb_strlen($this->string, 'UTF-8');
    }
    /**
     * @param string ...$prefix
     * @return \Symfony\Component\String\AbstractString
     */
    public function prepend(...$prefix)
    {
        $str = clone $this;
        $str->string = (1 >= \count($prefix) ? isset($prefix[0]) ? $prefix[0] : '' : \implode('', $prefix)) . $this->string;
        if (!\preg_match('//u', $str->string)) {
            throw new \ECSPrefix20210508\Symfony\Component\String\Exception\InvalidArgumentException('Invalid UTF-8 string.');
        }
        return $str;
    }
    /**
     * @param string $from
     */
    public function replace($from, string $to) : \ECSPrefix20210508\Symfony\Component\String\AbstractString
    {
        if (\is_object($from)) {
            $from = (string) $from;
        }
        $str = clone $this;
        if ('' === $from || !\preg_match('//u', $from)) {
            return $str;
        }
        if ('' !== $to && !\preg_match('//u', $to)) {
            throw new \ECSPrefix20210508\Symfony\Component\String\Exception\InvalidArgumentException('Invalid UTF-8 string.');
        }
        if ($this->ignoreCase) {
            $str->string = \implode($to, \preg_split('{' . \preg_quote($from) . '}iuD', $this->string));
        } else {
            $str->string = \str_replace($from, $to, $this->string);
        }
        return $str;
    }
    /**
     * @param int $start
     * @param int $length
     * @return \Symfony\Component\String\AbstractString
     */
    public function slice($start = 0, $length = null)
    {
        $str = clone $this;
        $str->string = \mb_substr($this->string, $start, $length, 'UTF-8');
        return $str;
    }
    /**
     * @param string $replacement
     */
    public function splice($replacement, int $start = 0, int $length = null) : \ECSPrefix20210508\Symfony\Component\String\AbstractString
    {
        if (\is_object($replacement)) {
            $replacement = (string) $replacement;
        }
        if (!\preg_match('//u', $replacement)) {
            throw new \ECSPrefix20210508\Symfony\Component\String\Exception\InvalidArgumentException('Invalid UTF-8 string.');
        }
        $str = clone $this;
        $start = $start ? \strlen(\mb_substr($this->string, 0, $start, 'UTF-8')) : 0;
        $length = $length ? \strlen(\mb_substr($this->string, $start, $length, 'UTF-8')) : $length;
        $str->string = \substr_replace($this->string, $replacement, $start, isset($length) ? $length : \PHP_INT_MAX);
        return $str;
    }
    /**
     * @param string $delimiter
     */
    public function split($delimiter, int $limit = null, int $flags = null) : array
    {
        if (\is_object($delimiter)) {
            $delimiter = (string) $delimiter;
        }
        if (1 > ($limit = isset($limit) ? $limit : \PHP_INT_MAX)) {
            throw new \ECSPrefix20210508\Symfony\Component\String\Exception\InvalidArgumentException('Split limit must be a positive integer.');
        }
        if ('' === $delimiter) {
            throw new \ECSPrefix20210508\Symfony\Component\String\Exception\InvalidArgumentException('Split delimiter is empty.');
        }
        if (null !== $flags) {
            return parent::split($delimiter . 'u', $limit, $flags);
        }
        if (!\preg_match('//u', $delimiter)) {
            throw new \ECSPrefix20210508\Symfony\Component\String\Exception\InvalidArgumentException('Split delimiter is not a valid UTF-8 string.');
        }
        $str = clone $this;
        $chunks = $this->ignoreCase ? \preg_split('{' . \preg_quote($delimiter) . '}iuD', $this->string, $limit) : \explode($delimiter, $this->string, $limit);
        foreach ($chunks as &$chunk) {
            $str->string = $chunk;
            $chunk = clone $str;
        }
        return $chunks;
    }
    /**
     * @return bool
     */
    public function startsWith($prefix)
    {
        if ($prefix instanceof \ECSPrefix20210508\Symfony\Component\String\AbstractString) {
            $prefix = $prefix->string;
        } elseif (\is_array($prefix) || $prefix instanceof \Traversable) {
            return parent::startsWith($prefix);
        } else {
            $prefix = (string) $prefix;
        }
        if ('' === $prefix || !\preg_match('//u', $prefix)) {
            return \false;
        }
        if ($this->ignoreCase) {
            return 0 === \mb_stripos($this->string, $prefix, 0, 'UTF-8');
        }
        return 0 === \strncmp($this->string, $prefix, \strlen($prefix));
    }
}