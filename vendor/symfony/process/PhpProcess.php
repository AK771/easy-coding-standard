<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20210508\Symfony\Component\Process;

use ECSPrefix20210508\Symfony\Component\Process\Exception\LogicException;
use ECSPrefix20210508\Symfony\Component\Process\Exception\RuntimeException;
/**
 * PhpProcess runs a PHP script in an independent process.
 *
 *     $p = new PhpProcess('<?php echo "foo"; ?>');
 *     $p->run();
 *     print $p->getOutput()."\n";
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class PhpProcess extends \ECSPrefix20210508\Symfony\Component\Process\Process
{
    /**
     * @param string      $script  The PHP script to run (as a string)
     * @param string|null $cwd     The working directory or null to use the working dir of the current PHP process
     * @param array|null  $env     The environment variables or null to use the same environment as the current PHP process
     * @param int         $timeout The timeout in seconds
     * @param array|null  $php     Path to the PHP binary to use with any additional arguments
     */
    public function __construct($script, string $cwd = null, array $env = null, int $timeout = 60, array $php = null)
    {
        if (\is_object($script)) {
            $script = (string) $script;
        }
        if (null === $php) {
            $executableFinder = new \ECSPrefix20210508\Symfony\Component\Process\PhpExecutableFinder();
            $php = $executableFinder->find(\false);
            $php = \false === $php ? null : \array_merge([$php], $executableFinder->findArguments());
        }
        if ('phpdbg' === \PHP_SAPI) {
            $file = \tempnam(\sys_get_temp_dir(), 'dbg');
            \file_put_contents($file, $script);
            \register_shutdown_function('unlink', $file);
            $php[] = $file;
            $script = null;
        }
        parent::__construct($php, $cwd, $env, $script, $timeout);
    }
    /**
     * {@inheritdoc}
     * @param float|null $timeout
     * @param string $command
     */
    public static function fromShellCommandline($command, string $cwd = null, array $env = null, $input = null, $timeout = 60)
    {
        if (\is_object($command)) {
            $command = (string) $command;
        }
        throw new \ECSPrefix20210508\Symfony\Component\Process\Exception\LogicException(\sprintf('The "%s()" method cannot be called when using "%s".', __METHOD__, self::class));
    }
    /**
     * {@inheritdoc}
     */
    public function start(callable $callback = null, array $env = [])
    {
        if (null === $this->getCommandLine()) {
            throw new \ECSPrefix20210508\Symfony\Component\Process\Exception\RuntimeException('Unable to find the PHP executable.');
        }
        parent::start($callback, $env);
    }
}