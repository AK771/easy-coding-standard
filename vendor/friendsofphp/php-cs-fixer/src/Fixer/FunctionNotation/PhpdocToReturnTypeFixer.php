<?php

/*
 * This file is part of PHP CS Fixer.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *     Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
namespace PhpCsFixer\Fixer\FunctionNotation;

use PhpCsFixer\AbstractPhpdocToTypeDeclarationFixer;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\FixerDefinition\VersionSpecification;
use PhpCsFixer\FixerDefinition\VersionSpecificCodeSample;
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
/**
 * @author Filippo Tessarotto <zoeslam@gmail.com>
 */
final class PhpdocToReturnTypeFixer extends \PhpCsFixer\AbstractPhpdocToTypeDeclarationFixer
{
    /**
     * @var array<int, array<int, int|string>>
     */
    private $excludeFuncNames = [[\T_STRING, '__construct'], [\T_STRING, '__destruct'], [\T_STRING, '__clone']];
    /**
     * @var array<string, true>
     */
    private $skippedTypes = ['mixed' => \true, 'resource' => \true, 'null' => \true];
    /**
     * {@inheritdoc}
     * @return \PhpCsFixer\FixerDefinition\FixerDefinitionInterface
     */
    public function getDefinition()
    {
        return new \PhpCsFixer\FixerDefinition\FixerDefinition('EXPERIMENTAL: Takes `@return` annotation of non-mixed types and adjusts accordingly the function signature. Requires PHP >= 7.0.', [new \PhpCsFixer\FixerDefinition\VersionSpecificCodeSample('<?php

/** @return \\My\\Bar */
function my_foo()
{}
', new \PhpCsFixer\FixerDefinition\VersionSpecification(70000)), new \PhpCsFixer\FixerDefinition\VersionSpecificCodeSample('<?php

/** @return void */
function my_foo()
{}
', new \PhpCsFixer\FixerDefinition\VersionSpecification(70100)), new \PhpCsFixer\FixerDefinition\VersionSpecificCodeSample('<?php

/** @return object */
function my_foo()
{}
', new \PhpCsFixer\FixerDefinition\VersionSpecification(70200)), new \PhpCsFixer\FixerDefinition\VersionSpecificCodeSample('<?php

/** @return Foo */
function foo() {}
/** @return string */
function bar() {}
', new \PhpCsFixer\FixerDefinition\VersionSpecification(70100), ['scalar_types' => \false]), new \PhpCsFixer\FixerDefinition\VersionSpecificCodeSample('<?php
final class Foo {
    /**
     * @return static
     */
    public function create($prototype) {
        return new static($prototype);
    }
}
', new \PhpCsFixer\FixerDefinition\VersionSpecification(80000))], null, 'This rule is EXPERIMENTAL and [1] is not covered with backward compatibility promise. [2] `@return` annotation is mandatory for the fixer to make changes, signatures of methods without it (no docblock, inheritdocs) will not be fixed. [3] Manual actions are required if inherited signatures are not properly documented.');
    }
    /**
     * {@inheritdoc}
     * @return bool
     */
    public function isCandidate(\PhpCsFixer\Tokenizer\Tokens $tokens)
    {
        if (\PHP_VERSION_ID >= 70400 && $tokens->isTokenKindFound(\T_FN)) {
            return \true;
        }
        return \PHP_VERSION_ID >= 70000 && $tokens->isTokenKindFound(\T_FUNCTION);
    }
    /**
     * {@inheritdoc}
     *
     * Must run before FullyQualifiedStrictTypesFixer, NoSuperfluousPhpdocTagsFixer, PhpdocAlignFixer, ReturnTypeDeclarationFixer.
     * Must run after AlignMultilineCommentFixer, CommentToPhpdocFixer, PhpdocIndentFixer, PhpdocScalarFixer, PhpdocScalarFixer, PhpdocToCommentFixer, PhpdocTypesFixer, PhpdocTypesFixer.
     * @return int
     */
    public function getPriority()
    {
        return 13;
    }
    /**
     * @param string $type
     */
    protected function isSkippedType($type) : bool
    {
        if (\is_object($type)) {
            $type = (string) $type;
        }
        return isset($this->skippedTypes[$type]);
    }
    /**
     * {@inheritdoc}
     * @return void
     */
    protected function applyFix(\SplFileInfo $file, \PhpCsFixer\Tokenizer\Tokens $tokens)
    {
        if (\PHP_VERSION_ID >= 80000) {
            unset($this->skippedTypes['mixed']);
        }
        for ($index = $tokens->count() - 1; 0 < $index; --$index) {
            if (!$tokens[$index]->isGivenKind(\T_FUNCTION) && (\PHP_VERSION_ID < 70400 || !$tokens[$index]->isGivenKind(\T_FN))) {
                continue;
            }
            $funcName = $tokens->getNextMeaningfulToken($index);
            if ($tokens[$funcName]->equalsAny($this->excludeFuncNames, \false)) {
                continue;
            }
            $docCommentIndex = $this->findFunctionDocComment($tokens, $index);
            if (null === $docCommentIndex) {
                continue;
            }
            $returnTypeAnnotation = $this->getAnnotationsFromDocComment('return', $tokens, $docCommentIndex);
            if (1 !== \count($returnTypeAnnotation)) {
                continue;
            }
            $typeInfo = $this->getCommonTypeFromAnnotation(\current($returnTypeAnnotation), \true);
            if (null === $typeInfo) {
                continue;
            }
            list($returnType, $isNullable) = $typeInfo;
            $startIndex = $tokens->getNextTokenOfKind($index, ['{', ';']);
            if ($this->hasReturnTypeHint($tokens, $startIndex)) {
                continue;
            }
            if (!$this->isValidSyntax(\sprintf('<?php function f():%s {}', $returnType))) {
                continue;
            }
            $endFuncIndex = $tokens->getPrevTokenOfKind($startIndex, [')']);
            $tokens->insertAt($endFuncIndex + 1, \array_merge([new \PhpCsFixer\Tokenizer\Token([\PhpCsFixer\Tokenizer\CT::T_TYPE_COLON, ':']), new \PhpCsFixer\Tokenizer\Token([\T_WHITESPACE, ' '])], $this->createTypeDeclarationTokens($returnType, $isNullable)));
        }
    }
    /**
     * Determine whether the function already has a return type hint.
     *
     * @param int $index The index of the end of the function definition line, EG at { or ;
     * @return bool
     */
    private function hasReturnTypeHint(\PhpCsFixer\Tokenizer\Tokens $tokens, $index)
    {
        $endFuncIndex = $tokens->getPrevTokenOfKind($index, [')']);
        $nextIndex = $tokens->getNextMeaningfulToken($endFuncIndex);
        return $tokens[$nextIndex]->isGivenKind(\PhpCsFixer\Tokenizer\CT::T_TYPE_COLON);
    }
}
