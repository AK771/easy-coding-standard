<?php

declare (strict_types=1);
namespace ECSPrefix20220613\Symplify\Skipper\SkipVoter;

use ECSPrefix20220613\Symplify\Skipper\Contract\SkipVoterInterface;
use ECSPrefix20220613\Symplify\Skipper\Matcher\FileInfoMatcher;
use ECSPrefix20220613\Symplify\Skipper\SkipCriteriaResolver\SkippedPathsResolver;
use ECSPrefix20220613\Symplify\SmartFileSystem\SmartFileInfo;
final class PathSkipVoter implements SkipVoterInterface
{
    /**
     * @var \Symplify\Skipper\Matcher\FileInfoMatcher
     */
    private $fileInfoMatcher;
    /**
     * @var \Symplify\Skipper\SkipCriteriaResolver\SkippedPathsResolver
     */
    private $skippedPathsResolver;
    public function __construct(FileInfoMatcher $fileInfoMatcher, SkippedPathsResolver $skippedPathsResolver)
    {
        $this->fileInfoMatcher = $fileInfoMatcher;
        $this->skippedPathsResolver = $skippedPathsResolver;
    }
    /**
     * @param string|object $element
     */
    public function match($element) : bool
    {
        return \true;
    }
    /**
     * @param string|object $element
     */
    public function shouldSkip($element, SmartFileInfo $smartFileInfo) : bool
    {
        $skippedPaths = $this->skippedPathsResolver->resolve();
        return $this->fileInfoMatcher->doesFileInfoMatchPatterns($smartFileInfo, $skippedPaths);
    }
}
