<?php
namespace iakio\phpunit\smartrunner;

class Utils
{

    public static function normalizePath($root, $path)
    {
        return str_replace(realpath($root) . DIRECTORY_SEPARATOR, "", realpath($path));
    }

} 
