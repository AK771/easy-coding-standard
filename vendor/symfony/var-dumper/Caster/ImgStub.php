<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20210508\Symfony\Component\VarDumper\Caster;

/**
 * @author Grégoire Pineau <lyrixx@lyrixx.info>
 */
class ImgStub extends \ECSPrefix20210508\Symfony\Component\VarDumper\Caster\ConstStub
{
    /**
     * @param string $data
     */
    public function __construct($data, string $contentType, string $size = '')
    {
        if (\is_object($data)) {
            $data = (string) $data;
        }
        $this->value = '';
        $this->attr['img-data'] = $data;
        $this->attr['img-size'] = $size;
        $this->attr['content-type'] = $contentType;
    }
}