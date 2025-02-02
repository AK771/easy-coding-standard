<?php

declare (strict_types=1);
namespace ECSPrefix202208;

/*
 * This file is part of Evenement.
 *
 * (c) Igor Wiedler <igor@wiedler.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
const ITERATIONS = 10000000;
use ECSPrefix202208\Evenement\EventEmitter;
require __DIR__ . '/../vendor/autoload.php';
$emitter = new EventEmitter();
$emitter->on('event', function ($a, $b, $c) {
});
$start = \microtime(\true);
for ($i = 0; $i < \ECSPrefix202208\ITERATIONS; $i++) {
    $emitter->emit('event', [1, 2, 3]);
}
$time = \microtime(\true) - $start;
echo 'Emitting ', \number_format(\ECSPrefix202208\ITERATIONS), ' events took: ', \number_format($time, 2), 's', \PHP_EOL;
