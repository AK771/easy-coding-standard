<?php

namespace ECSPrefix20210508\Nette\Neon;

/**
 * Converts value to NEON format.
 * @internal
 */
final class Encoder
{
    const BLOCK = 1;
    /**
     * Returns the NEON representation of a value.
     * @param int $flags
     * @return string
     */
    public function encode($var, $flags = 0)
    {
        if ($var instanceof \DateTimeInterface) {
            return $var->format('Y-m-d H:i:s O');
        } elseif ($var instanceof \ECSPrefix20210508\Nette\Neon\Entity) {
            if ($var->value === \ECSPrefix20210508\Nette\Neon\Neon::CHAIN) {
                return \implode('', \array_map([$this, 'encode'], $var->attributes));
            }
            return $this->encode($var->value) . '(' . (\is_array($var->attributes) ? \substr($this->encode($var->attributes), 1, -1) : '') . ')';
        }
        if (\is_object($var)) {
            $obj = $var;
            $var = [];
            foreach ($obj as $k => $v) {
                $var[$k] = $v;
            }
        }
        if (\is_array($var)) {
            $isList = !$var || \array_keys($var) === \range(0, \count($var) - 1);
            $s = '';
            if ($flags & self::BLOCK) {
                if (\count($var) === 0) {
                    return '[]';
                }
                foreach ($var as $k => $v) {
                    $v = $this->encode($v, self::BLOCK);
                    $s .= ($isList ? '-' : $this->encode($k) . ':') . (\strpos($v, "\n") === \false ? ' ' . $v . "\n" : "\n" . \preg_replace('#^(?=.)#m', "\t", $v) . (\substr($v, -2, 1) === "\n" ? '' : "\n"));
                }
                return $s;
            } else {
                foreach ($var as $k => $v) {
                    $s .= ($isList ? '' : $this->encode($k) . ': ') . $this->encode($v) . ', ';
                }
                return ($isList ? '[' : '{') . \substr($s, 0, -2) . ($isList ? ']' : '}');
            }
        } elseif (\is_string($var)) {
            if (!\preg_match('~[\\x00-\\x1F]|^[+-.]?\\d|^(true|false|yes|no|on|off|null)$~Di', $var) && \preg_match('~^' . \ECSPrefix20210508\Nette\Neon\Decoder::PATTERNS[1] . '$~Dx', $var)) {
                return $var;
            }
            $res = \json_encode($var, \JSON_UNESCAPED_UNICODE | \JSON_UNESCAPED_SLASHES);
            if ($res === \false) {
                throw new \ECSPrefix20210508\Nette\Neon\Exception('Invalid UTF-8 sequence: ' . $var);
            }
            if (\strpos($var, "\n") !== \false) {
                $res = \preg_replace_callback('#[^\\\\]|\\\\(.)#s', function ($m) {
                    return isset(['n' => "\n\t", 't' => "\t", '"' => '"'][isset($m[1]) ? $m[1] : '']) ? ['n' => "\n\t", 't' => "\t", '"' => '"'][isset($m[1]) ? $m[1] : ''] : $m[0];
                }, $res);
                $res = '"""' . "\n\t" . \substr($res, 1, -1) . "\n" . '"""';
            }
            return $res;
        } elseif (\is_float($var)) {
            $var = \json_encode($var);
            return \strpos($var, '.') === \false ? $var . '.0' : $var;
        } else {
            return \json_encode($var);
        }
    }
}