<?php

namespace Best\Driver\Config;


use InvalidArgumentException;
use Best\Contract\Config\ParserInterface;

class Ini implements ParserInterface
{
    public static function parse($filename): array
    {
        if (!is_file($filename)) {
            throw new InvalidArgumentException('File not exist!');
        }
        return parse_ini_file($filename, true);
    }
}