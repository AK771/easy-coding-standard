<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20210508\Symfony\Component\String\Slugger;

use ECSPrefix20210508\Symfony\Component\String\AbstractUnicodeString;
use ECSPrefix20210508\Symfony\Component\String\UnicodeString;
use ECSPrefix20210508\Symfony\Contracts\Translation\LocaleAwareInterface;
if (!\interface_exists(\ECSPrefix20210508\Symfony\Contracts\Translation\LocaleAwareInterface::class)) {
    throw new \LogicException('You cannot use the "Symfony\\Component\\String\\Slugger\\AsciiSlugger" as the "symfony/translation-contracts" package is not installed. Try running "composer require symfony/translation-contracts".');
}
/**
 * @author Titouan Galopin <galopintitouan@gmail.com>
 */
class AsciiSlugger implements \ECSPrefix20210508\Symfony\Component\String\Slugger\SluggerInterface, \ECSPrefix20210508\Symfony\Contracts\Translation\LocaleAwareInterface
{
    const LOCALE_TO_TRANSLITERATOR_ID = ['am' => 'Amharic-Latin', 'ar' => 'Arabic-Latin', 'az' => 'Azerbaijani-Latin', 'be' => 'Belarusian-Latin', 'bg' => 'Bulgarian-Latin', 'bn' => 'Bengali-Latin', 'de' => 'de-ASCII', 'el' => 'Greek-Latin', 'fa' => 'Persian-Latin', 'he' => 'Hebrew-Latin', 'hy' => 'Armenian-Latin', 'ka' => 'Georgian-Latin', 'kk' => 'Kazakh-Latin', 'ky' => 'Kirghiz-Latin', 'ko' => 'Korean-Latin', 'mk' => 'Macedonian-Latin', 'mn' => 'Mongolian-Latin', 'or' => 'Oriya-Latin', 'ps' => 'Pashto-Latin', 'ru' => 'Russian-Latin', 'sr' => 'Serbian-Latin', 'sr_Cyrl' => 'Serbian-Latin', 'th' => 'Thai-Latin', 'tk' => 'Turkmen-Latin', 'uk' => 'Ukrainian-Latin', 'uz' => 'Uzbek-Latin', 'zh' => 'Han-Latin'];
    private $defaultLocale;
    private $symbolsMap = ['en' => ['@' => 'at', '&' => 'and']];
    /**
     * Cache of transliterators per locale.
     *
     * @var \Transliterator[]
     */
    private $transliterators = [];
    /**
     * @param array|\Closure|null $symbolsMap
     * @param string $defaultLocale
     */
    public function __construct($defaultLocale = null, $symbolsMap = null)
    {
        if (null !== $symbolsMap && !\is_array($symbolsMap) && !$symbolsMap instanceof \Closure) {
            throw new \TypeError(\sprintf('Argument 2 passed to "%s()" must be array, Closure or null, "%s" given.', __METHOD__, \gettype($symbolsMap)));
        }
        $this->defaultLocale = $defaultLocale;
        $this->symbolsMap = isset($symbolsMap) ? $symbolsMap : $this->symbolsMap;
    }
    /**
     * {@inheritdoc}
     */
    public function setLocale($locale)
    {
        if (\is_object($locale)) {
            $locale = (string) $locale;
        }
        $this->defaultLocale = $locale;
    }
    /**
     * {@inheritdoc}
     */
    public function getLocale()
    {
        return $this->defaultLocale;
    }
    /**
     * {@inheritdoc}
     * @param string $string
     */
    public function slug($string, string $separator = '-', string $locale = null) : \ECSPrefix20210508\Symfony\Component\String\AbstractUnicodeString
    {
        if (\is_object($string)) {
            $string = (string) $string;
        }
        $locale = isset($locale) ? $locale : $this->defaultLocale;
        $transliterator = [];
        if ('de' === $locale || 0 === \strpos($locale, 'de_')) {
            // Use the shortcut for German in UnicodeString::ascii() if possible (faster and no requirement on intl)
            $transliterator = ['de-ASCII'];
        } elseif (\function_exists('transliterator_transliterate') && $locale) {
            $transliterator = (array) $this->createTransliterator($locale);
        }
        if ($this->symbolsMap instanceof \Closure) {
            $symbolsMap = $this->symbolsMap;
            \array_unshift($transliterator, static function ($s) use($symbolsMap, $locale) {
                return $symbolsMap($s, $locale);
            });
        }
        $unicodeString = (new \ECSPrefix20210508\Symfony\Component\String\UnicodeString($string))->ascii($transliterator);
        if (\is_array($this->symbolsMap) && isset($this->symbolsMap[$locale])) {
            foreach ($this->symbolsMap[$locale] as $char => $replace) {
                $unicodeString = $unicodeString->replace($char, ' ' . $replace . ' ');
            }
        }
        return $unicodeString->replaceMatches('/[^A-Za-z0-9]++/', $separator)->trim($separator);
    }
    /**
     * @return \Transliterator|null
     * @param string $locale
     */
    private function createTransliterator($locale)
    {
        if (\is_object($locale)) {
            $locale = (string) $locale;
        }
        if (\array_key_exists($locale, $this->transliterators)) {
            return $this->transliterators[$locale];
        }
        // Exact locale supported, cache and return
        if ($id = isset(self::LOCALE_TO_TRANSLITERATOR_ID[$locale]) ? self::LOCALE_TO_TRANSLITERATOR_ID[$locale] : null) {
            return $this->transliterators[$locale] = \Transliterator::create($id . '/BGN') !== null ? \Transliterator::create($id . '/BGN') : \Transliterator::create($id);
        }
        // Locale not supported and no parent, fallback to any-latin
        if (\false === ($str = \strrchr($locale, '_'))) {
            return $this->transliterators[$locale] = null;
        }
        // Try to use the parent locale (ie. try "de" for "de_AT") and cache both locales
        $parent = \substr($locale, 0, -\strlen($str));
        if ($id = isset(self::LOCALE_TO_TRANSLITERATOR_ID[$parent]) ? self::LOCALE_TO_TRANSLITERATOR_ID[$parent] : null) {
            $transliterator = \Transliterator::create($id . '/BGN') !== null ? \Transliterator::create($id . '/BGN') : \Transliterator::create($id);
        }
        return $this->transliterators[$locale] = $this->transliterators[$parent] = isset($transliterator) ? $transliterator : null;
    }
}