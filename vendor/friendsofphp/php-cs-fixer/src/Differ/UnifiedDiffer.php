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
namespace PhpCsFixer\Differ;

use PhpCsFixer\Diff\Differ;
use PhpCsFixer\Diff\Output\StrictUnifiedDiffOutputBuilder;
use PhpCsFixer\Preg;
/**
 * @author SpacePossum
 */
final class UnifiedDiffer implements \PhpCsFixer\Differ\DifferInterface
{
    /**
     * {@inheritdoc}
     * @param \SplFileInfo|null $file
     * @param string $old
     */
    public function diff($old, string $new, $file = null) : string
    {
        if (\is_object($old)) {
            $old = (string) $old;
        }
        if (null === $file) {
            $options = ['fromFile' => 'Original', 'toFile' => 'New'];
        } else {
            $filePath = $file->getRealPath();
            if (1 === \PhpCsFixer\Preg::match('/\\s/', $filePath)) {
                $filePath = '"' . $filePath . '"';
            }
            $options = ['fromFile' => $filePath, 'toFile' => $filePath];
        }
        $differ = new \PhpCsFixer\Diff\Differ(new \PhpCsFixer\Diff\Output\StrictUnifiedDiffOutputBuilder($options));
        return $differ->diff($old, $new);
    }
}