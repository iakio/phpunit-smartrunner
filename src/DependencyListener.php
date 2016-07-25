<?php

/*
 * This file is part of phpunit-smartrunner.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace iakio\phpunit\smartrunner;

use PHPUnit_Framework_Test;
use PHPUnit_Framework_TestSuite;
use ReflectionClass;

class DependencyListener extends \PHPUnit_Framework_BaseTestListener
{
    /** @var string */
    private $root;

    /** @var FileSystem */
    private $fs;

    /** @var Cache */
    private $cache;

    /** @var array */
    private $config;

    public function __construct()
    {
        $this->root = getcwd();
        $this->fs = new FileSystem($this->root);
        $this->cache = new Cache($this->fs);
        $this->cache->loadCache();
        $this->config = $this->fs->loadConfig();
    }

    public function startTest(PHPUnit_Framework_Test $test)
    {
        xdebug_start_code_coverage();
    }

    private function isIgnoredInternal($file)
    {
        if (strpos($file, 'phar://') === 0) {
            return true;
        }
        $relative_path = $this->fs->relativePath($file);
        foreach ($this->config['cacheignores'] as $pattern) {
            if (preg_match('#'.$pattern.'#', $relative_path)) {
                return true;
            }
        }

        return false;
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
        $executedFiles = array_keys(xdebug_get_code_coverage());
        foreach ($executedFiles as $executedFile) {
            if (!$this->isIgnored($executedFile)) {
                $this->cache->add($executedFile, $testFile);
            }
        }
        xdebug_stop_code_coverage();
    }

    public function endTestSuite(PHPUnit_Framework_TestSuite $suite)
    {
        $this->cache->saveCache();
    }
}
