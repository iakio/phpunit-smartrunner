<?php

namespace iakio\phpunit\smartrunner\commands;

use iakio\phpunit\smartrunner\Phpunit;
use iakio\phpunit\smartrunner\Cache;
use iakio\phpunit\smartrunner\FileSystem;

class RunCommand
{
    private $phpunit;

    private $cache;

    private $fs;

    public function __construct(Phpunit $phpunit, Cache $cache, FileSystem $fs)
    {
        $this->phpunit = $phpunit;
        $this->cache = $cache;
        $this->fs = $fs;
    }

    public function run($file_name)
    {
        $hits = $this->cache->get($file_name);
        if (count($hits) === 0 && self::isTestable($file_name)) {
            $hits = [$file_name];
        }
        if (count($hits) > 0) {
            $this->fs->saveSuiteFile($hits);
            $this->phpunit->exec('-c .smartrunner/phpunit.xml.dist SmartrunnerSuite .smartrunner/suite.php');
        }
    }

    public static function isTestable($arg_file)
    {
        return preg_match('/.*Test\.php$/', $arg_file);
    }
}
