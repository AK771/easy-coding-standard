<?php

namespace Symplify\SymplifyKernel\Strings;

use ECSPrefix20210508\Nette\Utils\Strings;
use Symplify\SymplifyKernel\Exception\HttpKernel\TooGenericKernelClassException;
use Symplify\SymplifyKernel\HttpKernel\AbstractSymplifyKernel;
final class KernelUniqueHasher
{
    /**
     * @var StringsConverter
     */
    private $stringsConverter;
    public function __construct()
    {
        $this->stringsConverter = new \Symplify\SymplifyKernel\Strings\StringsConverter();
    }
    /**
     * @param string $kernelClass
     */
    public function hashKernelClass($kernelClass) : string
    {
        if (\is_object($kernelClass)) {
            $kernelClass = (string) $kernelClass;
        }
        $this->ensureIsNotGenericKernelClass($kernelClass);
        $shortClassName = (string) \ECSPrefix20210508\Nette\Utils\Strings::after($kernelClass, '\\', -1);
        return $this->stringsConverter->camelCaseToGlue($shortClassName, '_');
    }
    /**
     * @return void
     * @param string $kernelClass
     */
    private function ensureIsNotGenericKernelClass($kernelClass)
    {
        if (\is_object($kernelClass)) {
            $kernelClass = (string) $kernelClass;
        }
        if ($kernelClass !== \Symplify\SymplifyKernel\HttpKernel\AbstractSymplifyKernel::class) {
            return;
        }
        $message = \sprintf('Instead of "%s", provide final Kernel class', \Symplify\SymplifyKernel\HttpKernel\AbstractSymplifyKernel::class);
        throw new \Symplify\SymplifyKernel\Exception\HttpKernel\TooGenericKernelClassException($message);
    }
}