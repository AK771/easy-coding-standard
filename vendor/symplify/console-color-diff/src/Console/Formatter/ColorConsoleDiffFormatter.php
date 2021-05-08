<?php

namespace Symplify\ConsoleColorDiff\Console\Formatter;

use ECSPrefix20210508\Nette\Utils\Strings;
use ECSPrefix20210508\Symfony\Component\Console\Formatter\OutputFormatter;
/**
 * Most is copy-pasted from https://github.com/FriendsOfPHP/PHP-CS-Fixer/blob/master/src/Differ/DiffConsoleFormatter.php
 * to be used as standalone class, without need to require whole package.
 *
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 * @see \Symplify\ConsoleColorDiff\Tests\Console\Formatter\ColorConsoleDiffFormatterTest
 */
final class ColorConsoleDiffFormatter
{
    /**
     * @var string
     * @see https://regex101.com/r/ovLMDF/1
     */
    const PLUS_START_REGEX = '#^(\\+.*)#';
    /**
     * @var string
     * @see https://regex101.com/r/xwywpa/1
     */
    const MINUT_START_REGEX = '#^(\\-.*)#';
    /**
     * @var string
     * @see https://regex101.com/r/CMlwa8/1
     */
    const AT_START_REGEX = '#^(@.*)#';
    /**
     * @var string
     * @see https://regex101.com/r/qduj2O/1
     */
    const NEWLINES_REGEX = "#\n\r|\n#";
    /**
     * @var string
     */
    private $template;
    public function __construct()
    {
        $this->template = \sprintf('<comment>    ---------- begin diff ----------</comment>%s%%s%s<comment>    ----------- end diff -----------</comment>' . \PHP_EOL, \PHP_EOL, \PHP_EOL);
    }
    /**
     * @param string $diff
     */
    public function format($diff) : string
    {
        if (\is_object($diff)) {
            $diff = (string) $diff;
        }
        return $this->formatWithTemplate($diff, $this->template);
    }
    /**
     * @param string $diff
     */
    private function formatWithTemplate($diff, string $template) : string
    {
        if (\is_object($diff)) {
            $diff = (string) $diff;
        }
        $escapedDiff = \ECSPrefix20210508\Symfony\Component\Console\Formatter\OutputFormatter::escape(\rtrim($diff));
        $escapedDiffLines = \ECSPrefix20210508\Nette\Utils\Strings::split($escapedDiff, self::NEWLINES_REGEX);
        $coloredLines = \array_map(function (string $string) : string {
            $string = $this->makePlusLinesGreen($string);
            $string = $this->makeMinusLinesRed($string);
            $string = $this->makeAtNoteCyan($string);
            if ($string === ' ') {
                return '';
            }
            return $string;
        }, $escapedDiffLines);
        return \sprintf($template, \implode(\PHP_EOL, $coloredLines));
    }
    /**
     * @param string $string
     */
    private function makePlusLinesGreen($string) : string
    {
        if (\is_object($string)) {
            $string = (string) $string;
        }
        return \ECSPrefix20210508\Nette\Utils\Strings::replace($string, self::PLUS_START_REGEX, '<fg=green>$1</fg=green>');
    }
    /**
     * @param string $string
     */
    private function makeMinusLinesRed($string) : string
    {
        if (\is_object($string)) {
            $string = (string) $string;
        }
        return \ECSPrefix20210508\Nette\Utils\Strings::replace($string, self::MINUT_START_REGEX, '<fg=red>$1</fg=red>');
    }
    /**
     * @param string $string
     */
    private function makeAtNoteCyan($string) : string
    {
        if (\is_object($string)) {
            $string = (string) $string;
        }
        return \ECSPrefix20210508\Nette\Utils\Strings::replace($string, self::AT_START_REGEX, '<fg=cyan>$1</fg=cyan>');
    }
}