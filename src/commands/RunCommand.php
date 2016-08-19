<?php

/*
 * This file is part of phpunit-smartrunner.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace iakio\phpunit\smartrunner\commands;

use iakio\phpunit\smartrunner\Cache;
use iakio\phpunit\smartrunner\FileSystem;
use iakio\phpunit\smartrunner\Phpunit;
use iakio\phpunit\smartrunner\SmartRunner;

class RunCommand
{
    private $phpunit;

    private $cache;

    private $fs;

    /**
     * RunCommand constructor.
     * @param Phpunit $phpunit
     * @param Cache $cache
     * @param FileSystem $fs
     */
    public function __construct(Phpunit $phpunit, Cache $cache, FileSystem $fs)
    {
        $this->phpunit = $phpunit;
        $this->cache = $cache;
        $this->fs = $fs;
    }

    /**
     * @param array $argv
     */
    public function run(array $argv)
    {
        if (count($argv) === 0) {
            SmartRunner::usage();

            return;
        }
        if (!$this->fs->cacheDirExists()) {
            echo $this->fs->cacheDir()." directory does not exist.\n";
            echo "Run `smartrunner init [phpunit.xml]` first.\n";

            return;
        }
        $file_name = array_shift($argv);
        $hits = $this->cache->get($file_name);
        if (count($hits) === 0 && self::isTestable($file_name)) {
            $hits = [$file_name];
        }
        $hits = array_filter($hits, function ($hit) use ($file_name) {
            if ($this->fs->fileExists($hit)) {
                return true;
            } else {
                $this->cache->remove($file_name, $hit);
                $this->fs->saveCache($this->cache);
                return false;
            }
        });
        if (count($hits) > 0) {
            $this->fs->saveSuiteFile(array_values($hits));
            $this->phpunit->exec('-c .smartrunner/phpunit.xml.dist SmartrunnerSuite .smartrunner/suite.php');
        }
    }

    public static function isTestable($arg_file)
    {
        return preg_match('/.*Test\.php$/', $arg_file);
    }
}
