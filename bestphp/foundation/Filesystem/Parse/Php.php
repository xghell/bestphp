<?php

namespace Best\Filesystem\Parse;


use InvalidArgumentException;
use Best\Filesystem\Parse\Contract\ParserInterface;

class Php implements ParserInterface
{
    public static function parse($file): array
    {
        if (!is_file($file)) {
            throw new InvalidArgumentException("File($file) not exist!");
        }
        return include $file;
    }
}