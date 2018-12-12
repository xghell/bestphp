<?php

namespace Best\Filesystem\Parse;


use InvalidArgumentException;
use Best\Filesystem\Parse\Contract\ParserInterface;

class Ini implements ParserInterface
{
    public static function parse($file): array
    {
        if (!is_file($file)) {
            throw new InvalidArgumentException('File not exist!');
        }
        return parse_ini_file($file, true);
    }
}