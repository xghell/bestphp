<?php

namespace Best\Filesystem\Parse\Contract;


interface ParserInterface
{
    /**
     * parse configuration file
     *
     * @param string $file
     * @return array
     */
    public static function parse($file): array;
}