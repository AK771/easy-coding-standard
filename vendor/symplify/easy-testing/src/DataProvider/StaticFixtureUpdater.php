<?php

declare (strict_types=1);
namespace ECSPrefix20210818\Symplify\EasyTesting\DataProvider;

use ECSPrefix20210818\Symplify\SmartFileSystem\SmartFileInfo;
use ECSPrefix20210818\Symplify\SmartFileSystem\SmartFileSystem;
final class StaticFixtureUpdater
{
    public static function updateFixtureContent(\ECSPrefix20210818\Symplify\SmartFileSystem\SmartFileInfo $originalFileInfo, string $changedContent, \ECSPrefix20210818\Symplify\SmartFileSystem\SmartFileInfo $fixtureFileInfo) : void
    {
        if (!\getenv('UPDATE_TESTS') && !\getenv('UT')) {
            return;
        }
        $newOriginalContent = self::resolveNewFixtureContent($originalFileInfo, $changedContent);
        self::getSmartFileSystem()->dumpFile($fixtureFileInfo->getRealPath(), $newOriginalContent);
    }
    public static function updateExpectedFixtureContent(string $newOriginalContent, \ECSPrefix20210818\Symplify\SmartFileSystem\SmartFileInfo $expectedFixtureFileInfo) : void
    {
        if (!\getenv('UPDATE_TESTS') && !\getenv('UT')) {
            return;
        }
        self::getSmartFileSystem()->dumpFile($expectedFixtureFileInfo->getRealPath(), $newOriginalContent);
    }
    private static function getSmartFileSystem() : \ECSPrefix20210818\Symplify\SmartFileSystem\SmartFileSystem
    {
        return new \ECSPrefix20210818\Symplify\SmartFileSystem\SmartFileSystem();
    }
    private static function resolveNewFixtureContent(\ECSPrefix20210818\Symplify\SmartFileSystem\SmartFileInfo $originalFileInfo, string $changedContent) : string
    {
        if ($originalFileInfo->getContents() === $changedContent) {
            return $originalFileInfo->getContents();
        }
        return $originalFileInfo->getContents() . '-----' . \PHP_EOL . $changedContent;
    }
}
