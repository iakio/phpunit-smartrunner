<?php
namespace iakio\phpunit\smartrunner;

class Utils
{

    public static function isTestable($arg_file)
    {
        return preg_match('/.*Test\.php$/', $arg_file);
    }

} 
