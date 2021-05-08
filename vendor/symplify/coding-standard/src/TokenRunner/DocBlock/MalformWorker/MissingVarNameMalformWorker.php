<?php

namespace Symplify\CodingStandard\TokenRunner\DocBlock\MalformWorker;

use ECSPrefix20210508\Nette\Utils\Strings;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use Symplify\CodingStandard\TokenRunner\Contract\DocBlock\MalformWorkerInterface;
final class MissingVarNameMalformWorker implements \Symplify\CodingStandard\TokenRunner\Contract\DocBlock\MalformWorkerInterface
{
    /**
     * @var string
     * @see https://regex101.com/r/QtWnWv/3
     */
    const VAR_WITHOUT_NAME_REGEX = '#^(?<open>\\/\\*\\* @var )(?<type>[\\\\\\w\\|]+)(?<close>\\s+\\*\\/)$#';
    /**
     * @param Tokens<Token> $tokens
     * @param string $docContent
     */
    public function work($docContent, \PhpCsFixer\Tokenizer\Tokens $tokens, int $position) : string
    {
        if (\is_object($docContent)) {
            $docContent = (string) $docContent;
        }
        if (!\ECSPrefix20210508\Nette\Utils\Strings::match($docContent, self::VAR_WITHOUT_NAME_REGEX)) {
            return $docContent;
        }
        $nextVariableToken = $this->getNextVariableToken($tokens, $position);
        if (!$nextVariableToken instanceof \PhpCsFixer\Tokenizer\Token) {
            return $docContent;
        }
        return \ECSPrefix20210508\Nette\Utils\Strings::replace($docContent, self::VAR_WITHOUT_NAME_REGEX, function (array $match) use($nextVariableToken) : string {
            return $match['open'] . $match['type'] . ' ' . $nextVariableToken->getContent() . $match['close'];
        });
    }
    /**
     * @param Tokens<Token> $tokens
     * @return \PhpCsFixer\Tokenizer\Token|null
     * @param int $position
     */
    private function getNextVariableToken(\PhpCsFixer\Tokenizer\Tokens $tokens, $position)
    {
        $nextMeaningfulTokenPosition = $tokens->getNextMeaningfulToken($position);
        if ($nextMeaningfulTokenPosition === null) {
            return null;
        }
        $nextToken = isset($tokens[$nextMeaningfulTokenPosition]) ? $tokens[$nextMeaningfulTokenPosition] : null;
        if (!$nextToken instanceof \PhpCsFixer\Tokenizer\Token) {
            return null;
        }
        if (!$nextToken->isGivenKind(\T_VARIABLE)) {
            return null;
        }
        return $nextToken;
    }
}