<?php
/**
 * Project: wwwroot
 * Author:
 * Date: 11/16/18
 * Time: 7:44 PM
 */

namespace Best\Driver\Config;

use InvalidArgumentException;
use Best\Contract\Config\ParserInterface;

class Json implements ParserInterface
{
    public static function parse($filename): array
    {
        if (!is_file($filename)) {
            throw new InvalidArgumentException('File not exist!');
        }
        $content = file_get_contents($filename);
        return json_decode($content);
    }
}