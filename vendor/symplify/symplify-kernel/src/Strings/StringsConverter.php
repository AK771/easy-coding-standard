<?php

namespace Symplify\SymplifyKernel\Strings;

use ECSPrefix20210508\Nette\Utils\Strings;
final class StringsConverter
{
    /**
     * @see https://regex101.com/r/5Lp2FX/1
     * @var string
     */
    const CAMEL_CASE_BY_WORD_REGEX = '#([A-Z][A-Z0-9]*(?=$|[A-Z][a-z0-9])|[A-Za-z][a-z0-9]+)#';
    /**
     * @param string $input
     */
    public function camelCaseToGlue($input, string $glue) : string
    {
        if (\is_object($input)) {
            $input = (string) $input;
        }
        if ($input === \strtolower($input)) {
            return $input;
        }
        $matches = \ECSPrefix20210508\Nette\Utils\Strings::matchAll($input, self::CAMEL_CASE_BY_WORD_REGEX);
        $parts = [];
        foreach ($matches as $match) {
            $parts[] = $match[0] === \strtoupper($match[0]) ? \strtolower($match[0]) : \lcfirst($match[0]);
        }
        return \implode($glue, $parts);
    }
    /**
     * @param string $content
     */
    public function dashedToCamelCaseWithGlue($content, string $glue) : string
    {
        if (\is_object($content)) {
            $content = (string) $content;
        }
        $parts = \explode('-', $content);
        $casedParts = [];
        foreach ($parts as $part) {
            // special names
            if ($part === 'phpstan') {
                $casedParts[] = 'PHPStan';
                continue;
            }
            $casedParts[] = \ucfirst($part);
        }
        return \implode($glue, $casedParts);
    }
}