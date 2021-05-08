<?php

namespace Symplify\CodingStandard\TokenRunner\Transformer\FixerTransformer;

use ECSPrefix20210508\Nette\Utils\Strings;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use PhpCsFixer\WhitespacesFixerConfig;
use Symplify\CodingStandard\TokenRunner\Analyzer\FixerAnalyzer\TokenSkipper;
use Symplify\CodingStandard\TokenRunner\Exception\TokenNotFoundException;
use Symplify\CodingStandard\TokenRunner\ValueObject\BlockInfo;
use Symplify\CodingStandard\TokenRunner\ValueObject\LineKind;
use Symplify\CodingStandard\TokenRunner\Whitespace\IndentResolver;
final class TokensNewliner
{
    /**
     * @var LineLengthCloserTransformer
     */
    private $lineLengthCloserTransformer;
    /**
     * @var TokenSkipper
     */
    private $tokenSkipper;
    /**
     * @var LineLengthOpenerTransformer
     */
    private $lineLengthOpenerTransformer;
    /**
     * @var WhitespacesFixerConfig
     */
    private $whitespacesFixerConfig;
    /**
     * @var IndentResolver
     */
    private $indentResolver;
    public function __construct(\Symplify\CodingStandard\TokenRunner\Transformer\FixerTransformer\LineLengthCloserTransformer $lineLengthCloserTransformer, \Symplify\CodingStandard\TokenRunner\Analyzer\FixerAnalyzer\TokenSkipper $tokenSkipper, \Symplify\CodingStandard\TokenRunner\Transformer\FixerTransformer\LineLengthOpenerTransformer $lineLengthOpenerTransformer, \PhpCsFixer\WhitespacesFixerConfig $whitespacesFixerConfig, \Symplify\CodingStandard\TokenRunner\Whitespace\IndentResolver $indentResolver)
    {
        $this->lineLengthCloserTransformer = $lineLengthCloserTransformer;
        $this->tokenSkipper = $tokenSkipper;
        $this->lineLengthOpenerTransformer = $lineLengthOpenerTransformer;
        $this->whitespacesFixerConfig = $whitespacesFixerConfig;
        $this->indentResolver = $indentResolver;
    }
    /**
     * @param Tokens|Token[] $tokens
     * @return void
     * @param int $kind
     */
    public function breakItems(\Symplify\CodingStandard\TokenRunner\ValueObject\BlockInfo $blockInfo, \PhpCsFixer\Tokenizer\Tokens $tokens, $kind = \Symplify\CodingStandard\TokenRunner\ValueObject\LineKind::CALLS)
    {
        // from bottom top, to prevent skipping ids
        //  e.g when token is added in the middle, the end index does now point to earlier element!
        $currentNewlineIndentWhitespace = $this->indentResolver->resolveCurrentNewlineIndentWhitespace($tokens, $blockInfo->getStart());
        $newlineIndentWhitespace = $this->indentResolver->resolveNewlineIndentWhitespace($tokens, $blockInfo->getStart());
        // 1. break before arguments closing
        $this->lineLengthCloserTransformer->insertNewlineBeforeClosingIfNeeded($tokens, $blockInfo, $kind, $currentNewlineIndentWhitespace, $this->indentResolver->resolveClosingBracketNewlineWhitespace($tokens, $blockInfo->getStart()));
        // again, from the bottom to the top
        for ($i = $blockInfo->getEnd() - 1; $i > $blockInfo->getStart(); --$i) {
            /** @var Token $currentToken */
            $currentToken = $tokens[$i];
            $i = $this->tokenSkipper->skipBlocksReversed($tokens, $i);
            // 2. new line after each comma ",", instead of just space
            if ($currentToken->getContent() === ',') {
                if ($this->isLastItem($tokens, $i)) {
                    continue;
                }
                if ($this->isFollowedByComment($tokens, $i)) {
                    continue;
                }
                $tokens->ensureWhitespaceAtIndex($i + 1, 0, $newlineIndentWhitespace);
            }
        }
        // 3. break after arguments opening
        $this->lineLengthOpenerTransformer->insertNewlineAfterOpeningIfNeeded($tokens, $blockInfo->getStart(), $newlineIndentWhitespace);
    }
    /**
     * Has already newline? usually the last line => skip to prevent double spacing
     *
     * @param Tokens|Token[] $tokens
     * @param int $position
     * @return bool
     */
    private function isLastItem(\PhpCsFixer\Tokenizer\Tokens $tokens, $position)
    {
        $nextPosition = $position + 1;
        if (!isset($tokens[$nextPosition])) {
            throw new \Symplify\CodingStandard\TokenRunner\Exception\TokenNotFoundException($nextPosition);
        }
        $tokenContent = $tokens[$nextPosition]->getContent();
        return \ECSPrefix20210508\Nette\Utils\Strings::contains($tokenContent, $this->whitespacesFixerConfig->getLineEnding());
    }
    /**
     * @param Tokens|Token[] $tokens
     * @param int $i
     * @return bool
     */
    private function isFollowedByComment(\PhpCsFixer\Tokenizer\Tokens $tokens, $i)
    {
        $nextToken = $tokens[$i + 1];
        $nextNextToken = $tokens[$i + 2];
        if ($nextNextToken->isComment()) {
            return \true;
        }
        // if next token is just space, turn it to newline
        if (!$nextToken->isWhitespace(' ')) {
            return \false;
        }
        return $nextNextToken->isComment();
    }
}