<?php

declare (strict_types=1);
namespace ECSPrefix202207\Symplify\Skipper\Contract;

use ECSPrefix202207\Symplify\SmartFileSystem\SmartFileInfo;
interface SkipVoterInterface
{
    /**
     * @param string|object $element
     */
    public function match($element) : bool;
    /**
     * @param string|object $element
     */
    public function shouldSkip($element, SmartFileInfo $smartFileInfo) : bool;
}
