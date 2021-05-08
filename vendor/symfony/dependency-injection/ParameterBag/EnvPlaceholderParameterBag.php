<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20210508\Symfony\Component\DependencyInjection\ParameterBag;

use ECSPrefix20210508\Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use ECSPrefix20210508\Symfony\Component\DependencyInjection\Exception\RuntimeException;
/**
 * @author Nicolas Grekas <p@tchwork.com>
 */
class EnvPlaceholderParameterBag extends \ECSPrefix20210508\Symfony\Component\DependencyInjection\ParameterBag\ParameterBag
{
    private $envPlaceholderUniquePrefix;
    private $envPlaceholders = [];
    private $unusedEnvPlaceholders = [];
    private $providedTypes = [];
    private static $counter = 0;
    /**
     * {@inheritdoc}
     * @param string $name
     */
    public function get($name)
    {
        if (\is_object($name)) {
            $name = (string) $name;
        }
        if (0 === \strpos($name, 'env(') && ')' === \substr($name, -1) && 'env()' !== $name) {
            $env = \substr($name, 4, -1);
            if (isset($this->envPlaceholders[$env])) {
                foreach ($this->envPlaceholders[$env] as $placeholder) {
                    return $placeholder;
                    // return first result
                }
            }
            if (isset($this->unusedEnvPlaceholders[$env])) {
                foreach ($this->unusedEnvPlaceholders[$env] as $placeholder) {
                    return $placeholder;
                    // return first result
                }
            }
            if (!\preg_match('/^(?:[-.\\w]*+:)*+\\w++$/', $env)) {
                throw new \ECSPrefix20210508\Symfony\Component\DependencyInjection\Exception\InvalidArgumentException(\sprintf('Invalid %s name: only "word" characters are allowed.', $name));
            }
            if ($this->has($name) && null !== ($defaultValue = parent::get($name)) && !\is_string($defaultValue)) {
                throw new \ECSPrefix20210508\Symfony\Component\DependencyInjection\Exception\RuntimeException(\sprintf('The default value of an env() parameter must be a string or null, but "%s" given to "%s".', \get_debug_type($defaultValue), $name));
            }
            $uniqueName = \md5($name . '_' . self::$counter++);
            $placeholder = \sprintf('%s_%s_%s', $this->getEnvPlaceholderUniquePrefix(), \strtr($env, ':-.', '___'), $uniqueName);
            $this->envPlaceholders[$env][$placeholder] = $placeholder;
            return $placeholder;
        }
        return parent::get($name);
    }
    /**
     * Gets the common env placeholder prefix for env vars created by this bag.
     * @return string
     */
    public function getEnvPlaceholderUniquePrefix()
    {
        if (null === $this->envPlaceholderUniquePrefix) {
            $reproducibleEntropy = \unserialize(\serialize($this->parameters));
            \array_walk_recursive($reproducibleEntropy, function (&$v) {
                $v = null;
            });
            $this->envPlaceholderUniquePrefix = 'env_' . \substr(\md5(\serialize($reproducibleEntropy)), -16);
        }
        return $this->envPlaceholderUniquePrefix;
    }
    /**
     * Returns the map of env vars used in the resolved parameter values to their placeholders.
     *
     * @return string[][] A map of env var names to their placeholders
     */
    public function getEnvPlaceholders()
    {
        return $this->envPlaceholders;
    }
    /**
     * @return mixed[]
     */
    public function getUnusedEnvPlaceholders()
    {
        return $this->unusedEnvPlaceholders;
    }
    public function clearUnusedEnvPlaceholders()
    {
        $this->unusedEnvPlaceholders = [];
    }
    /**
     * Merges the env placeholders of another EnvPlaceholderParameterBag.
     * @param $this $bag
     */
    public function mergeEnvPlaceholders($bag)
    {
        if ($newPlaceholders = $bag->getEnvPlaceholders()) {
            $this->envPlaceholders += $newPlaceholders;
            foreach ($newPlaceholders as $env => $placeholders) {
                $this->envPlaceholders[$env] += $placeholders;
            }
        }
        if ($newUnusedPlaceholders = $bag->getUnusedEnvPlaceholders()) {
            $this->unusedEnvPlaceholders += $newUnusedPlaceholders;
            foreach ($newUnusedPlaceholders as $env => $placeholders) {
                $this->unusedEnvPlaceholders[$env] += $placeholders;
            }
        }
    }
    /**
     * Maps env prefixes to their corresponding PHP types.
     */
    public function setProvidedTypes(array $providedTypes)
    {
        $this->providedTypes = $providedTypes;
    }
    /**
     * Gets the PHP types corresponding to env() parameter prefixes.
     *
     * @return string[][]
     */
    public function getProvidedTypes()
    {
        return $this->providedTypes;
    }
    /**
     * {@inheritdoc}
     */
    public function resolve()
    {
        if ($this->resolved) {
            return;
        }
        parent::resolve();
        foreach ($this->envPlaceholders as $env => $placeholders) {
            if ($this->has($name = "env({$env})") && null !== ($default = $this->parameters[$name]) && !\is_string($default)) {
                throw new \ECSPrefix20210508\Symfony\Component\DependencyInjection\Exception\RuntimeException(\sprintf('The default value of env parameter "%s" must be a string or null, "%s" given.', $env, \get_debug_type($default)));
            }
        }
    }
}