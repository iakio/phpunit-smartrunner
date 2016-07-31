<?php

/*
 * This file is part of phpunit-smartrunner.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace iakio\phpunit\smartrunner;

use iakio\phpunit\smartrunner\drivers\Driver;
use PHPUnit_Framework_BaseTestListener;
use PHPUnit_Framework_Test;
use PHPUnit_Framework_TestSuite;
use ReflectionClass;
use iakio\phpunit\smartrunner\drivers\XdebugDriver;
use iakio\phpunit\smartrunner\drivers\PhpdbgDriver;

class DependencyListener extends PHPUnit_Framework_BaseTestListener
{
    /** @var string */
    private $root;

    /** @var FileSystem */
    private $fs;

    /** @var Cache */
    private $cache;

    /** @var Config */
    private $config;

    /** @var Driver */
    private $driver;

    public function __construct()
    {
        $this->root = getcwd();
        $this->fs = new FileSystem($this->root);
        $this->cache = new Cache($this->fs);
        $this->cache->loadCache();
        $this->config = $this->fs->loadConfig();
        if (function_exists('phpdbg_start_oplog')) {
            $this->driver = new PhpdbgDriver();
        } else {
            $this->driver = new XdebugDriver();
        }
    }

    public function startTest(PHPUnit_Framework_Test $test)
    {
        $this->driver->startCodeCoverage();
    }

    private function isIgnoredInternal($file)
    {
        if (strpos($file, 'phar://') === 0) {
            return true;
        }
        $relative_path = $this->fs->relativePath($file);

        return $this->config->isIgnored($relative_path);
    }

    private function isIgnored($file)
    {
        static $memo = [];

        if (array_key_exists($file, $memo)) {
            return $memo[$file];
        }
        return $memo[$file] = $this->isIgnoredInternal($file);
    }

    public function endTest(PHPUnit_Framework_Test $test, $time)
    {
        $class = new ReflectionClass($test);
        $testFile = $class->getFileName();
        $executedFiles = array_keys($this->driver->stopCodeCoverage());
        foreach ($executedFiles as $executedFile) {
            if (!$this->isIgnored($executedFile)) {
                $this->cache->add($executedFile, $testFile);
            }
        }
    }

    public function endTestSuite(PHPUnit_Framework_TestSuite $suite)
    {
        $this->cache->saveCache();
    }
}
