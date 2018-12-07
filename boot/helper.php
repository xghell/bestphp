<?php
if (!function_exists('path_format')) {
    function path_format($path)
    {
        return rtrim($path, '/');
    }
}
