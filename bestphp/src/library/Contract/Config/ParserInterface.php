<?php

namespace Best\Contract\Config;


interface ParserInterface
{
    /**
     * parse configuration file
     *
     * @param string $filename
     * @return array
     */
    public static function parse($filename): array;
}