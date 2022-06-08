<?php

declare (strict_types=1);
namespace ECSPrefix20220608\Symplify\EasyParallel\Reflection;

use ReflectionClass;
use ReflectionMethod;
use ECSPrefix20220608\Symfony\Component\Console\Command\Command;
use ECSPrefix20220608\Symplify\EasyParallel\Exception\ParallelShouldNotHappenException;
final class CommandFromReflectionFactory
{
    /**
     * @param class-string<Command> $className
     */
    public function create(string $className) : Command
    {
        $commandReflectionClass = new ReflectionClass($className);
        $command = $commandReflectionClass->newInstanceWithoutConstructor();
        $parentClassReflection = $commandReflectionClass->getParentClass();
        if (!$parentClassReflection instanceof ReflectionClass) {
            throw new ParallelShouldNotHappenException();
        }
        $parentConstructorReflectionMethod = $parentClassReflection->getConstructor();
        if (!$parentConstructorReflectionMethod instanceof ReflectionMethod) {
            throw new ParallelShouldNotHappenException();
        }
        $parentConstructorReflectionMethod->invoke($command);
        return $command;
    }
}
