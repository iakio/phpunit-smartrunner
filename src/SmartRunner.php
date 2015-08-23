<?php

namespace iakio\phpunit\smartrunner;

class SmartRunner
{
    public static function run(array $argv)
    {
        if (count($argv) > 1) {
            $fs = new FileSystem(getcwd());
            $cache = new Cache($fs);
            $cache->loadCache();
            $arg_file = $fs->normalizePath($argv[1]);
            $hits = $cache->get($arg_file);
            if (count($hits) === 0 && self::isTestable($arg_file)) {
                $hits = [$arg_file];
            }
            if (count($hits) > 0) {
                $fs->saveSuiteFile($hits);
                system(implode(DIRECTORY_SEPARATOR, ["vendor", "bin", "phpunit"]) . " SmartrunnerSuite .smartrunner/suite.php");
            }
        }
    }

    public static function isTestable($arg_file)
    {
        return preg_match('/.*Test\.php$/', $arg_file);
    }
}
