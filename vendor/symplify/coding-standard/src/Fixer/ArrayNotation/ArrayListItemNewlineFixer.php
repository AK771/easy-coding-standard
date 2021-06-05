<?php

declare (strict_types=1);
namespace Symplify\CodingStandard\Fixer\ArrayNotation;

use ECSPrefix20210605\Nette\Utils\Strings;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use PhpCsFixer\WhitespacesFixerConfig;
use SplFileInfo;
use Symplify\CodingStandard\Fixer\AbstractSymplifyFixer;
use Symplify\CodingStandard\TokenRunner\Analyzer\FixerAnalyzer\ArrayAnalyzer;
use Symplify\CodingStandard\TokenRunner\Traverser\ArrayBlockInfoFinder;
use Symplify\CodingStandard\TokenRunner\ValueObject\BlockInfo;
use Symplify\CodingStandard\TokenRunner\ValueObject\TokenKinds;
use ECSPrefix20210605\Symplify\RuleDocGenerator\Contract\DocumentedRuleInterface;
use ECSPrefix20210605\Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use ECSPrefix20210605\Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
/**
 * @see \Symplify\CodingStandard\Tests\Fixer\ArrayNotation\ArrayListItemNewlineFixer\ArrayListItemNewlineFixerTest
 */
final class ArrayListItemNewlineFixer extends \Symplify\CodingStandard\Fixer\AbstractSymplifyFixer implements \ECSPrefix20210605\Symplify\RuleDocGenerator\Contract\DocumentedRuleInterface
{
    /**
     * @var string
     */
    const ERROR_MESSAGE = 'Indexed PHP array item has to have one line per item';
    /**
     * @var ArrayAnalyzer
     */
    private $arrayAnalyzer;
    /**
     * @var WhitespacesFixerConfig
     */
    private $whitespacesFixerConfig;
    /**
     * @var ArrayBlockInfoFinder
     */
    private $arrayBlockInfoFinder;
    public function __construct(\Symplify\CodingStandard\TokenRunner\Analyzer\FixerAnalyzer\ArrayAnalyzer $arrayAnalyzer, \PhpCsFixer\WhitespacesFixerConfig $whitespacesFixerConfig, \Symplify\CodingStandard\TokenRunner\Traverser\ArrayBlockInfoFinder $arrayBlockInfoFinder)
    {
        $this->arrayAnalyzer = $arrayAnalyzer;
        $this->whitespacesFixerConfig = $whitespacesFixerConfig;
        $this->arrayBlockInfoFinder = $arrayBlockInfoFinder;
    }
    public function getDefinition() : \PhpCsFixer\FixerDefinition\FixerDefinitionInterface
    {
        return new \PhpCsFixer\FixerDefinition\FixerDefinition(self::ERROR_MESSAGE, []);
    }
    public function getPriority() : int
    {
        return 40;
    }
    /**
     * @param Tokens<Token> $tokens
     */
    public function isCandidate(\PhpCsFixer\Tokenizer\Tokens $tokens) : bool
    {
        if (!$tokens->isAnyTokenKindsFound(\Symplify\CodingStandard\TokenRunner\ValueObject\TokenKinds::ARRAY_OPEN_TOKENS)) {
            return \false;
        }
        return $tokens->isTokenKindFound(\T_DOUBLE_ARROW);
    }
    /**
     * @param Tokens<Token> $tokens
     * @return void
     */
    public function fix(\SplFileInfo $fileInfo, \PhpCsFixer\Tokenizer\Tokens $tokens)
    {
        $arrayBlockInfos = $this->arrayBlockInfoFinder->findArrayOpenerBlockInfos($tokens);
        foreach ($arrayBlockInfos as $arrayBlockInfo) {
            $this->fixArrayOpener($tokens, $arrayBlockInfo);
        }
    }
    public function getRuleDefinition() : \ECSPrefix20210605\Symplify\RuleDocGenerator\ValueObject\RuleDefinition
    {
        return new \ECSPrefix20210605\Symplify\RuleDocGenerator\ValueObject\RuleDefinition(self::ERROR_MESSAGE, [new \ECSPrefix20210605\Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample(<<<'CODE_SAMPLE'
$value = ['simple' => 1, 'easy' => 2];
CODE_SAMPLE
, <<<'CODE_SAMPLE'
$value = ['simple' => 1,
'easy' => 2];
CODE_SAMPLE
)]);
    }
    /**
     * @param Tokens<Token> $tokens
     * @return void
     */
    private function fixArrayOpener(\PhpCsFixer\Tokenizer\Tokens $tokens, \Symplify\CodingStandard\TokenRunner\ValueObject\BlockInfo $blockInfo)
    {
        if (!$this->arrayAnalyzer->isIndexedList($tokens, $blockInfo)) {
            return;
        }
        $this->arrayAnalyzer->traverseArrayWithoutNesting($tokens, $blockInfo, function (\PhpCsFixer\Tokenizer\Token $token, int $position, \PhpCsFixer\Tokenizer\Tokens $tokens) {
            if ($token->getContent() !== ',') {
                return;
            }
            $nextTokenPosition = $position + 1;
            $nextToken = $tokens[$nextTokenPosition] ?? null;
            if (!$nextToken instanceof \PhpCsFixer\Tokenizer\Token) {
                return;
            }
            if (\ECSPrefix20210605\Nette\Utils\Strings::contains($nextToken->getContent(), "\n")) {
                return;
            }
            $tokens->ensureWhitespaceAtIndex($nextTokenPosition, 0, $this->whitespacesFixerConfig->getLineEnding());
        });
    }
}
