<?php

namespace Best\Driver\Config;


use InvalidArgumentException;
use Best\Contract\Config\ParserInterface;

class Php implements ParserInterface
{
    public static function parse($filename): array
    {
        if (!is_file($filename)) {
            throw new InvalidArgumentException("File($filename) not exist!");
        }
        return include $filename;
    }
}