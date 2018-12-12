<?php
/**
 * Project: wwwroot
 * Author:
 * Date: 11/16/18
 * Time: 7:44 PM
 */

namespace Best\Filesystem\Parse;

use InvalidArgumentException;
use Best\Filesystem\Parse\Contract\ParserInterface;

class Json implements ParserInterface
{
    public static function parse($file): array
    {
        if (!is_file($file)) {
            throw new InvalidArgumentException('File not exist!');
        }
        $content = file_get_contents($file);
        return json_decode($content, true);
    }
}