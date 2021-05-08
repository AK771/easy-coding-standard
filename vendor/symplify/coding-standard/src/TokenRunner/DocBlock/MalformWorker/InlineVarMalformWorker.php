<?php

namespace Symplify\CodingStandard\TokenRunner\DocBlock\MalformWorker;

use ECSPrefix20210508\Nette\Utils\Strings;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use Symplify\CodingStandard\TokenRunner\Contract\DocBlock\MalformWorkerInterface;
final class InlineVarMalformWorker implements \Symplify\CodingStandard\TokenRunner\Contract\DocBlock\MalformWorkerInterface
{
    /**
     * @var string
     * @see https://regex101.com/r/8OuO60/1
     */
    const SINGLE_ASTERISK_START_REGEX = '#^/\\*(\\n?\\s+@var)#';
    /**
     * @param Tokens<Token> $tokens
     * @param string $docContent
     */
    public function work($docContent, \PhpCsFixer\Tokenizer\Tokens $tokens, int $position) : string
    {
        if (\is_object($docContent)) {
            $docContent = (string) $docContent;
        }
        /** @var Token $token */
        $token = $tokens[$position];
        if (!$token->isGivenKind(\T_COMMENT)) {
            return $docContent;
        }
        return \ECSPrefix20210508\Nette\Utils\Strings::replace($docContent, self::SINGLE_ASTERISK_START_REGEX, '/**$1');
    }
}