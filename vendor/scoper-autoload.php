<?php

// scoper-autoload.php @generated by PhpScoper

$loader = require_once __DIR__.'/autoload.php';

// Aliases for the whitelisted classes. For more information see:
// https://github.com/humbug/php-scoper/blob/master/README.md#class-whitelisting
if (!class_exists('AutoloadIncluder', false) && !interface_exists('AutoloadIncluder', false) && !trait_exists('AutoloadIncluder', false)) {
    spl_autoload_call('ECSPrefix20210805\AutoloadIncluder');
}
if (!class_exists('ComposerAutoloaderInita469333b2eacde7e42a657ce7dacaa0a', false) && !interface_exists('ComposerAutoloaderInita469333b2eacde7e42a657ce7dacaa0a', false) && !trait_exists('ComposerAutoloaderInita469333b2eacde7e42a657ce7dacaa0a', false)) {
    spl_autoload_call('ECSPrefix20210805\ComposerAutoloaderInita469333b2eacde7e42a657ce7dacaa0a');
}
if (!class_exists('Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator', false) && !interface_exists('Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator', false) && !trait_exists('Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator', false)) {
    spl_autoload_call('ECSPrefix20210805\Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator');
}
if (!class_exists('Normalizer', false) && !interface_exists('Normalizer', false) && !trait_exists('Normalizer', false)) {
    spl_autoload_call('ECSPrefix20210805\Normalizer');
}
if (!class_exists('JsonException', false) && !interface_exists('JsonException', false) && !trait_exists('JsonException', false)) {
    spl_autoload_call('ECSPrefix20210805\JsonException');
}
if (!class_exists('Attribute', false) && !interface_exists('Attribute', false) && !trait_exists('Attribute', false)) {
    spl_autoload_call('ECSPrefix20210805\Attribute');
}
if (!class_exists('Stringable', false) && !interface_exists('Stringable', false) && !trait_exists('Stringable', false)) {
    spl_autoload_call('ECSPrefix20210805\Stringable');
}
if (!class_exists('UnhandledMatchError', false) && !interface_exists('UnhandledMatchError', false) && !trait_exists('UnhandledMatchError', false)) {
    spl_autoload_call('ECSPrefix20210805\UnhandledMatchError');
}
if (!class_exists('ValueError', false) && !interface_exists('ValueError', false) && !trait_exists('ValueError', false)) {
    spl_autoload_call('ECSPrefix20210805\ValueError');
}
if (!class_exists('ReturnTypeWillChange', false) && !interface_exists('ReturnTypeWillChange', false) && !trait_exists('ReturnTypeWillChange', false)) {
    spl_autoload_call('ECSPrefix20210805\ReturnTypeWillChange');
}
if (!class_exists('Symplify\SmartFileSystem\SmartFileInfo', false) && !interface_exists('Symplify\SmartFileSystem\SmartFileInfo', false) && !trait_exists('Symplify\SmartFileSystem\SmartFileInfo', false)) {
    spl_autoload_call('ECSPrefix20210805\Symplify\SmartFileSystem\SmartFileInfo');
}

// Functions whitelisting. For more information see:
// https://github.com/humbug/php-scoper/blob/master/README.md#functions-whitelisting
if (!function_exists('composerRequirea469333b2eacde7e42a657ce7dacaa0a')) {
    function composerRequirea469333b2eacde7e42a657ce7dacaa0a() {
        return \ECSPrefix20210805\composerRequirea469333b2eacde7e42a657ce7dacaa0a(...func_get_args());
    }
}
if (!function_exists('sample')) {
    function sample() {
        return \ECSPrefix20210805\sample(...func_get_args());
    }
}
if (!function_exists('foo')) {
    function foo() {
        return \ECSPrefix20210805\foo(...func_get_args());
    }
}
if (!function_exists('bar')) {
    function bar() {
        return \ECSPrefix20210805\bar(...func_get_args());
    }
}
if (!function_exists('baz')) {
    function baz() {
        return \ECSPrefix20210805\baz(...func_get_args());
    }
}
if (!function_exists('xyz')) {
    function xyz() {
        return \ECSPrefix20210805\xyz(...func_get_args());
    }
}
if (!function_exists('uv_signal_init')) {
    function uv_signal_init() {
        return \ECSPrefix20210805\uv_signal_init(...func_get_args());
    }
}
if (!function_exists('uv_signal_start')) {
    function uv_signal_start() {
        return \ECSPrefix20210805\uv_signal_start(...func_get_args());
    }
}
if (!function_exists('uv_poll_init_socket')) {
    function uv_poll_init_socket() {
        return \ECSPrefix20210805\uv_poll_init_socket(...func_get_args());
    }
}
if (!function_exists('printPHPCodeSnifferTestOutput')) {
    function printPHPCodeSnifferTestOutput() {
        return \ECSPrefix20210805\printPHPCodeSnifferTestOutput(...func_get_args());
    }
}
if (!function_exists('setproctitle')) {
    function setproctitle() {
        return \ECSPrefix20210805\setproctitle(...func_get_args());
    }
}
if (!function_exists('array_is_list')) {
    function array_is_list() {
        return \ECSPrefix20210805\array_is_list(...func_get_args());
    }
}
if (!function_exists('enum_exists')) {
    function enum_exists() {
        return \ECSPrefix20210805\enum_exists(...func_get_args());
    }
}
if (!function_exists('includeIfExists')) {
    function includeIfExists() {
        return \ECSPrefix20210805\includeIfExists(...func_get_args());
    }
}
if (!function_exists('dump')) {
    function dump() {
        return \ECSPrefix20210805\dump(...func_get_args());
    }
}
if (!function_exists('dd')) {
    function dd() {
        return \ECSPrefix20210805\dd(...func_get_args());
    }
}

return $loader;
